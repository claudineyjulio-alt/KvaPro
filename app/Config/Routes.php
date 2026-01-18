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


// ==================================================
// 2. ROTAS PRIVADAS (Protegidas pelo Filtro 'auth')
// ==================================================

$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Biblioteca de Símbolos (CRUD)
    $routes->group('simbolos', function($routes) {
        $routes->get('/', 'Simbolos::index');           
        $routes->get('novo', 'Simbolos::novo');         
        $routes->post('salvar', 'Simbolos::salvar');    
        $routes->get('editar/(:num)', 'Simbolos::editar/$1'); 
        $routes->get('excluir/(:num)', 'Simbolos::excluir/$1'); 
    });

    // Editor de Diagramas (CAD)
    $routes->group('diagrama', function($routes) {
        $routes->get('/', 'Diagrama::index');
        // Aqui futuramente entrarão rotas de salvar diagrama, exportar, etc.
    });

});