<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Aqui futuramente buscaremos os projetos do banco
        // Por enquanto, dados fictícios para o layout
        $data = [
            'projetos_recentes' => [
                ['nome' => 'Painel Principal - Fábrica A', 'data' => 'Há 2 horas', 'status' => 'Em Andamento'],
                ['nome' => 'Quadro de Bombas', 'data' => 'Ontem', 'status' => 'Concluído'],
                ['nome' => 'Automação Esteira 02', 'data' => '25/12/2025', 'status' => 'Revisão'],
            ]
        ];

        return view('dashboard/home', $data);
    }
}