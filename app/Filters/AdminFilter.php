<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verifica se o usuário tem nível 'admin' na sessão
        if (session()->get('nivel') !== 'admin') {
            return redirect()->to('/')->with('erro', 'Acesso negado.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}