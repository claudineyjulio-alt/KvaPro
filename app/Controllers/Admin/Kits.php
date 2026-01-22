<?php namespace App\Controllers\Admin;

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
        $kitModel  = new KitModel();
        $itemModel = new KitItemModel();
        $matModel  = new MaterialModel();

        // --- MUDANÇA: Checa se tem dados POST (Igual ao Materiais) ---
        if ($this->request->getPost()) {
            
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
                     dd($kitModel->errors()); // Debug se falhar update
                }
                $kitIdParaSalvarItens = $idFinal;
            } else {
                // INSERT
                if (!$kitModel->insert($dadosKit)) {
                     dd($kitModel->errors()); // Debug se falhar insert
                }
                $kitIdParaSalvarItens = $kitModel->getInsertID();
            }

            // 4. Salva os ITENS (Filhos)
            // Primeiro limpa os anteriores para não duplicar
            $itemModel->where('kit_id', $kitIdParaSalvarItens)->delete();

            $materiais = $this->request->getPost('materiais'); // Array []
            $qtds      = $this->request->getPost('qtds');      // Array []

            if ($materiais && is_array($materiais)) {
                foreach ($materiais as $index => $matId) {
                    // Só salva se tiver um material selecionado
                    if (!empty($matId)) {
                        $itemModel->insert([
                            'kit_id'      => $kitIdParaSalvarItens,
                            'material_id' => $matId,
                            'quantidade'  => $qtds[$index] ?? 1
                        ]);
                    }
                }
            }

            return redirect()->to('admin/kits')->with('sucesso', 'Kit salvo com sucesso!');
        }

        // --- GET: Carrega o formulário ---
        $data['kit'] = $id ? $kitModel->find($id) : null;
        
        // Se for edição, busca os itens atuais desse kit para preencher a tabela
        $data['itensKit'] = $id ? $itemModel->where('kit_id', $id)->findAll() : [];
        
        // Lista de todos materiais para o Dropdown
        $data['todosMateriais'] = $matModel->orderBy('descricao', 'ASC')->findAll();

        return view('admin/kits_form', $data);
    }

    public function excluir($id)
    {
        (new KitModel())->delete($id);
        return redirect()->to('admin/kits');
    }
}