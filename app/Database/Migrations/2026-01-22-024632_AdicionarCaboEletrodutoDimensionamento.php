<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdicionarCaboEletrodutoDimensionamento extends Migration
{
    public function up()
    {
        $fields = [
            'cabo' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'corrente_disjuntor' // Organiza visualmente
            ],
            'eletroduto' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'cabo'
            ],
        ];

        $this->forge->addColumn('dimensionamento', $fields);

        // Opcional: Já preencher os dados existentes com base no que você me mandou
        // Isso facilita para não ficar tudo vazio no início
        $db = \Config\Database::connect();
        
        // M1 - 40A
        $db->query("UPDATE dimensionamento SET cabo = '2#6(6)mm²', eletroduto = 'Ø 1\"' WHERE subcategoria = 'M1'");
        
        // M2 - 63A
        $db->query("UPDATE dimensionamento SET cabo = '2#16(16)mm²', eletroduto = 'Ø 1.1/4\"' WHERE subcategoria = 'M2'");
        
        // B1 - 40A
        $db->query("UPDATE dimensionamento SET cabo = '3#6(6)mm²', eletroduto = 'Ø 1\"' WHERE subcategoria = 'B1'");
        
        // B2 - 63A
        $db->query("UPDATE dimensionamento SET cabo = '3#16(16)mm²', eletroduto = 'Ø 1.1/4\"' WHERE subcategoria = 'B2'");
    }

    public function down()
    {
        $this->forge->dropColumn('dimensionamento', ['cabo', 'eletroduto']);
    }
}