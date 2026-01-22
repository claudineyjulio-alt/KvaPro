<?php namespace App\Controllers\Admin;

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
        foreach($kits as $k) {
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

        return view('admin/regras_materiais_form', $data);
    }

    public function excluir($id)
    {
        $model = new RegraMaterialModel();
        $model->delete($id);
        return redirect()->to('admin/regras-materiais')->with('sucesso', 'Regra excluída.');
    }
}