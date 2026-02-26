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

    public function salvar()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $dados = $this->request->getPost();
        // printf("<pre>%s</pre>", print_r($dados, true));
        // exit;

        // Gera a view de download dos arquivos
        return view('projeto/projeto_processado', ['dados' => $dados]);
    }

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

        if (empty($projeto['unidades'])) {
            return "Erro: Nenhuma unidade recebida. Acesse através da tela de processamento.";
        }

        // printf("<pre>%s</pre>", print_r($jsonPayload, true));   
        // return;
        //  print_r($jsonPayload, true);
        // exit;

        // 3. Carrega a view do diagrama com os dados mapeados
        return view('projeto/diagrama_unifilar', $projeto);
    }

    public function layout()
    {
        $jsonPayload = $this->request->getPost('payload_projeto');
        if (!$jsonPayload) {
            return "Erro: Nenhum dado de projeto recebido. Acesse através da tela de processamento.";
        }
        $projeto = json_decode($jsonPayload, true);
        if (empty($projeto['unidades'])) {
            return "Erro: Nenhuma unidade recebida. Acesse através da tela de processamento.";
        }
        return view('projeto/layout', $projeto);
    }

    public function teste()
    {
        $projeto['medicoes'] = [
            ['identificador' => 'C.01', 'categoria' => 1], // Monopolar
            ['identificador' => 'C.02', 'categoria' => 2], // Bipolar
            ['identificador' => 'C.03', 'categoria' => 3], // Tripolar
            ['identificador' => 'C.04', 'categoria' => 2], // Bipolar
            ['identificador' => 'C.05', 'categoria' => 1], // Monopolar
            ['identificador' => 'C.06', 'categoria' => 2], // Bipolar
            ['identificador' => 'C.07', 'categoria' => 3], // Tripolar
            ['identificador' => 'C.08', 'categoria' => 2], // Bipolar
            ['identificador' => 'C.09', 'categoria' => 1], // Monopolar
            ['identificador' => 'C.10', 'categoria' => 2], // Bipolar
            ['identificador' => 'C.11', 'categoria' => 3], // Tripolar
            ['identificador' => 'C.12', 'categoria' => 2], // Bipolar
        ];


        return view('projeto/teste', $projeto);
    }

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

    public function listaMateriais()
    {
        $jsonPayload = $this->request->getPost('payload_projeto');
        if (!$jsonPayload) return "Erro: Payload vazio.";

        $p = json_decode($jsonPayload, true);

        if (empty($p['unidades'])) {
            return "Erro: Nenhuma unidade recebida. Acesse através da tela de processamento.";
        }

        $regraMatModel  = new \App\Models\RegraMaterialModel();
        $definicaoModel = new \App\Models\DefinicaoRegraMatModel();
        $kitModel       = new \App\Models\KitMaterialModel();

        $idConcessionaria = $p['concessionaria_id'] ?? 1;
        $regras = $regraMatModel->where('concessionaria_id', $idConcessionaria)
            ->orderBy('prioridade', 'ASC')
            ->findAll();

        $listaBruta = [];

        // =========================================================================
        // PASSO 1: AVALIAR REGRAS GLOBAIS (Ex: Aterramento, Poste, etc)
        // =========================================================================
        $kitsGlobais = [];

        foreach ($regras as $regra) {
            $categoria = strtolower(trim($regra['tipo_kit']));

            // PULA as regras que são individuais (vamos tratá-las no Passo 1.5)
            if (strpos($categoria, 'medicao') !== false || strpos($categoria, 'unidade') !== false) {
                continue;
            }

            if (isset($kitsGlobais[$categoria])) continue;

            $condicoes = $definicaoModel->where('regra_id', $regra['id'])->findAll();
            $todasPassaram = true;

            foreach ($condicoes as $cond) {
                if ($cond['variavel'] === 'padrao') continue;
                $valProj = $p[$cond['variavel']] ?? null;
                if ($valProj === null) {
                    $todasPassaram = false;
                    break;
                }

                $valProjStr = strtolower(trim((string)$valProj));
                $valMinStr  = strtolower(trim((string)$cond['valor_min']));
                $passou = false;

                switch ($cond['condicao']) {
                    case '=':
                        $passou = ($valProjStr == $valMinStr);
                        break;
                    case '!=':
                        $passou = ($valProjStr != $valMinStr);
                        break;
                    case '>':
                        $passou = ((float)$valProj > (float)$cond['valor_min']);
                        break;
                    case '>=':
                        $passou = ((float)$valProj >= (float)$cond['valor_min']);
                        break;
                    case '<':
                        $passou = ((float)$valProj < (float)$cond['valor_min']);
                        break;
                    case '<=':
                        $passou = ((float)$valProj <= (float)$cond['valor_min']);
                        break;
                    case 'BETWEEN':
                        $passou = ((float)$valProj >= (float)$cond['valor_min'] && (float)$valProj <= (float)$cond['valor_max']);
                        break;
                    case 'CONTEM':
                        $passou = (strpos($valProjStr, $valMinStr) !== false);
                        break;
                }
                if (!$passou) {
                    $todasPassaram = false;
                    break;
                }
            }

            if ($todasPassaram && !empty($condicoes)) {
                $kitsGlobais[$categoria] = ['id' => $regra['kit_id'], 'regra' => $regra['nome']];
            }
        }

        // Expande Kits Globais
        foreach ($kitsGlobais as $cat => $kitData) {
            $itensDoKit = $kitModel->buscarItensProcessados($kitData['id'], $p);
            foreach ($itensDoKit as $k => $item) {
                $itensDoKit[$k]['origem'] = $kitData['regra'];
            }
            $listaBruta = array_merge($listaBruta, $itensDoKit);
        }

        // =========================================================================
        // PASSO 1.5: AVALIAR REGRAS INDIVIDUAIS (LOOP DE MEDIÇÕES)
        // =========================================================================
        $medicoes = $p['unidades'] ?? ($p['medicoes'] ?? []);

        foreach ($medicoes as $index => $m) {

            // Cria um ambiente virtual misturando os dados globais com os dados DESTA medição
            $pMedicao = $p;
            $pMedicao['unidade_classe']    = $m['classe'] ?? '';
            $pMedicao['unidade_categoria'] = $m['categoria'] ?? '1';
            $pMedicao['unidade_fase']      = $m['fase'] ?? '';
            $pMedicao['unidade_neutro']    = $m['neutro'] ?? '';
            $pMedicao['unidade_eletroduto'] = $m['eletroduto'] ?? '';

            // Limpa o "A" do disjuntor caso o JS tenha mandado "40A" (deixa só o número para matemática)
            $pMedicao['unidade_disjuntor'] = preg_replace('/[^0-9\.]/', '', $m['disjuntor'] ?? '');

            $kitsDestaMedicao = [];

            foreach ($regras as $regra) {
                $categoria = strtolower(trim($regra['tipo_kit']));

                // SÓ avalia aqui as regras que contêm 'medicao' ou 'unidade' no Tipo de Kit
                if (strpos($categoria, 'medicao') === false && strpos($categoria, 'unidade') === false) {
                    continue;
                }

                if (isset($kitsDestaMedicao[$categoria])) continue;

                $condicoes = $definicaoModel->where('regra_id', $regra['id'])->findAll();
                $todasPassaram = true;

                foreach ($condicoes as $cond) {
                    if ($cond['variavel'] === 'padrao') continue;

                    // A MÁGICA: Agora ele busca a variável no ambiente da medição!
                    $valProj = $pMedicao[$cond['variavel']] ?? null;
                    if ($valProj === null) {
                        $todasPassaram = false;
                        break;
                    }

                    $valProjStr = strtolower(trim((string)$valProj));
                    $valMinStr  = strtolower(trim((string)$cond['valor_min']));
                    $passou = false;

                    switch ($cond['condicao']) {
                        case '=':
                            $passou = ($valProjStr == $valMinStr);
                            break;
                        case '!=':
                            $passou = ($valProjStr != $valMinStr);
                            break;
                        case '>':
                            $passou = ((float)$valProj > (float)$cond['valor_min']);
                            break;
                        case '>=':
                            $passou = ((float)$valProj >= (float)$cond['valor_min']);
                            break;
                        case '<':
                            $passou = ((float)$valProj < (float)$cond['valor_min']);
                            break;
                        case '<=':
                            $passou = ((float)$valProj <= (float)$cond['valor_min']);
                            break;
                        case 'BETWEEN':
                            $passou = ((float)$valProj >= (float)$cond['valor_min'] && (float)$valProj <= (float)$cond['valor_max']);
                            break;
                        case 'CONTEM':
                            $passou = (strpos($valProjStr, $valMinStr) !== false);
                            break;
                    }
                    if (!$passou) {
                        $todasPassaram = false;
                        break;
                    }
                }

                if ($todasPassaram && !empty($condicoes)) {
                    // Adiciona qual medidor acionou a regra (para auditoria)
                    $kitsDestaMedicao[$categoria] = ['id' => $regra['kit_id'], 'regra' => $regra['nome'] . ' (Relógio ' . ($index + 1) . ')'];
                }
            }

            // Expande os kits EXCLUSIVOS deste medidor usando os dados dele
            foreach ($kitsDestaMedicao as $cat => $kitData) {
                $itensDoKit = $kitModel->buscarItensProcessados($kitData['id'], $pMedicao);
                foreach ($itensDoKit as $k => $item) {
                    $itensDoKit[$k]['origem'] = $kitData['regra'];
                }
                $listaBruta = array_merge($listaBruta, $itensDoKit);
            }
        }

        // =========================================================================
        // PASSO 3: AGRUPAMENTO INTELIGENTE (Agrupa todos os iguais no final)
        // =========================================================================
        $listaConsolidada = [];
        foreach ($listaBruta as $item) {
            $chave = trim(mb_strtoupper($item['descricao']));

            if (isset($listaConsolidada[$chave])) {
                $listaConsolidada[$chave]['qtd'] += $item['qtd'];
                if (strpos($listaConsolidada[$chave]['origem'], $item['origem']) === false) {
                    $listaConsolidada[$chave]['origem'] .= " + " . $item['origem'];
                }
            } else {
                $listaConsolidada[$chave] = $item;
            }
        }

        $listaFinal = array_values($listaConsolidada);
        usort($listaFinal, function ($a, $b) {
            return strcasecmp($a['descricao'], $b['descricao']);
        });

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
    //     $jsonPayload = $this->request->getPost('payload_projeto');
    //     if (!$jsonPayload) return "Erro: Payload vazio.";

    //     $p = json_decode($jsonPayload, true);

    //     // Instancia os 3 models necessários agora
    //     $regraMatModel  = new \App\Models\RegraMaterialModel();
    //     $definicaoModel = new \App\Models\DefinicaoRegraMatModel(); // Model das Múltiplas Condições
    //     $kitModel       = new \App\Models\KitMaterialModel(); // Verifique o nome correto do seu Model de Kit aqui

    //     // =========================================================================
    //     // PASSO 1: AVALIAR REGRAS E SELECIONAR KITS (Com múltiplas condições E)
    //     // =========================================================================

    //     $idConcessionaria = $p['concessionaria_id'] ?? 1;

    //     // Puxa as regras ordenadas por prioridade (1 = Máxima / 99 = Padrão)
    //     $regras = $regraMatModel->where('concessionaria_id', $idConcessionaria)
    //         ->orderBy('prioridade', 'ASC')
    //         ->findAll();

    //     $kitsSelecionados = []; // Guarda: ['infra' => ['id' => 10, 'regra' => 'Poste 7m']]

    //     foreach ($regras as $regra) {
    //         $categoria = $regra['tipo_kit'];

    //         // Se já escolhemos um kit para esta categoria (por uma regra de prioridade maior), ignora.
    //         if (isset($kitsSelecionados[$categoria])) continue;

    //         // Busca todas as condições atreladas a esta regra
    //         $condicoes = $definicaoModel->where('regra_id', $regra['id'])->findAll();

    //         // Flag de aprovação (Operador E/AND): Assume que é verdadeira até uma falhar
    //         $todasPassaram = true;

    //         foreach ($condicoes as $cond) {
    //             if ($cond['variavel'] === 'padrao') {
    //                 continue; // "padrao" sempre passa, ignora a validação
    //             }

    //             $valorProjeto = $p[$cond['variavel']] ?? null;

    //             // Se o JSON do projeto não enviou essa variável e a regra exige, a regra falha.
    //             if ($valorProjeto === null) {
    //                 $todasPassaram = false;
    //                 break;
    //             }

    //             // Converte para minúsculas para não dar erro se o usuário digitar "PVC" e no banco estar "pvc"
    //             $valProjStr = strtolower(trim((string)$valorProjeto));
    //             $valMinStr  = strtolower(trim((string)$cond['valor_min']));

    //             $passouNestaCondicao = false;

    //             switch ($cond['condicao']) {
    //                 case '=':
    //                     $passouNestaCondicao = ($valProjStr == $valMinStr);
    //                     break;
    //                 case '!=':
    //                     $passouNestaCondicao = ($valProjStr != $valMinStr);
    //                     break;
    //                 case '>':
    //                     $passouNestaCondicao = ((float)$valorProjeto > (float)$cond['valor_min']);
    //                     break;
    //                 case '>=':
    //                     $passouNestaCondicao = ((float)$valorProjeto >= (float)$cond['valor_min']);
    //                     break;
    //                 case '<':
    //                     $passouNestaCondicao = ((float)$valorProjeto < (float)$cond['valor_min']);
    //                     break;
    //                 case '<=':
    //                     $passouNestaCondicao = ((float)$valorProjeto <= (float)$cond['valor_min']);
    //                     break;
    //                 case 'BETWEEN':
    //                     $passouNestaCondicao = ((float)$valorProjeto >= (float)$cond['valor_min'] && (float)$valorProjeto <= (float)$cond['valor_max']);
    //                     break;
    //                 case 'CONTEM':
    //                     $passouNestaCondicao = (strpos($valProjStr, $valMinStr) !== false);
    //                     break;
    //             }

    //             // Se uma única condição falhar, aborta a validação dessa regra
    //             if (!$passouNestaCondicao) {
    //                 $todasPassaram = false;
    //                 break;
    //             }
    //         }

    //         // Se sobreviveu a TODAS as condições e o array não estava vazio
    //         if ($todasPassaram && !empty($condicoes)) {
    //             $kitsSelecionados[$categoria] = [
    //                 'id'    => $regra['kit_id'],
    //                 'regra' => $regra['nome'] . ' (Prio: ' . $regra['prioridade'] . ')'
    //             ];
    //         }
    //     }

    //     // =========================================================================
    //     // PASSO 2: EXPANDIR E ANEXAR A ORIGEM
    //     // =========================================================================

    //     $listaBruta = [];

    //     foreach ($kitsSelecionados as $cat => $kitData) {
    //         // OBS: Certifique-se de que o método buscarItensProcessados aceita o ID do Kit e o array $p
    //         $itensDoKit = $kitModel->buscarItensProcessados($kitData['id'], $p);

    //         // Adiciona a origem em cada item para a auditoria
    //         foreach ($itensDoKit as &$item) {
    //             $item['origem'] = $kitData['regra'];
    //         }
    //         unset($item);
    //         $listaBruta = array_merge($listaBruta, $itensDoKit);
    //     }

    //     // REMOVIDO: Blocos de códigos chumbados para medições

    //     // =========================================================================
    //     // PASSO 3: AGRUPAMENTO INTELIGENTE (Junta descrições e soma quantidades)
    //     // =========================================================================

    //     $listaConsolidada = [];
    //     foreach ($listaBruta as $item) {
    //         $chave = trim($item['descricao']);

    //         if (isset($listaConsolidada[$chave])) {
    //             // Soma quantidade
    //             $listaConsolidada[$chave]['qtd'] += $item['qtd'];

    //             // Concatena a regra se for de kits diferentes para rastreabilidade
    //             if (strpos($listaConsolidada[$chave]['origem'], $item['origem']) === false) {
    //                 $listaConsolidada[$chave]['origem'] .= " + " . $item['origem'];
    //             }
    //         } else {
    //             $listaConsolidada[$chave] = $item;
    //         }
    //     }
    //     unset($item);
    //     $listaFinal = array_values($listaConsolidada);

    //     // Ordenação alfabética
    //     usort($listaFinal, function ($a, $b) {
    //         return strcasecmp($a['descricao'], $b['descricao']);
    //     });

    //     // =========================================================================
    //     // PASSO 4: RETORNO
    //     // =========================================================================

    //     $cabecalho = [
    //         'obra'     => $p['titulo_obra'] ?? '',
    //         'cliente'  => $p['cliente_nome'] ?? '',
    //         'endereco' => ($p['logradouro'] ?? '') . ', ' . ($p['numero'] ?? '')
    //     ];

    //     return view('lista_materiais', [
    //         'cabecalho' => $cabecalho,
    //         'materiais' => $listaFinal,
    //         'medicoes'  => []
    //     ]);
    // }


    // public function listaMateriais()
    // {
    //     $jsonPayload = $this->request->getPost('payload_projeto');
    //     if (!$jsonPayload) return "Erro: Payload vazio.";

    //     $p = json_decode($jsonPayload, true);

    //     $regraMatModel = new \App\Models\RegraMaterialModel();
    //     $kitModel      = new \App\Models\KitMaterialModel();

    //     // =========================================================================
    //     // PASSO 1: SELECIONAR OS KITS E GUARDAR A REGRA (AUDITORIA)
    //     // =========================================================================

    //     $idConcessionaria = $p['concessionaria_id'] ?? 1;
    //     $regras = $regraMatModel->where('concessionaria_id', $idConcessionaria)
    //         ->orderBy('prioridade', 'ASC')
    //         ->findAll();

    //     $kitsSelecionados = []; // Agora guarda Array: ['id' => 10, 'regra' => 'Poste 7m se for Rua']

    //     foreach ($regras as $regra) {
    //         $categoria = $regra['tipo_kit'];

    //         if (isset($kitsSelecionados[$categoria])) continue; // Já selecionado

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

    //         if ($passou) {
    //             // AQUI ESTÁ A MUDANÇA: Guardamos o ID e a Descrição da Regra
    //             $kitsSelecionados[$categoria] = [
    //                 'id'    => $regra['kit_id'],
    //                 'regra' => $regra['descricao'] . ' (Prioridade ' . $regra['prioridade'] . ')'
    //             ];
    //         }
    //     }

    //     // =========================================================================
    //     // PASSO 2: EXPANDIR E ANEXAR A ORIGEM
    //     // =========================================================================

    //     $listaBruta = [];

    //     foreach ($kitsSelecionados as $cat => $kitData) {
    //         $itensDoKit = $kitModel->buscarItensProcessados($kitData['id'], $p);

    //         // Adiciona a origem em cada item
    //         foreach ($itensDoKit as &$item) {
    //             $item['origem'] = $kitData['regra']; // Ex: "Travessia de Rua exige Poste 7m"
    //         }
    //         $listaBruta = array_merge($listaBruta, $itensDoKit);
    //     }

    //     // Itens Manuais (Medições)
    //     // if (isset($p['medicoes']) && is_array($p['medicoes'])) {
    //     //     foreach ($p['medicoes'] as $m) {
    //     //         $regraIndividual = "Definição Individual (Medição/Ramal)";
    //     //         if (!empty($m['disjuntor'])) {
    //     //             $listaBruta[] = ['descricao' => "Disjuntor Termomagnético DIN " . $m['disjuntor'], 'qtd' => 1, 'unidade' => 'Unid', 'origem' => $regraIndividual];
    //     //         }
    //     //         if (!empty($m['cabo'])) {
    //     //             $listaBruta[] = ['descricao' => "Cabo Ramal Interno " . $m['cabo'], 'qtd' => $m['distancia'] ?? 10, 'unidade' => 'm', 'origem' => $regraIndividual];
    //     //         }
    //     //         if (!empty($m['eletroduto'])) {
    //     //             $listaBruta[] = ['descricao' => "Eletroduto Ramal Interno " . $m['eletroduto'], 'qtd' => $m['distancia'] ?? 10, 'unidade' => 'm', 'origem' => $regraIndividual];
    //     //         }
    //     //     }
    //     // }

    //     // =========================================================================
    //     // PASSO 3: AGRUPAMENTO INTELIGENTE (Junta descrições de regras)
    //     // =========================================================================

    //     $listaConsolidada = [];
    //     foreach ($listaBruta as $item) {
    //         $chave = trim($item['descricao']);

    //         if (isset($listaConsolidada[$chave])) {
    //             // Soma quantidade
    //             $listaConsolidada[$chave]['qtd'] += $item['qtd'];

    //             // Concatena a regra se for diferente (para saber que veio de 2 lugares)
    //             // Ex: "Regra Poste + Regra Aterramento"
    //             if (strpos($listaConsolidada[$chave]['origem'], $item['origem']) === false) {
    //                 $listaConsolidada[$chave]['origem'] .= " + " . $item['origem'];
    //             }
    //         } else {
    //             $listaConsolidada[$chave] = $item;
    //         }
    //     }

    //     $listaFinal = array_values($listaConsolidada);

    //     // Ordenação
    //     usort($listaFinal, function ($a, $b) {
    //         return strcasecmp($a['descricao'], $b['descricao']);
    //     });

    //     // =========================================================================
    //     // PASSO 4: RETORNO
    //     // =========================================================================

    //     $cabecalho = [
    //         'obra'     => $p['titulo_obra'] ?? '',
    //         'cliente'  => $p['cliente_nome'] ?? '',
    //         'endereco' => ($p['logradouro'] ?? '') . ', ' . ($p['numero'] ?? '')
    //     ];

    //     return view('lista_materiais', [
    //         'cabecalho' => $cabecalho,
    //         'materiais' => $listaFinal,
    //         'medicoes'  => []
    //     ]);
    // }

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
