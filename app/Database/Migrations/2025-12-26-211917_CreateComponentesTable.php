<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComponentesTable extends Migration
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
                // Ex: "Eletroduto Galvanizado 2 pol"
            ],
            'descricao' => [
                'type'       => 'TEXT',
                'null'       => true,
                // Ex: "Parede grossa, norma NBR..."
            ],
            'unidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                // Ex: "m", "pç", "barra", "kg"
            ],
            'preco_referencia' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                // Para estimativas de orçamento
            ],
            'categoria' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                // Ex: "Infraestrutura", "Cabeamento", "Proteção"
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'deleted_at' => [ // Soft Delete (opcional, bom para não perder histórico)
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('componentes');
    }

    public function down()
    {
        $this->forge->dropTable('componentes');
    }
}