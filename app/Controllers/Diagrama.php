<?php

namespace App\Controllers;

use App\Models\SimboloModel;

class Diagrama extends BaseController
{
    public function index()
    {
        $model = new SimboloModel();
        $todosSimbolos = $model->findAll();

        // Agrupa os símbolos por categoria para a barra lateral
        $simbolosAgrupados = [];
        foreach ($todosSimbolos as $s) {
            $cat = ucfirst($s['categoria'] ?? 'Geral');
            $simbolosAgrupados[$cat][] = $s;
        }

        return view('diagrama/editor', [
            'simbolosPorCategoria' => $simbolosAgrupados
        ]);
    }
}