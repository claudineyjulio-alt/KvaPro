<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConcessionariaModel;
use App\Models\TensaoModel;
use App\Models\DimensionamentoModel;

class Projeto extends BaseController
{
    public function novo()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $concessionariaModel = new ConcessionariaModel();
        $tensaoModel = new TensaoModel();

        $data = [
            'concessionarias' => $concessionariaModel->findAll(),
            'tensoes' => $tensaoModel->findAll(),
            'titulo' => 'Novo Projeto'
        ];
        // LÓGICA NOVA: Verifica se veio dados para editar
        $recuperacao = $this->request->getPost('recuperar_projeto');
        if ($recuperacao) {
            // Passa o JSON para a view
            $data['projeto_recuperado'] = $recuperacao;
        }

        return view('projeto_novo', $data); // Ou o nome da sua view de formulário
    }

// public function novo()
//     {
//         if (!session()->get('isLoggedIn')) return redirect()->to('/');

//         $concessionariaModel = new ConcessionariaModel();
//         $tensaoModel = new TensaoModel();

//         $data = [
//             'concessionarias' => $concessionariaModel->findAll(),
//             'tensoes' => $tensaoModel->findAll(),
//             'titulo' => 'Novo Projeto'
//         ];

//         return view('projeto_novo', $data);
//     }

    /**
     * API para retornar os dados de dimensionamento baseado na concessionária e tensão
     * Rota sugerida: $routes->get('projeto/api/dimensionamento', 'Projeto::api_dimensionamento');
     * Parâmetros via GET: concessionaria (ID), tensao (ID)
     */
    public function api_dimensionamento()
    {
        if (!session()->get('isLoggedIn')) return $this->response->setJSON([]);
        $concessionaria = $this->request->getGet('concessionaria');
        $tensao = $this->request->getGet('tensao');
        
        $model = new DimensionamentoModel();
        return $this->response->setJSON($model->buscarPorConfig($concessionaria, $tensao));
    }

    /**
     * Exibe o Diagrama Unifilar SVG na tela
     * Rota sugerida: $routes->get('projetos/diagrama/(:num)', 'Projetos::diagrama/$1');
     */
    public function diagrama()
    {
        // 1. Recebe o JSON enviado pelo formulário (input hidden)
        $jsonPayload = $this->request->getPost('payload_projeto');

        // Se tentar acessar direto pela URL sem dados, redireciona ou mostra erro
        if (!$jsonPayload) {
            return "Erro: Nenhum dado de projeto recebido. Acesse através da tela de processamento.";
        }

        // 2. Decodifica o JSON de volta para Array
        $projeto = json_decode($jsonPayload, true);

        // 3. Mapeia os dados do JSON para o formato que a View 'diagrama_unifilar' espera.
        // NOTA: Ajuste as chaves ($projeto['chave']) conforme o array que você gera no seu processamento.
        
        $entrada = [
            // Tenta pegar com nomes comuns, ou usa um padrão se não achar
            'cabo'       => $projeto['entrada_cabo']       ?? $projeto['cabo_entrada']       ?? '3#35(35)mm²',
            'disjuntor'  => $projeto['entrada_disjuntor']  ?? $projeto['disjuntor_geral']    ?? '100A',
            'eletroduto' => $projeto['entrada_eletroduto'] ?? $projeto['eletroduto_entrada'] ?? 'Ø 2"',
            'fases'      => $projeto['numero_fases']       ?? 3,
            
            // Dados opcionais (DPS/Terra) - Se não existirem no array, ficam null
            'cabo_dps'         => $projeto['dps_cabo']      ?? null,
            'tensao_dps'       => $projeto['dps_tensao']    ?? null,
            'ka_dps'           => $projeto['dps_ka']        ?? null,
            'cabo_terra'       => $projeto['terra_cabo']    ?? null,
            'eletroduto_terra' => $projeto['terra_tubo']    ?? null,
            'hastes'           => $projeto['terra_hastes']  ?? null,
        ];

        // 4. Prepara as medições (Unidades)
        $medicoes = [];
        if (isset($projeto['medicoes']) && is_array($projeto['medicoes'])) {
            foreach ($projeto['medicoes'] as $m) {
                $medicoes[] = [
                    // CORREÇÃO AQUI: Tenta pegar 'placa' (do formulário novo) ou 'nome' (legado)
                    'nome'       => $m['placa'] ?? $m['nome'] ?? 'Sem Nome',
                    
                    // CORREÇÃO AQUI: Tenta pegar 'numero_uc' (do formulário novo) ou 'uc' (legado)
                    'uc_id'      => $m['numero_uc'] ?? $m['uc'] ?? '',
                    
                    'cabo'       => $m['cabo'] ?? '',
                    'disj'       => $m['disjuntor'] ?? '',
                    'fases_especificas' => $m['fases_especificas'] ?? '', // Garante que vai para a view
                    'fases'             => $m['fases'] ?? 1,
                    'eletroduto' => $m['eletroduto'] ?? ''
                ];
            }
        }

        // 5. Carrega a view do diagrama com os dados mapeados
        return view('diagrama_unifilar', [
            'medicoes' => $medicoes,
            'entrada'  => $entrada
        ]);
    }

    public function salvar()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        // 1. Captura os dados da seção Geral e Identificação
        $dados = [
            'titulo_obra'         => $this->request->getPost('titulo_obra'),
            'cliente_nome'        => $this->request->getPost('cliente_nome'),
            'tipo_obra'           => $this->request->getPost('tipo_obra'),
            'cep'                 => $this->request->getPost('cep'),
            'logradouro'          => $this->request->getPost('logradouro'),
            'numero'              => $this->request->getPost('numero'),
            'bairro'              => $this->request->getPost('bairro'),
            'cidade'              => $this->request->getPost('cidade'),
            'uf'                  => $this->request->getPost('uf'),
            'zona'                => $this->request->getPost('zona'),
            'concessionaria_id'   => $this->request->getPost('concessionaria_id'),
            'tensao_id'           => $this->request->getPost('tensao_id'),
            'tipo_ramal'          => $this->request->getPost('tipo_ramal'),
            'localizacao_medidor' => $this->request->getPost('localizacao_medidor'),

            // Campos novos que adicionamos ao formulário
            'entrada_cabo'       => $this->request->getPost('entrada_cabo'),
            'entrada_eletroduto' => $this->request->getPost('entrada_eletroduto'),
            'entrada_disjuntor'  => $this->request->getPost('entrada_disjuntor'),
            'numero_fases'       => $this->request->getPost('numero_fases'),
            
            'dps_tensao'         => $this->request->getPost('dps_tensao'),
            'dps_ka'             => $this->request->getPost('dps_ka'),
            'dps_cabo'           => $this->request->getPost('dps_cabo'),
            
            'terra_cabo'         => $this->request->getPost('terra_cabo'),
            'terra_tubo'         => $this->request->getPost('terra_tubo'),
            'terra_hastes'       => $this->request->getPost('terra_hastes'),
        ];

        // 2. Processa as Unidades (transforma o array 'unidades' do form para 'medicoes')
        $unidadesRaw = $this->request->getPost('unidades');
        $medicoes = [];

        if ($unidadesRaw && is_array($unidadesRaw)) {
            foreach ($unidadesRaw as $u) {
                // Lógica para definir número de fases (int) baseado na categoria
                $fasesInt = 1; // Padrão Monofásico
                if (isset($u['categoria'])) {
                    if ($u['categoria'] == 'B') $fasesInt = 2;
                    if ($u['categoria'] == 'T') $fasesInt = 3;
                }

                $medicoes[] = [
                    // Dados básicos
                    'classe'             => $u['classe'] ?? '',
                    'categoria'          => $u['categoria'] ?? '',
                    'dimensionamento_id' => $u['dimensionamento_id'] ?? '',
                    'info_tecnica'       => $u['info_tecnica'] ?? '',
                    'placa'              => $u['placa'] ?? '',
                    'numero_uc'          => $u['numero_uc'] ?? '',
                    'observacao'         => $u['observacao'] ?? '',
                    
                    // Dados técnicos detalhados (Cabo/Tubo/Disj)
                    'cabo'              => $u['cabo'] ?? '', 
                    'eletroduto'        => $u['eletroduto'] ?? '',
                    'disjuntor'         => $u['disjuntor'] ?? '', // Texto (ex: 50A)
                    // [NOVO] Captura as fases específicas (A, AB, BC...)
                    'fases_especificas'  => $u['fases_especificas'] ?? '',
                    
                    // Mantém o cálculo numérico para lógicas internas
                    'fases'              => ($u['categoria'] == 'T') ? 3 : (($u['categoria'] == 'B') ? 2 : 1)
                ];
            }
        }

        $dados['medicoes'] = $medicoes;
        
        // Debug rápido: Se quiser ver o que está chegando, descomente a linha abaixo
        // dd($dados); 

        // Gera a view de download
        return view('projeto_resultado', ['dados' => $dados]);
    }

    public function listaMateriais()
    {
        $jsonPayload = $this->request->getPost('payload_projeto');
        if (!$jsonPayload) return "Erro.";

        $p = json_decode($jsonPayload, true);
        $kitModel = new \App\Models\KitMaterialModel();

        // --- 1. ENTRADA (Concatenação Manual) ---
        $materiaisEntrada = [];

        if (!empty($p['entrada_cabo'])) {
            $materiaisEntrada[] = [
                'descricao' => 'Cabo de Entrada (Ramal de Ligação) ' . $p['entrada_cabo'], // Concatenado
                'qtd'       => '',
                'unidade'   => 'm'
            ];
        }
        if (!empty($p['entrada_eletroduto'])) {
            $materiaisEntrada[] = [
                'descricao' => 'Eletroduto de Entrada ' . $p['entrada_eletroduto'], // Concatenado
                'qtd'       => '1',
                'unidade'   => 'Unid'
            ];
        }
        if (!empty($p['entrada_disjuntor'])) {
            $materiaisEntrada[] = [
                'descricao' => 'Disjuntor Geral (Proteção Entrada) ' . $p['entrada_disjuntor'], // Concatenado
                'qtd'       => '1',
                'unidade'   => 'Unid'
            ];
        }
        
        // Itens fixos
        $materiaisEntrada[] = ['descricao' => 'Caixa de Proteção (Padrão Concessionária)', 'qtd' => '1', 'unidade' => 'Unid'];
        $materiaisEntrada[] = ['descricao' => 'Armação Secundária com isolador', 'qtd' => '1', 'unidade' => 'Peça'];


        // --- 2. PROTEÇÃO (Kits do Banco) ---
        
        $slugKitTerra = 'terra_1_haste';
        if (isset($p['terra_hastes']) && (strpos($p['terra_hastes'], '3') !== false)) {
            $slugKitTerra = 'terra_3_hastes';
        }

        // O Model já devolve 'descricao' concatenada com o placeholder {terra_cabo}
        $materiaisProtecao = $kitModel->buscarItensProcessados($slugKitTerra, $p);

        // DPS (Manual)
        if (!empty($p['dps_ka']) || !empty($p['dps_tensao'])) {
            $qtdDps = $p['numero_fases'] ?? 1;
            array_unshift($materiaisProtecao, [
                'descricao' => 'DPS - Dispositivo de Proteção ' . ($p['dps_ka'] ?? '') . ' ' . ($p['dps_tensao'] ?? ''),
                'qtd'       => $qtdDps,
                'unidade'   => 'Unid'
            ]);
        }

        // --- 3. MEDIÇÕES ---
        $listaMedicoes = [];
        
        if (isset($p['medicoes']) && is_array($p['medicoes'])) {
            $contagemDisj = [];
            $tiposCabos = [];
            $tiposEletrodutos = [];
            $qtdCaixas = 0;

            foreach ($p['medicoes'] as $m) {
                $qtdCaixas++;
                if (!empty($m['disjuntor'])) {
                    $chave = $m['disjuntor'];
                    if (!isset($contagemDisj[$chave])) $contagemDisj[$chave] = 0;
                    $contagemDisj[$chave]++;
                }
                if (!empty($m['cabo']) && !in_array($m['cabo'], $tiposCabos)) $tiposCabos[] = $m['cabo'];
                if (!empty($m['eletroduto']) && !in_array($m['eletroduto'], $tiposEletrodutos)) $tiposEletrodutos[] = $m['eletroduto'];
            }

            // Caixa
            $listaMedicoes[] = [
                'descricao' => 'Caixa módulo de Medição',
                'qtd'       => $qtdCaixas,
                'unidade'   => 'Unid'
            ];

            // Disjuntores
            foreach ($contagemDisj as $amp => $qtd) {
                $listaMedicoes[] = [
                    'descricao' => "Disjuntor Termomagnético DIN/NEMA " . $amp, // Concatenado
                    'qtd'       => $qtd,
                    'unidade'   => 'Unid'
                ];
            }

            // Cabos
            foreach ($tiposCabos as $cabo) {
                $listaMedicoes[] = [
                    'descricao' => "Cabo Ramal Interno (Flexível/Rígido) " . $cabo, // Concatenado
                    'qtd'       => '',
                    'unidade'   => 'm'
                ];
            }

            // Eletrodutos
            foreach ($tiposEletrodutos as $tubo) {
                $listaMedicoes[] = [
                    'descricao' => "Eletroduto Ramal Interno " . $tubo, // Concatenado
                    'qtd'       => '',
                    'unidade'   => 'm'
                ];
            }
        }

        // Cabeçalho
        $cabecalho = [
            'obra'     => $p['titulo_obra'] ?? '',
            'cliente'  => $p['cliente_nome'] ?? '',
            'endereco' => ($p['logradouro'] ?? '') . ', ' . ($p['numero'] ?? '') . ' - ' . ($p['cidade'] ?? '')
        ];

        return view('lista_materiais', [
            'cabecalho' => $cabecalho,
            'entrada'   => $materiaisEntrada,
            'protecao'  => $materiaisProtecao,
            'medicoes'  => $listaMedicoes
        ]);
    }
    
}