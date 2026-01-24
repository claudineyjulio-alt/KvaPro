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

        return view('projeto/projeto_novo', $data); // Ou o nome da sua view de formulário
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
        if (!$jsonPayload) return "Erro: Payload vazio.";

        $p = json_decode($jsonPayload, true);

        $regraMatModel = new \App\Models\RegraMaterialModel();
        $kitModel      = new \App\Models\KitMaterialModel();

        // =========================================================================
        // PASSO 1: SELECIONAR OS KITS E GUARDAR A REGRA (AUDITORIA)
        // =========================================================================

        $idConcessionaria = $p['concessionaria_id'] ?? 1;
        $regras = $regraMatModel->where('concessionaria_id', $idConcessionaria)
            ->orderBy('prioridade', 'ASC')
            ->findAll();

        $kitsSelecionados = []; // Agora guarda Array: ['id' => 10, 'regra' => 'Poste 7m se for Rua']

        foreach ($regras as $regra) {
            $categoria = $regra['tipo_kit'];

            if (isset($kitsSelecionados[$categoria])) continue; // Já selecionado

            $passou = false;
            if ($regra['variavel'] === 'padrao') {
                $passou = true;
            } else {
                $valorProjeto = $p[$regra['variavel']] ?? null;
                if ($valorProjeto !== null) {
                    switch ($regra['condicao']) {
                        case '=':
                            $passou = ($valorProjeto == $regra['valor_min']);
                            break;
                        case '>':
                            $passou = ($valorProjeto > $regra['valor_min']);
                            break;
                        case '>=':
                            $passou = ($valorProjeto >= $regra['valor_min']);
                            break;
                        case '<':
                            $passou = ($valorProjeto < $regra['valor_min']);
                            break;
                        case 'BETWEEN':
                            $passou = ($valorProjeto >= $regra['valor_min'] && $valorProjeto <= $regra['valor_max']);
                            break;
                        case 'CONTEM':
                            $passou = (strpos((string)$valorProjeto, (string)$regra['valor_min']) !== false);
                            break;
                    }
                }
            }

            if ($passou) {
                // AQUI ESTÁ A MUDANÇA: Guardamos o ID e a Descrição da Regra
                $kitsSelecionados[$categoria] = [
                    'id'    => $regra['kit_id'],
                    'regra' => $regra['descricao'] . ' (Prioridade ' . $regra['prioridade'] . ')'
                ];
            }
        }

        // =========================================================================
        // PASSO 2: EXPANDIR E ANEXAR A ORIGEM
        // =========================================================================

        $listaBruta = [];

        foreach ($kitsSelecionados as $cat => $kitData) {
            $itensDoKit = $kitModel->buscarItensProcessados($kitData['id'], $p);

            // Adiciona a origem em cada item
            foreach ($itensDoKit as &$item) {
                $item['origem'] = $kitData['regra']; // Ex: "Travessia de Rua exige Poste 7m"
            }
            $listaBruta = array_merge($listaBruta, $itensDoKit);
        }

        // Itens Manuais (Medições)
        // if (isset($p['medicoes']) && is_array($p['medicoes'])) {
        //     foreach ($p['medicoes'] as $m) {
        //         $regraIndividual = "Definição Individual (Medição/Ramal)";
        //         if (!empty($m['disjuntor'])) {
        //             $listaBruta[] = ['descricao' => "Disjuntor Termomagnético DIN " . $m['disjuntor'], 'qtd' => 1, 'unidade' => 'Unid', 'origem' => $regraIndividual];
        //         }
        //         if (!empty($m['cabo'])) {
        //             $listaBruta[] = ['descricao' => "Cabo Ramal Interno " . $m['cabo'], 'qtd' => $m['distancia'] ?? 10, 'unidade' => 'm', 'origem' => $regraIndividual];
        //         }
        //         if (!empty($m['eletroduto'])) {
        //             $listaBruta[] = ['descricao' => "Eletroduto Ramal Interno " . $m['eletroduto'], 'qtd' => $m['distancia'] ?? 10, 'unidade' => 'm', 'origem' => $regraIndividual];
        //         }
        //     }
        // }

        // =========================================================================
        // PASSO 3: AGRUPAMENTO INTELIGENTE (Junta descrições de regras)
        // =========================================================================

        $listaConsolidada = [];
        foreach ($listaBruta as $item) {
            $chave = trim($item['descricao']);

            if (isset($listaConsolidada[$chave])) {
                // Soma quantidade
                $listaConsolidada[$chave]['qtd'] += $item['qtd'];

                // Concatena a regra se for diferente (para saber que veio de 2 lugares)
                // Ex: "Regra Poste + Regra Aterramento"
                if (strpos($listaConsolidada[$chave]['origem'], $item['origem']) === false) {
                    $listaConsolidada[$chave]['origem'] .= " + " . $item['origem'];
                }
            } else {
                $listaConsolidada[$chave] = $item;
            }
        }

        $listaFinal = array_values($listaConsolidada);

        // Ordenação
        usort($listaFinal, function ($a, $b) {
            return strcasecmp($a['descricao'], $b['descricao']);
        });

        // =========================================================================
        // PASSO 4: RETORNO
        // =========================================================================

        $cabecalho = [
            'obra'     => $p['titulo_obra'] ?? '',
            'cliente'  => $p['cliente_nome'] ?? '',
            'endereco' => ($p['logradouro'] ?? '') . ', ' . ($p['numero'] ?? '')
        ];

        return view('lista_materiais', [
            'cabecalho' => $cabecalho,
            'materiais' => $listaFinal,
            'medicoes'  => []
        ]);
    }

    // public function listaMateriais()
    // {
    //     // 1. Recebe e Decodifica o JSON
    //     $jsonPayload = $this->request->getPost('payload_projeto');
    //     if (!$jsonPayload) return "Erro: Payload vazio.";

    //     $p = json_decode($jsonPayload, true);

    //     // Inicializa Models
    //     $regraMatModel = new \App\Models\RegraMaterialModel();
    //     $kitModel      = new \App\Models\KitMaterialModel();

    //     // =========================================================================
    //     // PASSO 1: SELECIONAR OS KITS (MOTOR DE REGRAS)
    //     // =========================================================================

    //     $idConcessionaria = $p['concessionaria_id'] ?? 1;

    //     // Busca regras ordenadas pela prioridade (1 primeiro, 99 por último)
    //     $regras = $regraMatModel->where('concessionaria_id', $idConcessionaria)
    //         ->orderBy('prioridade', 'ASC')
    //         ->findAll();

    //     $kitsSelecionadosIds = [];

    //     foreach ($regras as $regra) {
    //         $categoria = $regra['tipo_kit'];

    //         // Se já selecionamos um kit para esta categoria (prioridade maior), pula os próximos
    //         if (isset($kitsSelecionadosIds[$categoria])) {
    //             continue;
    //         }

    //         // Avalia a condição
    //         $passou = false;

    //         if ($regra['variavel'] === 'padrao') {
    //             $passou = true;
    //         } else {
    //             $valorProjeto = $p[$regra['variavel']] ?? null;

    //             if ($valorProjeto !== null) {
    //                 switch ($regra['condicao']) {
    //                     case '=':
    //                         $passou = ($valorProjeto == $regra['valor_min']);
    //                         break;
    //                     case '>':
    //                         $passou = ($valorProjeto > $regra['valor_min']);
    //                         break;
    //                     case '>=':
    //                         $passou = ($valorProjeto >= $regra['valor_min']);
    //                         break;
    //                     case '<':
    //                         $passou = ($valorProjeto < $regra['valor_min']);
    //                         break;
    //                     case 'BETWEEN':
    //                         $passou = ($valorProjeto >= $regra['valor_min'] && $valorProjeto <= $regra['valor_max']);
    //                         break;
    //                     case 'CONTEM':
    //                         $passou = (strpos((string)$valorProjeto, (string)$regra['valor_min']) !== false);
    //                         break;
    //                 }
    //             }
    //         }

    //         // Se a regra passou, guarda o ID do Kit
    //         if ($passou) {
    //             $kitsSelecionadosIds[$categoria] = $regra['kit_id'];
    //         }
    //     }

    //     // =========================================================================
    //     // PASSO 2: EXPANDIR E AGRUPAR ITENS
    //     // =========================================================================

    //     $listaBruta = [];

    //     // Busca os itens de cada Kit selecionado
    //     foreach ($kitsSelecionadosIds as $kitId) {
    //         $itensDoKit = $kitModel->buscarItensProcessados($kitId, $p);
    //         $listaBruta = array_merge($listaBruta, $itensDoKit);
    //     }

    //     // Adiciona itens manuais das medições (se houver)
    //     // if (isset($p['medicoes']) && is_array($p['medicoes'])) {
    //     //     foreach ($p['medicoes'] as $m) {
    //     //         if (!empty($m['disjuntor'])) {
    //     //             $listaBruta[] = ['descricao' => "Disjuntor Termomagnético DIN " . $m['disjuntor'], 'qtd' => 1, 'unidade' => 'teste'];
    //     //         }
    //     //         if (!empty($m['cabo'])) {
    //     //             // Aqui assumi 10m se não tiver distância, ajuste conforme sua lógica
    //     //             $listaBruta[] = ['descricao' => "Cabo Ramal Interno " . $m['cabo'], 'qtd' => $m['distancia'] ?? 10, 'unidade' => 'teste'];
    //     //         }
    //     //         if (!empty($m['eletroduto'])) {
    //     //             $listaBruta[] = ['descricao' => "Eletroduto Ramal Interno " . $m['eletroduto'], 'qtd' => $m['distancia'] ?? 10, 'unidade' => 'teste'];
    //     //         }
    //     //     }
    //     // }

    //     // LÓGICA DE AGRUPAMENTO (SOMAR QUANTIDADES DE ITENS IGUAIS)
    //     $listaConsolidada = [];
    //     foreach ($listaBruta as $item) {
    //         $chave = trim($item['descricao']); // A chave é a descrição (ex: "Conector")

    //         if (isset($listaConsolidada[$chave])) {
    //             // Se já existe, soma a quantidade
    //             $listaConsolidada[$chave]['qtd'] += $item['qtd'];
    //         } else {
    //             // Se não existe, cria
    //             $listaConsolidada[$chave] = $item;
    //         }
    //     }

    //     // Converte de volta para array numérico
    //     $listaFinal = array_values($listaConsolidada);

    //     // =========================================================================
    //     // PASSO 3: ORDENAÇÃO (A-Z)
    //     // =========================================================================

    //     usort($listaFinal, function ($a, $b) {
    //         return strcasecmp($a['descricao'], $b['descricao']);
    //     });

    //     // =========================================================================
    //     // PASSO 4: RETORNO PARA VIEW
    //     // =========================================================================

    //     $cabecalho = [
    //         'obra'     => $p['titulo_obra'] ?? '',
    //         'cliente'  => $p['cliente_nome'] ?? '',
    //         'endereco' => ($p['logradouro'] ?? '') . ', ' . ($p['numero'] ?? '')
    //     ];

    //     // ATENÇÃO: Verifique se sua View espera a variável 'materiais'
    //     return view('lista_materiais', [
    //         'cabecalho' => $cabecalho,
    //         'materiais' => $listaFinal,
    //         'medicoes'  => [] // Deixei vazio pois já juntei tudo na lista principal acima
    //     ]);
    // }
}
