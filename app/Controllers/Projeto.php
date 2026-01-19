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

        return view('projeto_novo', $data);
    }

    public function api_dimensionamento()
    {
        if (!session()->get('isLoggedIn')) return $this->response->setJSON([]);
        $concessionaria = $this->request->getGet('concessionaria');
        $tensao = $this->request->getGet('tensao');
        
        $model = new DimensionamentoModel();
        return $this->response->setJSON($model->buscarPorConfig($concessionaria, $tensao));
    }

    public function diagrama()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        return view('diagrama_unifilar');
    }

    public function salvar()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        // Recebe todos os dados da tela única (incluindo a lista de unidades detalhadas)
        $dados = $this->request->getPost();
        
        // Gera a view de download
        return view('projeto_resultado', ['dados' => $dados]);
    }
}