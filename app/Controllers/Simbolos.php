<?php

namespace App\Controllers;

use App\Models\SimboloModel;

class Simbolos extends BaseController
{
    private $simboloModel;

    public function __construct()
    {
        $this->simboloModel = new SimboloModel();
    }

    // 1. Tela de Listagem
    public function index()
    {
        $data['simbolos'] = $this->simboloModel->findAll();
        return view('simbolos/index', $data);
    }

    // 2. Tela de Novo Cadastro
    // public function novo()
    // {
    //     return view('simbolos/form', ['titulo' => 'Novo Símbolo']);
    // }

    public function novo()
    {
        // Define modo para a toolbar saber quais botões mostrar
        return view('simbolos/editor_novo', [
            'titulo' => 'Novo Símbolo',
            'modo' => 'simbolo' 
        ]);
    }



    // 3. Tela de Edição
    public function editar($id)
    {
        $simbolo = $this->simboloModel->find($id);
        
        if (!$simbolo) {
            return redirect()->to('/simbolos')->with('erro', 'Símbolo não encontrado');
        }

        return view('simbolos/form', [
            'titulo' => 'Editar Símbolo',
            'simbolo' => $simbolo
        ]);
    }

    // 4. Ação de Salvar (CORRIGIDA)
    public function salvar()
    {
        $id = $this->request->getPost('id');

        // Captura o JSON que o Javascript gerou
        $bornesJson = $this->request->getPost('bornes_json');
        
        // Garante que seja um array válido
        $bornesArray = json_decode((string)$bornesJson, true);
        if (!is_array($bornesArray)) {
            $bornesArray = [];
        }

        // Dados básicos do formulário
        $data = [
            'nome'         => $this->request->getPost('nome'),
            'sigla_padrao' => $this->request->getPost('sigla_padrao'),
            'categoria'    => $this->request->getPost('categoria'),
            'simbolo_svg'  => $this->request->getPost('simbolo_svg'),
            'bornes'       => $bornesArray,
        ];

        if (empty($id)) {
            // === INSERIR (NOVO) ===
            // Inicializamos os campos JSON extras como array vazio []
            // Isso evita o erro "Field is not nullable" do CodeIgniter
            $data['footprint_layout'] = [];
            $data['configuracao_tag'] = [];
            $data['logica_contatos']  = [];

            $this->simboloModel->insert($data);
        } else {
            // === ATUALIZAR (EDITAR) ===
            // Na atualização, não passamos os campos vazios para não 
            // sobrescrever dados que possam existir
            $this->simboloModel->update($id, $data);
        }

        return redirect()->to('/simbolos')->with('sucesso', 'Símbolo salvo com sucesso!');
    }

    // 5. Ação de Excluir
    public function excluir($id)
    {
        $this->simboloModel->delete($id);
        return redirect()->to('/simbolos')->with('sucesso', 'Símbolo excluído!');
    }
}