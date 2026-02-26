<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DimensionamentoModel;
use App\Models\ConcessionariaModel;
use App\Models\TensaoModel;
use App\Models\CaboModel; // NOVO: Traz o model de cabos

class Dimensionamento extends BaseController
{
    protected $dimModel;

    public function __construct()
    {
        $this->dimModel = new DimensionamentoModel();
    }

    // --- LISTAGEM ---
    public function index()
    {
        // Trazendo também ca.terra (Aéreo) e cs.terra (Subterrâneo)
        $data['dimensionamentos'] = $this->dimModel
            ->select('dimensionamento.*, ca.fase as aereo_fase, ca.neutro as aereo_neutro, ca.terra as aereo_terra, cs.fase as sub_fase, cs.neutro as sub_neutro, cs.terra as sub_terra')
            ->join('cabos ca', 'ca.id = dimensionamento.cabo_aereo_id', 'left')
            ->join('cabos cs', 'cs.id = dimensionamento.cabo_subterraneo_id', 'left')
            ->orderBy('categoria', 'ASC')
            ->orderBy('pot_min', 'ASC')
            ->findAll();

        return view('admin/dimensionamento_lista', $data);
    }
    // public function index()
    // {
    //     // NOVO: Faz um JOIN inteligente para trazer as seções dos cabos aéreos e subterrâneos
    //     $data['dimensionamentos'] = $this->dimModel
    //         ->select('dimensionamento.*, ca.fase as aereo_fase, ca.neutro as aereo_neutro, cs.fase as sub_fase, cs.neutro as sub_neutro')
    //         ->join('cabos ca', 'ca.id = dimensionamento.cabo_aereo_id', 'left')
    //         ->join('cabos cs', 'cs.id = dimensionamento.cabo_subterraneo_id', 'left')
    //         ->orderBy('categoria', 'ASC')
    //         ->orderBy('pot_min', 'ASC')
    //         ->findAll();

    //     return view('admin/dimensionamento_lista', $data);
    // }

    // --- FORMULÁRIO (NOVO E EDITAR) ---
    public function form($id = null)
    {
        $concModel   = new ConcessionariaModel();
        $tensaoModel = new TensaoModel();
        $caboModel   = new CaboModel(); // NOVO

        if ($this->request->getPost()) {

            $dados = $this->request->getPost();

            // Blindagem: Se não selecionou um cabo, salva como Nulo
            if (empty($dados['cabo_aereo_id'])) $dados['cabo_aereo_id'] = null;
            if (empty($dados['cabo_subterraneo_id'])) $dados['cabo_subterraneo_id'] = null;

            try {
                if (!empty($dados['id'])) {
                    if (!$this->dimModel->update($dados['id'], $dados)) {
                        $erros = implode(', ', $this->dimModel->errors() ?? ['Erro desconhecido']);
                        throw new \Exception('Erro ao atualizar: ' . $erros);
                    }
                    $msg = 'Padrão atualizado com sucesso!';
                } else {
                    if (!$this->dimModel->insert($dados)) {
                        $erros = implode(', ', $this->dimModel->errors() ?? ['Erro desconhecido']);
                        throw new \Exception('Erro ao cadastrar: ' . $erros);
                    }
                    $msg = 'Novo padrão cadastrado com sucesso!';
                }

                return redirect()->to('admin/dimensionamento')->with('sucesso', $msg);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('erro', $e->getMessage());
            }
        }

        $data['item'] = $id ? $this->dimModel->find($id) : null;

        $data['concessionarias'] = $concModel->orderBy('nome', 'ASC')->findAll();
        $data['tensoes']         = $tensaoModel->orderBy('fase_neutro', 'ASC')->findAll();

        // NOVO: Puxa todos os cabos, ordenando pelo tamanho da Fase (para ficar bonito no select)
        $data['cabos']           = $caboModel->orderBy('CAST(fase AS DECIMAL)', 'ASC')->findAll();

        return view('admin/dimensionamento_form', $data);
    }

    // --- EXCLUSÃO ---
    public function excluir($id)
    {
        if ($this->dimModel->delete($id)) {
            return redirect()->to('admin/dimensionamento')->with('sucesso', 'Padrão excluído com sucesso!');
        }

        return redirect()->to('admin/dimensionamento')->with('erro', 'Erro ao excluir o padrão.');
    }
}

// namespace App\Controllers\Admin;

// use App\Controllers\BaseController;
// use App\Models\DimensionamentoModel;
// use App\Models\ConcessionariaModel;
// use App\Models\TensaoModel;

// class Dimensionamento extends BaseController
// {
//     protected $dimModel;

//     public function __construct()
//     {
//         $this->dimModel = new DimensionamentoModel();
//     }

//     // --- LISTAGEM ---
//     public function index()
//     {
//         // Busca todos ordenados por categoria e subcategoria
//         $data['dimensionamentos'] = $this->dimModel
//             ->orderBy('categoria', 'ASC')
//             ->orderBy('pot_min', 'ASC')
//             ->findAll();

//         return view('admin/dimensionamento_lista', $data);
//     }

//     // --- FORMULÁRIO (NOVO E EDITAR) ---
//     public function form($id = null)
//     {
//         // Models auxiliares para os Dropdowns
//         $concModel   = new \App\Models\ConcessionariaModel();
//         $tensaoModel = new \App\Models\TensaoModel();

//         // Correção: Verifica se tem dados vindo do formulário em vez de checar a string do método
//         if ($this->request->getPost()) {

//             // Coleta os dados do POST
//             $dados = $this->request->getPost();

//             try {
//                 if (!empty($dados['id'])) {
//                     // ATUALIZAR (UPDATE)
//                     if (!$this->dimModel->update($dados['id'], $dados)) {
//                         // Captura erros do Model se falhar
//                         $erros = implode(', ', $this->dimModel->errors() ?? ['Erro desconhecido no banco']);
//                         throw new \Exception('Erro ao atualizar: ' . $erros);
//                     }
//                     $msg = 'Padrão atualizado com sucesso!';
//                 } else {
//                     // INSERIR (INSERT)
//                     if (!$this->dimModel->insert($dados)) {
//                         // Captura erros do Model se falhar
//                         $erros = implode(', ', $this->dimModel->errors() ?? ['Erro desconhecido no banco']);
//                         throw new \Exception('Erro ao cadastrar: ' . $erros);
//                     }
//                     $msg = 'Novo padrão cadastrado com sucesso!';
//                 }

//                 return redirect()->to('admin/dimensionamento')->with('sucesso', $msg);
//             } catch (\Exception $e) {
//                 // Devolve o usuário para o form com os dados preenchidos e a mensagem de erro
//                 return redirect()->back()->withInput()->with('erro', $e->getMessage());
//             }
//         }

//         // Se for requisição normal (GET), carrega os dados para a View
//         $data['item'] = $id ? $this->dimModel->find($id) : null;

//         // Busca as opções para preencher os <select>
//         $data['concessionarias'] = $concModel->orderBy('nome', 'ASC')->findAll();
//         $data['tensoes']         = $tensaoModel->orderBy('fase_neutro', 'ASC')->findAll();

//         return view('admin/dimensionamento_form', $data);
//     }


//     // --- EXCLUSÃO ---
//     public function excluir($id)
//     {
//         if ($this->dimModel->delete($id)) {
//             return redirect()->to('admin/dimensionamento')->with('sucesso', 'Padrão excluído com sucesso!');
//         }

//         return redirect()->to('admin/dimensionamento')->with('erro', 'Erro ao excluir o padrão.');
//     }
// }
