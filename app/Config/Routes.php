<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================================================
// 1. ROTAS PÚBLICAS (Abertas para todos)
// ==================================================

$routes->get('/', 'Login::index'); // Tela de Login
$routes->get('login/google', 'Login::google'); // Botão Google
$routes->match(['get', 'post'], 'login/callback', 'Login::callback'); // Retorno Google
$routes->get('logout', 'Login::logout'); // Sair
$routes->get('cadastro', 'Login::cadastro');
$routes->post('cadastro/salvar', 'Login::salvarCadastro');
$routes->get('perfil', 'Login::perfil');// Rota para edição (Usa a mesma lógica base)
$routes->get('projeto/novo', 'Projeto::novo');
// Mudança aqui: O form agora vai para 'detalhar'
$routes->post('projeto/detalhar', 'Projeto::detalhar'); 
// Nova rota para o passo final
$routes->post('projeto/salvar', 'Projeto::salvar'); 
$routes->get('projeto/api/dimensionamento', 'Projeto::api_dimensionamento');
$routes->get('projeto/diagrama', 'Projeto::diagrama');

// ==================================================
// 2. ROTAS PRIVADAS (Protegidas pelo Filtro 'auth')
// ==================================================

$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');



});