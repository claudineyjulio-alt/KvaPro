<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegraMaterialModel;
use App\Models\KitModel;

class RegrasMateriais extends BaseController
{
    public function index()
    {
        $model = new RegraMaterialModel();

        // Busca regras e ordena: Primeiro por Concessionária, depois por Categoria, depois por Prioridade (1 no topo)
        $regras = $model->orderBy('concessionaria_id', 'ASC')
            ->orderBy('tipo_kit', 'ASC')
            ->orderBy('prioridade', 'ASC')
            ->findAll();

        // Precisamos dos nomes dos kits para exibir na lista (fazemos um "join" manual simples)
        $kitModel = new KitModel();
        $kits = $kitModel->findAll();
        $kitsMap = [];
        foreach ($kits as $k) {
            $kitsMap[$k['id']] = $k['nome'];
        }

        return view('admin/regras_materiais_lista', [
            'regras' => $regras,
            'kitsMap' => $kitsMap
        ]);
    }

    public function form($id = null)
    {
        $model = new RegraMaterialModel();
        $kitModel = new KitModel();

        // --- SALVAR (POST) ---
        if ($this->request->getPost()) {

            $dados = [
                'concessionaria_id' => $this->request->getPost('concessionaria_id'), // Por enquanto fixo ou input
                'tipo_kit'          => $this->request->getPost('tipo_kit'),
                'prioridade'        => $this->request->getPost('prioridade'),
                'descricao'         => $this->request->getPost('descricao'),
                'variavel'          => $this->request->getPost('variavel'),
                'condicao'          => $this->request->getPost('condicao'),
                'valor_min'         => $this->request->getPost('valor_min'),
                'valor_max'         => $this->request->getPost('valor_max'),
                'kit_id'            => $this->request->getPost('kit_id'),
                'observacao'        => $this->request->getPost('observacao'),
            ];

            $idPost = $this->request->getPost('id');
            $idFinal = $idPost ? $idPost : $id;

            if ($idFinal) {
                $model->update($idFinal, $dados);
            } else {
                $model->insert($dados);
            }

            return redirect()->to('admin/regras-materiais')->with('sucesso', 'Regra salva com sucesso!');
        }

        // --- CARREGAR VIEW (GET) ---
        $data['item'] = $id ? $model->find($id) : null;

        // Lista de Kits para o Dropdown
        $data['kits'] = $kitModel->orderBy('nome', 'ASC')->findAll();

        // Crie este array no seu Controller e passe para a View
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

    public function excluir($id)
    {
        $model = new RegraMaterialModel();
        $model->delete($id);
        return redirect()->to('admin/regras-materiais')->with('sucesso', 'Regra excluída.');
    }
}
