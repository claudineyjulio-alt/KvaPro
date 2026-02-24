<?php

namespace App\Models;

use CodeIgniter\Model;

class KitMaterialModel extends Model
{
    protected $table = 'kits';

    /**
     * Busca os itens de um kit estritamente pelo ID
     * @param int $kitId
     * @param array $dadosProjeto
     */

    public function buscarItensProcessados($kitId, $dadosProjeto)
    {
        // Segurança básica
        if (!is_numeric($kitId)) {
            return [];
        }

        $db = \Config\Database::connect();

        // 1. Incluindo o campo regra_qtd na busca do banco
        $builder = $db->table('kit_itens');
        $builder->select('materiais.descricao, materiais.unidade, kit_itens.quantidade, kit_itens.regra_qtd');
        $builder->join('materiais', 'materiais.id = kit_itens.material_id');
        $builder->where('kit_itens.kit_id', $kitId);

        $itens = $builder->get()->getResultArray();

        if (empty($itens)) {
            return [];
        }

        $itensProcessados = [];

        foreach ($itens as $item) {
            $descricaoFinal = $item['descricao'];

            // =========================================================
            // PASSO 1: SUBSTITUIÇÃO DE TEXTO {variavel} NO NOME
            // =========================================================
            if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $descricaoFinal, $matches)) {
                foreach ($matches[1] as $index => $nomeVariavel) {
                    $valor = isset($dadosProjeto[$nomeVariavel]) ? $dadosProjeto[$nomeVariavel] : '';
                    $descricaoFinal = str_replace($matches[0][$index], $valor, $descricaoFinal);
                }
            }
            $descricaoFinal = trim(preg_replace('/\s+/', ' ', $descricaoFinal));

            // =========================================================
            // PASSO 2: CÁLCULO DA QUANTIDADE (FÓRMULA MATEMÁTICA)
            // =========================================================
            $qtdFinal = (float) $item['quantidade']; // Quantidade padrão como base

            // Se o campo de regra matemática estiver preenchido no banco...
            if (!empty($item['regra_qtd']) && $item['quantidade']== 1 ) {
                $formula = $item['regra_qtd'];

                // Troca as variáveis da fórmula pelos números do projeto
                if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $formula, $matches)) {
                    foreach ($matches[1] as $index => $nomeVariavel) {
                        // Se não encontrar a variável no projeto, usa 0 na conta
                        $valorNumerico = (isset($dadosProjeto[$nomeVariavel]) && is_numeric($dadosProjeto[$nomeVariavel]))
                            ? (float) $dadosProjeto[$nomeVariavel]
                            : 0;
                        $formula = str_replace($matches[0][$index], $valorNumerico, $formula);
                    }
                }

                // Limpa a string para deixar SÓ matemática (medida de segurança)
                $formulaLimpa = preg_replace('/[^0-9\.\+\-\*\/\(\)]/', '', $formula);

                if ($formulaLimpa !== '') {
                    try {
                        // Executa a conta
                        $resultadoEval = eval("return ($formulaLimpa);");
                        if (is_numeric($resultadoEval)) {
                            $qtdFinal = (float) $resultadoEval;
                        }
                    } catch (\Throwable $e) {
                        // Se o usuário digitou uma fórmula quebrada no banco, ignora e usa a qtd base
                    }
                }
            }

            // =========================================================
            // PASSO 3: RETORNAR (Exibindo até os itens zerados!)
            // =========================================================
            $itensProcessados[] = [
                'descricao' => $descricaoFinal,
                'qtd'       => round($qtdFinal, 2), // Arredonda cabos/fios (ex: 7.50)
                'unidade'   => $item['unidade']
            ];
        }

        return $itensProcessados;
    }

    // public function buscarItensProcessados($kitId, $dadosProjeto)
    // {
    //     // Segurança básica
    //     if (!is_numeric($kitId)) {
    //         return [];
    //     }

    //     $db = \Config\Database::connect();

    //     // Busca os itens vinculados ao kit (sem a coluna de fórmula)
    //     $builder = $db->table('kit_itens');
    //     $builder->select('materiais.descricao, materiais.unidade, kit_itens.quantidade');
    //     $builder->join('materiais', 'materiais.id = kit_itens.material_id');
    //     $builder->where('kit_itens.kit_id', $kitId);

    //     $itens = $builder->get()->getResultArray();

    //     if (empty($itens)) {
    //         return [];
    //     }

    //     $itensProcessados = [];

    //     foreach ($itens as $item) {
    //         $descricaoFinal = $item['descricao'];

    //         // =========================================================
    //         // PASSO 1: SUBSTITUIÇÃO DE TEXTO {variavel}
    //         // =========================================================
    //         // Procura por tudo que estiver entre chaves, ex: {terra_cx}
    //         if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $descricaoFinal, $matches)) {
    //             foreach ($matches[1] as $index => $nomeVariavel) {
    //                 // Pega o valor do projeto se existir, senão deixa em branco
    //                 $valor = isset($dadosProjeto[$nomeVariavel]) ? $dadosProjeto[$nomeVariavel] : '';
    //                 // Substitui a tag {nomeVariavel} pelo valor na string
    //                 $descricaoFinal = str_replace($matches[0][$index], $valor, $descricaoFinal);
    //             }
    //         }

    //         // Limpa espaços duplos que possam sobrar se a variável ficar vazia
    //         $descricaoFinal = trim(preg_replace('/\s+/', ' ', $descricaoFinal));

    //         // =========================================================
    //         // PASSO 2: RETORNAR EXATAMENTE O QUE ESTÁ NO BANCO
    //         // =========================================================
    //         // Sem filtro de zero, sem cálculo de matemática. 
    //         // O que está salvo na tabela kit_itens vai aparecer na tela.
    //         $itensProcessados[] = [
    //             'descricao' => $descricaoFinal,
    //             'qtd'       => (float) $item['quantidade'],
    //             'unidade'   => $item['unidade']
    //         ];
    //     }

    //     return $itensProcessados;
    // }

    // public function buscarItensProcessados($kitId, $dadosProjeto)
    // {
    //     if (!is_numeric($kitId)) {
    //         return [];
    //     }

    //     $db = \Config\Database::connect();

    //     $builder = $db->table('kit_itens');
    //     $builder->select('materiais.descricao, materiais.unidade, kit_itens.quantidade, kit_itens.regra_qtd');
    //     $builder->join('materiais', 'materiais.id = kit_itens.material_id');
    //     $builder->where('kit_itens.kit_id', $kitId);
    //     $itens = $builder->get()->getResultArray();

    //     $itensProcessados = [];

    //     foreach ($itens as $item) {
    //         $desc = $item['descricao'];

    //         // =========================================================
    //         // PASSO 1: SUBSTITUIÇÃO DE TEXTO {variavel}
    //         // =========================================================
    //         // Regex segura: pega só letras, números e underlines dentro das chaves
    //         if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $desc, $matches)) {
    //             foreach ($matches[1] as $idx => $varName) {
    //                 // Pega o valor do JSON, se não existir deixa em branco
    //                 $val = isset($dadosProjeto[$varName]) ? $dadosProjeto[$varName] : '';
    //                 $desc = str_replace($matches[0][$idx], $val, $desc);
    //             }
    //         }

    //         // Remove espaços duplos caso a variável tenha ficado em branco
    //         $desc = trim(preg_replace('/\s+/', ' ', $desc));

    //         // =========================================================
    //         // PASSO 2: MATEMÁTICA DA QUANTIDADE
    //         // =========================================================
    //         $qtd = 1;//(float) $item['quantidade']

    //         // Se o campo de regra matemática estiver preenchido no banco
    //         if (!empty($item['regra_qtd'])) {
    //             $form = $item['regra_qtd'];

    //             if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $form, $matches)) {
    //                 foreach ($matches[1] as $idx => $varName) {
    //                     $valNum = (isset($dadosProjeto[$varName]) && is_numeric($dadosProjeto[$varName]))
    //                         ? (float) $dadosProjeto[$varName]
    //                         : 0;
    //                     $form = str_replace($matches[0][$idx], $valNum, $form);
    //                 }
    //             }

    //             // Limpa tudo que não for número e sinal matemático
    //             $formLimpa = preg_replace('/[^0-9\.\+\-\*\/\(\)]/', '', $form);

    //             if ($formLimpa !== '') {
    //                 try {
    //                     $evalRes = eval("return ($formLimpa);");
    //                     if (is_numeric($evalRes)) {
    //                         $qtd = (float) $evalRes;
    //                     }
    //                 } catch (\Throwable $e) {
    //                     // Ignora erro de digitação de fórmula e mantém a Qtd Original
    //                 }
    //             }
    //         }

    //         // =========================================================
    //         // PASSO 3: SÓ ENTRA NA LISTA SE FOR MAIOR QUE ZERO
    //         // =========================================================
    //         if ($qtd > 0) {
    //             $itensProcessados[] = [
    //                 'descricao' => $desc,
    //                 'qtd'       => round($qtd, 2),
    //                 'unidade'   => $item['unidade']
    //             ];
    //         }
    //     }

    //     return $itensProcessados;
    // }

    // public function buscarItensProcessados($kitId, $dadosProjeto)
    //     {
    //         // Se não vier um número, retorna vazio (Segurança)
    //         if (!is_numeric($kitId)) {
    //             return [];
    //         }

    //         $db = \Config\Database::connect();

    //         // 1. Busca os itens e a coluna regra_qtd
    //         $builder = $db->table('kit_itens');
    //         $builder->select('materiais.descricao, materiais.unidade, kit_itens.quantidade, kit_itens.regra_qtd');
    //         $builder->join('materiais', 'materiais.id = kit_itens.material_id');
    //         $builder->where('kit_itens.kit_id', $kitId);

    //         $itens = $builder->get()->getResultArray();

    //         if (empty($itens)) {
    //             return [];
    //         }

    //         $itensProcessados = [];

    //         // Início do Loop
    //         foreach ($itens as $item) {
    //             $descricaoFinal = $item['descricao'];

    //             // =========================================================
    //             // PASSO A: SUBSTITUIÇÃO DE VARIÁVEIS NO TEXTO {variavel}
    //             // =========================================================
    //             if (preg_match_all('/\{\s*(.*?)\s*\}/', $descricaoFinal, $matches)) {
    //                 foreach ($matches[1] as $index => $variavel) {
    //                     $placeholder = $matches[0][$index];
    //                     $valor = isset($dadosProjeto[$variavel]) ? $dadosProjeto[$variavel] : '';
    //                     $descricaoFinal = str_replace($placeholder, $valor, $descricaoFinal);
    //                 }
    //             }

    //             // =========================================================
    //             // PASSO B: CÁLCULO MATEMÁTICO (regra_qtd)
    //             // =========================================================
    //             $qtdFinal = (float) $item['quantidade'];

    //             if (!empty($item['regra_qtd'])) {
    //                 $formula = $item['regra_qtd'];

    //                 if (preg_match_all('/\{\s*(.*?)\s*\}/', $formula, $matches)) {
    //                     foreach ($matches[1] as $index => $variavel) {
    //                         $placeholder = $matches[0][$index];
    //                         $valorNumerico = isset($dadosProjeto[$variavel]) ? (float) $dadosProjeto[$variavel] : 0;
    //                         $formula = str_replace($placeholder, $valorNumerico, $formula);
    //                     }
    //                 }

    //                 // Deixa apenas números e sinais matemáticos
    //                 $formulaLimpa = preg_replace('/[^0-9\.\+\-\*\/\(\)]/', '', $formula);

    //                 if ($formulaLimpa !== '') {
    //                     try {
    //                         // Calcula a matemática com segurança
    //                         $resultadoEval = eval("return ($formulaLimpa);");
    //                         if (is_numeric($resultadoEval)) {
    //                             $qtdFinal = (float) $resultadoEval;
    //                         }
    //                     } catch (\Throwable $e) {
    //                         // Se a fórmula der erro, volta para a Qtd padrão
    //                         $qtdFinal = (float) $item['quantidade']; 
    //                     }
    //                 }
    //             }

    //             // =========================================================
    //             // PASSO C: MODO RAIO-X (DEBUG)
    //             // =========================================================
    //             if ($qtdFinal <= 0) {
    //                 $descricaoFinal = "🔴 ERRO QTD ZERO: " . trim($descricaoFinal) . " (Banco: " . $item['quantidade'] . ")";
    //                 $qtdFinal = 1; // Força a quantidade para 1 só para aparecer na tela
    //             }

    //             // =========================================================
    //             // PASSO D: ANEXA O ITEM PRONTO
    //             // =========================================================
    //             $itensProcessados[] = [
    //                 'descricao' => trim($descricaoFinal),
    //                 'qtd'       => round($qtdFinal, 2), // Arredonda em 2 casas (ex: 7.50)
    //                 'unidade'   => $item['unidade']
    //             ];

    //         } // <-- Fim do Loop foreach

    //         // Só faz o return APÓS terminar de ler todos os itens do Kit!
    //         return $itensProcessados;
    //     }
    //     public function buscarItensProcessados($kitId, $dadosProjeto)
    //     {
    //         // Se não vier um número, retorna vazio (Segurança)
    //         if (!is_numeric($kitId)) {
    //             return [];
    //         }

    //         $db = \Config\Database::connect();

    //         // Busca os itens fazendo o JOIN com a tabela de materiais
    //         $builder = $db->table('kit_itens');
    //         $builder->select('materiais.descricao, materiais.unidade, kit_itens.quantidade');
    //         $builder->join('materiais', 'materiais.id = kit_itens.material_id');
    //         $builder->where('kit_itens.kit_id', $kitId);

    //         $itens = $builder->get()->getResultArray();

    //         if (empty($itens)) {
    //             return [];
    //         }

    //         // Processa Placeholders
    //         $itensProcessados = [];

    //         foreach ($itens as $item) {
    //             $descricaoFinal = $item['descricao'];

    //             // Substituição de Variáveis: {variavel}
    //             if (preg_match_all('/\{(.*?)\}/', $descricaoFinal, $matches)) {
    //                 foreach ($matches[1] as $variavel) {
    //                     if (isset($dadosProjeto[$variavel])) {
    //                         $descricaoFinal = str_replace('{' . $variavel . '}', $dadosProjeto[$variavel], $descricaoFinal);
    //                     } else {
    //                         // Limpa o placeholder se não tiver dado, ou coloca um aviso
    //                         $descricaoFinal = str_replace('{' . $variavel . '}', '', $descricaoFinal);
    //                     }
    //                 }
    //             }

    //             $itensProcessados[] = [
    //                 'descricao' => trim($descricaoFinal), // Trim remove espaços extras se o placeholder ficar vazio
    //                 'qtd'       => $item['quantidade'],
    //                 'unidade'   => $item['unidade']
    //             ];
    //         }

    //         return $itensProcessados;
    //     }
}
