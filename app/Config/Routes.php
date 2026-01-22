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
$routes->match(['get', 'post'], 'projeto/novo', 'Projeto::novo');
// Mudança aqui: O form agora vai para 'detalhar'
$routes->post('projeto/detalhar', 'Projeto::detalhar'); 
// Nova rota para o passo final
$routes->post('projeto/salvar', 'Projeto::salvar'); 
$routes->get('projeto/api/dimensionamento', 'Projeto::api_dimensionamento');
// rota para aceitar POST
$routes->match(['get', 'post'], 'projeto/diagrama', 'Projeto::diagrama');
$routes->post('projeto/lista-materiais', 'Projeto::listaMateriais');

// ==================================================
// 2. ROTAS PRIVADAS (Protegidas pelo Filtro 'auth')
// ==================================================

$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');


    $routes->group('admin', ['filter' => 'admin'], function($routes) {
        
        // --- MATERIAIS ---
        $routes->get('materiais', 'Admin\Materiais::index');
        
        // Rota para ABRIR o formulário (GET)
        $routes->get('materiais/novo', 'Admin\Materiais::form');
        $routes->get('materiais/editar/(:num)', 'Admin\Materiais::form/$1');
        
        // Rota para SALVAR o formulário (POST)
        // Nota: Aponta para o mesmo método 'form', mas força o verbo POST
        $routes->post('materiais/novo', 'Admin\Materiais::form');
        $routes->post('materiais/editar/(:num)', 'Admin\Materiais::form/$1');

        $routes->get('materiais/excluir/(:num)', 'Admin\Materiais::excluir/$1');


        // --- KITS ---
        $routes->get('kits', 'Admin\Kits::index');
        
        // Abrir (GET)
        $routes->get('kits/novo', 'Admin\Kits::form');
        $routes->get('kits/editar/(:num)', 'Admin\Kits::form/$1');
        
        // Salvar (POST)
        $routes->post('kits/novo', 'Admin\Kits::form');
        $routes->post('kits/editar/(:num)', 'Admin\Kits::form/$1');
        
        $routes->get('kits/excluir/(:num)', 'Admin\Kits::excluir/$1');
    });


});


