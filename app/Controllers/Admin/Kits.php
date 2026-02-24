<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KitModel;
use App\Models\KitItemModel;
use App\Models\MaterialModel;

class Kits extends BaseController
{
    public function index()
    {
        $model = new KitModel();
        // Verifica se a view existe
        if (!is_file(APPPATH . 'Views/admin/kits_lista.php')) {
            die('A view app/Views/admin/kits_lista.php não foi encontrada.');
        }
        return view('admin/kits_lista', ['kits' => $model->findAll()]);
    }

    public function form($id = null)
    {
        $kitModel  = new \App\Models\KitModel();
        $itemModel = new \App\Models\KitItemModel();
        $matModel  = new \App\Models\MaterialModel();

        // --- SALVAR (POST) ---
        if ($this->request->getPost()) {

            $db = \Config\Database::connect();
            $db->transStart(); // Inicia a transação (Tudo ou Nada)

            try {
                // 1. Pega dados do KIT
                $dadosKit = [
                    'nome' => $this->request->getPost('nome'),
                    'slug' => $this->request->getPost('slug'),
                ];

                // 2. Decide ID (Prioriza o hidden do form)
                $idPost = $this->request->getPost('id');
                $idFinal = $idPost ? $idPost : $id;

                $kitIdParaSalvarItens = null;

                // 3. Salva o Kit (Pai)
                if ($idFinal) {
                    // UPDATE
                    if (!$kitModel->update($idFinal, $dadosKit)) {
                        throw new \Exception(implode(', ', $kitModel->errors()));
                    }
                    $kitIdParaSalvarItens = $idFinal;
                } else {
                    // INSERT
                    if (!$kitModel->insert($dadosKit)) {
                        throw new \Exception(implode(', ', $kitModel->errors()));
                    }
                    $kitIdParaSalvarItens = $kitModel->getInsertID();
                }

                // 4. Salva os ITENS (Filhos)
                // Primeiro limpa os anteriores para não duplicar
                $itemModel->where('kit_id', $kitIdParaSalvarItens)->delete();

                // Captura os Arrays do formulário
                $materiais = $this->request->getPost('materiais');
                $qtds      = $this->request->getPost('qtds');
                $regrasQtd = $this->request->getPost('regras_qtd'); // <-- NOVO: Captura as Fórmulas

                $itensData = [];

                if ($materiais && is_array($materiais)) {
                    foreach ($materiais as $index => $matId) {
                        // Só monta a linha se tiver um material selecionado
                        if (!empty($matId)) {
                            // Se a fórmula estiver preenchida, salva. Se não, salva como NULL.
                            $formula = !empty($regrasQtd[$index]) ? trim($regrasQtd[$index]) : null;

                            $itensData[] = [
                                'kit_id'      => $kitIdParaSalvarItens,
                                'material_id' => $matId,
                                'quantidade'  => $qtds[$index] ?? 1,
                                'regra_qtd'   => $formula // <-- NOVO: Injeta a fórmula no banco
                            ];
                        }
                    }
                }

                // Insere todos os itens de uma vez (insertBatch é mais rápido)
                if (!empty($itensData)) {
                    $itemModel->insertBatch($itensData);
                }

                $db->transComplete(); // Confirma a transação no banco

                if ($db->transStatus() === FALSE) {
                    throw new \Exception('Erro no Banco de Dados ao salvar os itens do Kit.');
                }

                return redirect()->to('admin/kits')->with('sucesso', 'Kit salvo com sucesso!');
            } catch (\Exception $e) {
                // Se falhar, reverte tudo e mostra o erro
                return redirect()->back()->withInput()->with('erro', 'Erro ao salvar: ' . $e->getMessage());
            }
        }

        // --- GET: Carrega o formulário ---
        $data['kit'] = $id ? $kitModel->find($id) : null;

        // Se for edição, busca os itens atuais desse kit para preencher a tabela
        $data['itensKit'] = $id ? $itemModel->where('kit_id', $id)->findAll() : [];

        // Lista de todos materiais para o Dropdown
        $data['todosMateriais'] = $matModel->orderBy('descricao', 'ASC')->findAll();

        $data['todosMateriais'] = $matModel->orderBy('descricao', 'ASC')->findAll();

        // ADICIONE ESTE BLOCO AQUI (A mesma lista do outro controller)
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
            'Medições Individuais' => [
                'unidade_classe'    => 'Classe (Residencial, Comercial)',
                'unidade_categoria' => 'Categoria (M, B, T)',
                'unidade_fase'      => 'Seção Cabo Fase (mm²)',
                'unidade_disjuntor' => 'Disjuntor (A)',
            ],
            'Variáveis Virtuais' => [
                'total_medidores' => 'Total de Medidores (Qtd. Unidades)',
            ]
        ];


        return view('admin/kits_form', $data);
    }

    // public function form($id = null)
    // {
    //     $kitModel  = new KitModel();
    //     $itemModel = new KitItemModel();
    //     $matModel  = new MaterialModel();

    //     // --- MUDANÇA: Checa se tem dados POST (Igual ao Materiais) ---
    //     if ($this->request->getPost()) {

    //         // 1. Pega dados do KIT
    //         $dadosKit = [
    //             'nome' => $this->request->getPost('nome'),
    //             'slug' => $this->request->getPost('slug'),
    //         ];

    //         // 2. Decide ID (Prioriza o hidden do form)
    //         $idPost = $this->request->getPost('id');
    //         $idFinal = $idPost ? $idPost : $id;

    //         $kitIdParaSalvarItens = null;

    //         // 3. Salva o Kit (Pai)
    //         if ($idFinal) {
    //             // UPDATE
    //             if (!$kitModel->update($idFinal, $dadosKit)) {
    //                  dd($kitModel->errors()); // Debug se falhar update
    //             }
    //             $kitIdParaSalvarItens = $idFinal;
    //         } else {
    //             // INSERT
    //             if (!$kitModel->insert($dadosKit)) {
    //                  dd($kitModel->errors()); // Debug se falhar insert
    //             }
    //             $kitIdParaSalvarItens = $kitModel->getInsertID();
    //         }

    //         // 4. Salva os ITENS (Filhos)
    //         // Primeiro limpa os anteriores para não duplicar
    //         $itemModel->where('kit_id', $kitIdParaSalvarItens)->delete();

    //         $materiais = $this->request->getPost('materiais'); // Array []
    //         $qtds      = $this->request->getPost('qtds');      // Array []

    //         if ($materiais && is_array($materiais)) {
    //             foreach ($materiais as $index => $matId) {
    //                 // Só salva se tiver um material selecionado
    //                 if (!empty($matId)) {
    //                     $itemModel->insert([
    //                         'kit_id'      => $kitIdParaSalvarItens,
    //                         'material_id' => $matId,
    //                         'quantidade'  => $qtds[$index] ?? 1
    //                     ]);
    //                 }
    //             }
    //         }

    //         return redirect()->to('admin/kits')->with('sucesso', 'Kit salvo com sucesso!');
    //     }

    //     // --- GET: Carrega o formulário ---
    //     $data['kit'] = $id ? $kitModel->find($id) : null;

    //     // Se for edição, busca os itens atuais desse kit para preencher a tabela
    //     $data['itensKit'] = $id ? $itemModel->where('kit_id', $id)->findAll() : [];

    //     // Lista de todos materiais para o Dropdown
    //     $data['todosMateriais'] = $matModel->orderBy('descricao', 'ASC')->findAll();

    //     return view('admin/kits_form', $data);
    // }

    public function excluir($id)
    {
        (new KitModel())->delete($id);
        return redirect()->to('admin/kits');
    }
}
