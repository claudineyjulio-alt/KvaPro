<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSimbolosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                // Ex: "Bobina de Contator", "Botão NA"
            ],
            'sigla_padrao' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                // Ex: "K", "S", "Q", "F"
            ],
            'categoria' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                // Ex: 'bobina', 'comando', 'potencia', 'conexao', 'referencia'
            ],
            'simbolo_svg' => [
                'type' => 'TEXT', 
                'null' => true,
                // O código <path...> ou o nome do arquivo SVG
            ],
            // Campos JSON para dados estruturados
            'footprint_layout' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'configuracao_tag' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'bornes' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'logica_contatos' => [
                'type' => 'JSON',
                'null' => true,
            ],
            // Datas de controle
            'created_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('simbolos');
    }

    public function down()
    {
        $this->forge->dropTable('simbolos');
    }
}