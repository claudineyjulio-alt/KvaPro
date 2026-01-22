<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarSistemaRegras extends Migration
{
    public function up()
    {
        // ==========================================================
        // 1. TABELA NORMAS (O Catálogo)
        // ==========================================================
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'concessionaria_id' => ['type' => 'INT', 'unsigned' => true], // FK futura para tabela concessionarias
            'codigo'            => ['type' => 'VARCHAR', 'constraint' => 50],  // Ex: NDU001
            'titulo'            => ['type' => 'VARCHAR', 'constraint' => 200], // Ex: Fornecimento BT
            'ativo'             => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('normas');

        // ==========================================================
        // 2. TABELA REGRAS_NORMAS (O Motor de Decisão de Norma)
        // ==========================================================
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'concessionaria_id' => ['type' => 'INT', 'unsigned' => true],
            
            // Prioridade: 1 é a mais alta (testada primeiro)
            'prioridade'        => ['type' => 'INT', 'constraint' => 3, 'default' => 99],
            'descricao'         => ['type' => 'VARCHAR', 'constraint' => 255],
            
            // A Condição (O "SE")
            'variavel'          => ['type' => 'VARCHAR', 'constraint' => 50], // Ex: 'qtd_uc', 'demanda_kva'
            'condicao'          => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '='], // =, >, <, BETWEEN
            'valor_min'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'valor_max'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            
            // A Resposta (O "ENTÃO")
            'norma_resultante_id' => ['type' => 'INT', 'unsigned' => true], // Se bater a regra, usa esta norma
            
            'observacao'        => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('norma_resultante_id', 'normas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('regras_normas');

        // ==========================================================
        // 3. TABELA REGRAS_MATERIAIS (O Motor de Decisão de Kits)
        // ==========================================================
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'concessionaria_id' => ['type' => 'INT', 'unsigned' => true],
            
            // Tipo: Para saber se essa regra define o poste ou o aterramento
            'tipo_kit'          => ['type' => 'VARCHAR', 'constraint' => 50], // Ex: 'infra', 'aterramento', 'medicao'
            
            'prioridade'        => ['type' => 'INT', 'constraint' => 3, 'default' => 99],
            'descricao'         => ['type' => 'VARCHAR', 'constraint' => 255],
            
            // A Condição (O "SE")
            'variavel'          => ['type' => 'VARCHAR', 'constraint' => 50], // Ex: 'travessia', 'fases'
            'condicao'          => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '='],
            'valor_min'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'valor_max'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            
            // A Resposta (O "ENTÃO")
            'kit_id'            => ['type' => 'INT', 'unsigned' => true], // Se bater a regra, usa este kit
            
            'observacao'        => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        // Garante que só podemos criar regras para KITS que existem
        $this->forge->addForeignKey('kit_id', 'kits', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('regras_materiais');
    }

    public function down()
    {
        $this->forge->dropTable('regras_materiais');
        $this->forge->dropTable('regras_normas');
        $this->forge->dropTable('normas');
    }
}