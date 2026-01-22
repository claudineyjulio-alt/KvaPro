<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MaterialModel;

class Materiais extends BaseController
{
    public function index()
    {
        $model = new MaterialModel();
        return view('admin/materiais_lista', ['materiais' => $model->findAll()]);
    }

    public function form($id = null)
    {
        $model = new MaterialModel();
        
        // --- MUDANÇA AQUI: Checa se tem dados POST, independente se é 'POST', 'post' ou 'Post' ---
        if ($this->request->getPost()) {
            
            // 1. Pega dados
            $dados = [
                'descricao' => $this->request->getPost('descricao'),
                'unidade'   => $this->request->getPost('unidade'),
            ];

            // 2. Decide ID (Prioriza o hidden do form)
            $idPost = $this->request->getPost('id');
            $idFinal = $idPost ? $idPost : $id;

            // 3. Salva
            // DEBUG DE SEGURANÇA: Se falhar, ele mostra o erro na tela em vez de recarregar
            if ($idFinal) {
                if (!$model->update($idFinal, $dados)) {
                     dd($model->errors()); // Mostra erro se falhar update
                }
            } else {
                if (!$model->insert($dados)) {
                     dd($model->errors()); // Mostra erro se falhar insert
                }
            }

            return redirect()->to('admin/materiais')->with('sucesso', 'Material salvo com sucesso!');
        }

        // Se não tem POST, carrega a view
        $data['item'] = $id ? $model->find($id) : null;
        return view('admin/materiais_form', $data);
    }

    public function excluir($id)
    {
        (new MaterialModel())->delete($id);
        return redirect()->to('admin/materiais');
    }
}