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

            if ($usuario) {
                $model->update($usuario['id'], [
                    'nome' => $nome,
                    'foto' => $foto,
                    'google_id' => $googleId
                ]);
                $usuario['nome'] = $nome;
                $usuario['foto'] = $foto;
            } else {
                $novoId = $model->insert([
                    'google_id' => $googleId,
                    'nome'      => $nome,
                    'email'     => $email,
                    'foto'      => $foto,
                    'nivel'     => 'demo'
                ]);
                
                if (!$novoId) throw new \Exception('Erro ao criar usuário.');
                $usuario = $model->find($novoId);
            }

            // Cria a Sessão
            session()->set([
                'id'         => $usuario['id'],
                'nome'       => $usuario['nome'],
                'email'      => $usuario['email'],
                'foto'       => $usuario['foto'],
                'nivel'      => $usuario['nivel'],
                'isLoggedIn' => true,
            ]);

            // Retorna JSON para o JavaScript redirecionar
            return $this->response->setJSON(['success' => true, 'redirectUrl' => base_url('dashboard')]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}