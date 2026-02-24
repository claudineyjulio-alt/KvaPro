<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegraMaterialModel;
use App\Models\KitModel;

class RegrasMateriais extends BaseController
{
    public function index() // ou listar()
    {
        $model = new \App\Models\RegraMaterialModel();
        $definicaoModel = new \App\Models\DefinicaoRegraMatModel();
        $kitModel = new \App\Models\KitModel();

        // 1. Busca todas as regras (Casca)
        $regras = $model->orderBy('prioridade', 'ASC')->findAll();

        // 2. Busca as condições de CADA regra e anexa ao array
        foreach ($regras as $key => $r) {
            $condicoes = $definicaoModel->where('regra_id', $r['id'])->findAll();
            $regras[$key]['condicoes'] = $condicoes; // Cria um subarray chamado 'condicoes'
        }

        // 3. Monta o Mapa de Kits (se você já tem isso, apenas mantenha)
        $kits = $kitModel->findAll();
        $kitsMap = [];
        foreach ($kits as $k) {
            $kitsMap[$k['id']] = '[' . $k['slug'] . '] ' . $k['nome'];
        }

        $data = [
            'regras'  => $regras,
            'kitsMap' => $kitsMap
        ];

        return view('admin/regras_materiais_lista', $data);
    }

    // public function index()
    // {
    //     $model = new RegraMaterialModel();

    //     // Busca regras e ordena: Primeiro por Concessionária, depois por Categoria, depois por Prioridade (1 no topo)
    //     $regras = $model->orderBy('concessionaria_id', 'ASC')
    //         ->orderBy('tipo_kit', 'ASC')
    //         ->orderBy('prioridade', 'ASC')
    //         ->findAll();

    //     // Precisamos dos nomes dos kits para exibir na lista (fazemos um "join" manual simples)
    //     $kitModel = new KitModel();
    //     $kits = $kitModel->findAll();
    //     $kitsMap = [];
    //     foreach ($kits as $k) {
    //         $kitsMap[$k['id']] = $k['nome'];
    //     }

    //     return view('admin/regras_materiais_lista', [
    //         'regras' => $regras,
    //         'kitsMap' => $kitsMap
    //     ]);
    // }

    public function form($id = null)
    {
        $model = new \App\Models\RegraMaterialModel();
        // Você precisará criar este Model para a tabela nova, se ainda não tiver:
        $definicaoModel = new \App\Models\DefinicaoRegraMatModel();
        $kitModel = new \App\Models\KitModel();

        $db = \Config\Database::connect(); // Inicia a conexão para usarmos Transação

        // --- SALVAR (POST) ---
        if ($this->request->getPost()) {

            $db = \Config\Database::connect();
            $db->transStart(); // Inicia a transação

            try {
                // 1. Salva a "Casca" da Regra
                $dadosRegra = [
                    'concessionaria_id' => $this->request->getPost('concessionaria_id') ?: 1,
                    'nome'              => $this->request->getPost('nome'),
                    'tipo_kit'          => $this->request->getPost('tipo_kit'),
                    'prioridade'        => $this->request->getPost('prioridade'),
                    'descricao'         => $this->request->getPost('descricao'),
                    'kit_id'            => $this->request->getPost('kit_id'),
                    'observacao'        => $this->request->getPost('observacao'),
                ];

                $idPost = $this->request->getPost('id');

                if ($idPost) {
                    // Atualiza
                    $model->update($idPost, $dadosRegra);
                    $regraId = $idPost;

                    // Limpa as condições antigas para inserir as novas
                    $definicaoModel->where('regra_id', $regraId)->delete();
                } else {
                    // Insere nova
                    $regraId = $model->insert($dadosRegra);
                    if (!$regraId) {
                        // Se o model recusar a inserção (ex: validação falhou)
                        $errosModel = implode(', ', $model->errors());
                        throw new \Exception('Erro ao inserir a regra: ' . $errosModel);
                    }
                }

                // 2. Salva as "Condições"
                $variaveis = $this->request->getPost('variaveis') ?? [];
                $condicoes = $this->request->getPost('condicoes') ?? [];
                $valores_min = $this->request->getPost('valores_min') ?? [];
                $valores_max = $this->request->getPost('valores_max') ?? [];

                $condicoesData = [];

                for ($i = 0; $i < count($variaveis); $i++) {
                    if (!empty($variaveis[$i])) {
                        $condicoesData[] = [
                            'regra_id'  => $regraId,
                            'variavel'  => $variaveis[$i],
                            'condicao'  => $condicoes[$i],
                            'valor_min' => $valores_min[$i],
                            'valor_max' => !empty($valores_max[$i]) ? $valores_max[$i] : null,
                        ];
                    }
                }

                if (!empty($condicoesData)) {
                    $definicaoModel->insertBatch($condicoesData);
                }

                $db->transComplete(); // Confirma a transação

                if ($db->transStatus() === FALSE) {
                    $erroDB = $db->error();
                    throw new \Exception('Erro no Banco de Dados: ' . $erroDB['message']);
                }

                // Tudo deu certo, redireciona com sucesso
                return redirect()->to('admin/regras-materiais')->with('sucesso', 'Regra de automação salva com sucesso!');
            } catch (\Exception $e) {
                // Se der qualquer erro, o catch intercepta e envia para o seu Modal de Erro
                return redirect()->back()->withInput()->with('erro', $e->getMessage());
            }
        }


        // --- CARREGAR VIEW (GET) ---
        if ($id) {
            $data['item'] = $model->find($id);
            // Busca as condições no banco para o JS preencher as linhas na tela de edição
            $data['condicoesRegra'] = $definicaoModel->where('regra_id', $id)->findAll();
        } else {
            $data['item'] = null;
            $data['condicoesRegra'] = [];
        }

        // Lista de Kits para o Dropdown
        $data['kits'] = $kitModel->orderBy('nome', 'ASC')->findAll();

        // Matriz de Variáveis para agrupar no Select (optgroup)
        $data['variaveisProjeto'] = [
            'Dados da Obra' => [
                'tipo_obra'           => 'Tipo de Obra (Nova, Aumento, etc)',
                'zona'                => 'Zona (Urbana/Rural)',
                'tipo_ramal'          => 'Tipo de Ramal (Aéreo/Subterrâneo)',
                'localizacao_medidor' => 'Localização do Medidor',
            ],
            'Alimentador Geral (Entrada)' => [
                'tipo_geral'         => 'Tipo Geral (1=Mono, 2=Bi, 3=Tri)',
                'qtd_fase'           => 'Qtd. de Cabos por Fase',
                'entrada_fase'       => 'Seção do Cabo Fase (mm²)',
                'entrada_neutro'     => 'Seção do Cabo Neutro (mm²)',
                'entrada_eletroduto' => 'Diâmetro Eletroduto Entrada',
                'entrada_disjuntor'  => 'Corrente Disjuntor Geral (A)',
            ],
            'Aterramento e Proteção' => [
                'terra_hastes'      => 'Nº de Hastes de Terra',
                'terra_tipo_hastes' => 'Tipo da Haste (Ex: 16x2400mm)',
                'terra_cabo'        => 'Seção Cabo Terra (mm²)',
                'terra_tubo'        => 'Diâmetro Tubo Terra',
                'terra_cx'          => 'Tipo Caixa Inspeção (PVC/Concreto)',
                'dps_tensao'        => 'Tensão do DPS (V)',
                'dps_ka'            => 'Capacidade DPS (kA)',
                'dps_cabo'          => 'Seção Cabo DPS (mm²)',
            ],
            'Medições Individuais (Loop)' => [
                'unidade_classe'    => 'Classe (Residencial, Comercial)',
                'unidade_categoria' => 'Categoria (M, B, T)',
                'unidade_fase'      => 'Seção Cabo Fase Medidor (mm²)',
                'unidade_disjuntor' => 'Disjuntor do Medidor (A)',
            ],
            'Variáveis Virtuais (Cálculos)' => [
                'padrao'          => 'Regra Padrão (Sempre executa)',
                'total_medidores' => 'Total de Medidores (Qtd. Unidades)',
            ]
        ];

        return view('admin/regras_materiais_form', $data);
    }

    // public function form($id = null)
    // {
    //     $model = new RegraMaterialModel();
    //     $kitModel = new KitModel();

    //     // --- SALVAR (POST) ---
    //     if ($this->request->getPost()) {

    //         $dados = [
    //             'concessionaria_id' => $this->request->getPost('concessionaria_id'), // Por enquanto fixo ou input
    //             'tipo_kit'          => $this->request->getPost('tipo_kit'),
    //             'prioridade'        => $this->request->getPost('prioridade'),
    //             'descricao'         => $this->request->getPost('descricao'),
    //             'variavel'          => $this->request->getPost('variavel'),
    //             'condicao'          => $this->request->getPost('condicao'),
    //             'valor_min'         => $this->request->getPost('valor_min'),
    //             'valor_max'         => $this->request->getPost('valor_max'),
    //             'kit_id'            => $this->request->getPost('kit_id'),
    //             'observacao'        => $this->request->getPost('observacao'),
    //         ];

    //         $idPost = $this->request->getPost('id');
    //         $idFinal = $idPost ? $idPost : $id;

    //         if ($idFinal) {
    //             $model->update($idFinal, $dados);
    //         } else {
    //             $model->insert($dados);
    //         }

    //         return redirect()->to('admin/regras-materiais')->with('sucesso', 'Regra salva com sucesso!');
    //     }

    //     // --- CARREGAR VIEW (GET) ---
    //     $data['item'] = $id ? $model->find($id) : null;

    //     // Lista de Kits para o Dropdown
    //     $data['kits'] = $kitModel->orderBy('nome', 'ASC')->findAll();

    //     // Crie este array no seu Controller e passe para a View
    //     $data['variaveisProjeto'] = [
    //         'Dados da Obra' => [
    //             'tipo_obra'           => 'Tipo de Obra (Nova, Aumento, etc)',
    //             'zona'                => 'Zona (Urbana/Rural)',
    //             'tipo_ramal'          => 'Tipo de Ramal (Aéreo/Subterrâneo)',
    //             'localizacao_medidor' => 'Localização do Medidor',
    //         ],
    //         'Alimentador Geral (Entrada)' => [
    //             'tipo_geral'         => 'Tipo Geral (1=Mono, 2=Bi, 3=Tri)',
    //             'qtd_fase'           => 'Qtd. de Cabos por Fase',
    //             'entrada_fase'       => 'Seção do Cabo Fase (mm²)',
    //             'entrada_neutro'     => 'Seção do Cabo Neutro (mm²)',
    //             'entrada_eletroduto' => 'Diâmetro Eletroduto Entrada',
    //             'entrada_disjuntor'  => 'Corrente Disjuntor Geral (A)',
    //         ],
    //         'Aterramento e Proteção' => [
    //             'terra_hastes'      => 'Nº de Hastes de Terra',
    //             'terra_tipo_hastes' => 'Tipo da Haste (Ex: 16x2400mm)',
    //             'terra_cabo'        => 'Seção Cabo Terra (mm²)',
    //             'terra_tubo'        => 'Diâmetro Tubo Terra',
    //             'terra_cx'          => 'Tipo Caixa Inspeção (PVC/Concreto)',
    //             'dps_tensao'        => 'Tensão do DPS (V)',
    //             'dps_ka'            => 'Capacidade DPS (kA)',
    //             'dps_cabo'          => 'Seção Cabo DPS (mm²)',
    //         ],
    //         'Medições Individuais (Loop)' => [
    //             'unidade_classe'    => 'Classe (Residencial, Comercial)',
    //             'unidade_categoria' => 'Categoria (M, B, T)',
    //             'unidade_fase'      => 'Seção Cabo Fase Medidor (mm²)',
    //             'unidade_disjuntor' => 'Disjuntor do Medidor (A)',
    //         ],
    //         'Variáveis Virtuais (Cálculos)' => [
    //             'padrao'          => 'Regra Padrão (Sempre executa)',
    //             'total_medidores' => 'Total de Medidores (Qtd. Unidades)',
    //         ]
    //     ];

    //     return view('admin/regras_materiais_form', $data);
    // }

    public function excluir($id)
    {
        $model = new RegraMaterialModel();
        $model->delete($id);
        return redirect()->to('admin/regras-materiais')->with('sucesso', 'Regra excluída.');
    }
}
