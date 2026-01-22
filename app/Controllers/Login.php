<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel; // Garante que o Model seja carregado

class Login extends BaseController
{
    // Carrega a view com o visual novo
    public function index()
    {
        // Se já logado, tchau login
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('login'); // Nome do arquivo na pasta Views
    }

    // Seu método de validação via Token (Mantido intacto)
    public function callback()
    {
        $responseArr = ['success' => false];

        try {
            // O Javascript vai mandar isso via POST
            $credential = $this->request->getPost('credential');

            if (!$credential) {
                throw new \Exception('Token não recebido.');
            }

            // Pega o ID do .env
            $client_id = getenv('GOOGLE_CLIENT_ID'); 
            
            if (empty($client_id)) {
                throw new \Exception('Configure o GOOGLE_CLIENT_ID no .env');
            }

            // Valida no Google via cURL (Super Seguro)
            $client = \Config\Services::curlrequest();
            
            $googleResponse = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query' => ['id_token' => $credential],
                'http_errors' => false, 
                'verify' => false 
            ]);

            if ($googleResponse->getStatusCode() !== 200) {
                log_message('error', 'Google API Erro: ' . $googleResponse->getBody());
                throw new \Exception('Token rejeitado pelo Google.');
            }

            $payload = json_decode($googleResponse->getBody(), true);

            if (!isset($payload['aud']) || $payload['aud'] !== $client_id) {
                throw new \Exception('Tentativa de falsificação de cliente.');
            }

            // Dados do Google
            $googleId = $payload['sub'];
            $email    = $payload['email'];
            $nome     = $payload['name'];
            $foto     = $payload['picture'] ?? null;

            // Manipulação do Banco
            $model = new UsuarioModel();
            $usuario = $model->where('email', $email)->first();
            
            $isNewUser = false;

            if ($usuario) {
                // Atualiza dados básicos do Google
                $model->update($usuario['id'], [
                    'foto' => $foto,
                    'google_id' => $googleId
                ]);
            } else {
                // Cria novo usuário
                $novoId = $model->insert([
                    'google_id' => $googleId,
                    'nome'      => $nome,
                    'email'     => $email,
                    'foto'      => $foto,
                    'nivel'     => 'teste', // Nível padrão
                    'validade'  => null,    // Indeterminado
                    'cadastro_completo' => 0 // Marca que precisa completar
                ]);
                $usuario = $model->find($novoId);
                $isNewUser = true;
            }

            // Define Sessão
            session()->set([
                'id'         => $usuario['id'],
                'nome'       => $usuario['nome'],
                'email'      => $usuario['email'],
                'foto'       => $usuario['foto'],
                'nivel'      => $usuario['nivel'],
                'isLoggedIn' => true,
            ]);

            // DECISÃO DE ROTA
            // Se o cadastro não estiver completo, manda para o formulário
            if ($usuario['cadastro_completo'] == 0) {
                 return $this->response->setJSON(['success' => true, 'redirectUrl' => base_url('cadastro')]);
            }

            // Se já estiver completo, manda para o dashboard
            return $this->response->setJSON(['success' => true, 'redirectUrl' => base_url('dashboard')]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- NOVOS MÉTODOS PARA O FORMULÁRIO ---
    // MÉTODO 1: Tela de Cadastro Inicial (Obrigatória)
    public function cadastro()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $model = new UsuarioModel();
        $user = $model->find(session()->get('id'));

        // Se já completou, joga pro dashboard (não deixa refazer o "primeiro passo")
        if ($user['cadastro_completo'] == 1) return redirect()->to('/dashboard');

        // Configuração para MODO CADASTRO
        $data = [
            'user' => $user,
            'is_editing' => false, // Importante: define que é o primeiro acesso
            'titulo' => 'Bem-vindo ao KvaPro',
            'subtitulo' => 'Finalize seu perfil profissional para começar.',
            'botao_texto' => 'Salvar e Acessar KvaPro'
        ];

        return view('cadastro_completar', $data);
    }

    // MÉTODO 2: Tela de Edição (Opcional - Acessada pelo Menu)
    public function perfil()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $model = new UsuarioModel();
        $user = $model->find(session()->get('id'));

        // Configuração para MODO EDIÇÃO
        $data = [
            'user' => $user,
            'is_editing' => true, // Define que é edição
            'titulo' => 'Meu Perfil',
            'subtitulo' => 'Mantenha seus dados profissionais atualizados.',
            'botao_texto' => 'Salvar Alterações'
        ];

        return view('cadastro_completar', $data);
    }

    // MÉTODO 3: Salva (Serve para os dois)
    public function salvarCadastro()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        // Na edição, o termo não vem no post, então removemos a validação dele se for edição
        $regras = [
            'nome' => 'required|min_length[3]',
            'tipo_profissional' => 'required',
        ];

        // Se for o cadastro inicial (verifica se veio o campo 'is_editing' hidden do form)
        $is_editing = $this->request->getPost('is_editing');
        
        if (!$is_editing) {
            $regras['termos'] = 'required'; // Só obriga o termo no primeiro cadastro
        }

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'razao_social' => $this->request->getPost('razao_social'),
            'cnpj' => $this->request->getPost('cnpj'),
            'telefone' => $this->request->getPost('telefone'),
            'email_contato' => $this->request->getPost('email_contato'),
            'tipo_profissional' => $this->request->getPost('tipo_profissional'),
            'area_atuacao' => $this->request->getPost('area_atuacao'),
            'registro_orgao' => $this->request->getPost('registro_orgao'),
            'registro_uf' => $this->request->getPost('registro_uf'),
            'registro_numero' => $this->request->getPost('registro_numero'),
            'cadastro_completo' => 1
        ];

        // Só atualiza a data de aceite se o usuário marcou o checkbox (cadastro inicial)
        if ($this->request->getPost('termos')) {
            $data['termos_aceite'] = date('Y-m-d H:i:s');
        }

        $model = new UsuarioModel();
        $model->update(session()->get('id'), $data);

        // Redirecionamento inteligente
        if ($is_editing) {
            return redirect()->to('/perfil')->with('msg', 'Dados atualizados com sucesso!');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}