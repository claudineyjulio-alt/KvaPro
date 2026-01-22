<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelasMateriais extends Migration
{
    public function up()
    {
        // 1. Tabela MATERIAIS (O cadastro base)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'descricao'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'unidade'     => ['type' => 'VARCHAR', 'constraint' => 20], // Pç, m, Unid, Kit
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('materiais');

        // 2. Tabela KITS (As "Listas" agrupadoras)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 100], // Ex: "Kit Aterramento 3 Hastes"
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 50],  // Ex: "terra_3_hastes" (Facilita chamar no código)
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kits');

        // 3. Tabela KIT_ITENS (Ligação N:N com quantidade)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kit_id'      => ['type' => 'INT', 'unsigned' => true],
            'material_id' => ['type' => 'INT', 'unsigned' => true],
            'quantidade'  => ['type' => 'DECIMAL', 'constraint' => '10,2'], 
            'regra_qtd'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // Ex: "multiplicar_fases" (opcional futuro)
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kit_id', 'kits', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('material_id', 'materiais', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kit_itens');

        // --- SEED (DADOS INICIAIS PARA TESTE) ---
        $db = \Config\Database::connect();

        // Cadastrar alguns Materiais
        $db->table('materiais')->insertBatch([
            ['id' => 1, 'descricao' => 'Haste de Aterramento Cobreada Alta Camada', 'unidade' => 'Unid'],
            ['id' => 2, 'descricao' => 'Caixa de Inspeção de Solo (PVC/Concreto)', 'unidade' => 'Unid'],
            ['id' => 3, 'descricao' => 'Conector Cabo/Haste (Grampo ou Solda) para cabo {terra_cabo}', 'unidade' => 'Unid'], // OLHA O CORINGA AQUI
            ['id' => 4, 'descricao' => 'Cabo de Cobre Nú {terra_cabo}', 'unidade' => 'm'], // CORINGA
            ['id' => 5, 'descricao' => 'Eletroduto Corrugado/Rígido {terra_tubo}', 'unidade' => 'Unid'], // CORINGA
        ]);

        // Cadastrar Kits
        $db->table('kits')->insertBatch([
            ['id' => 1, 'nome' => 'Aterramento Padrão (3 Hastes)', 'slug' => 'terra_3_hastes'],
            ['id' => 2, 'nome' => 'Aterramento Simples (1 Haste)', 'slug' => 'terra_1_haste'],
        ]);

        // Itens do Kit 3 Hastes
        $db->table('kit_itens')->insertBatch([
            ['kit_id' => 1, 'material_id' => 1, 'quantidade' => 3.00], // 3 Hastes
            ['kit_id' => 1, 'material_id' => 2, 'quantidade' => 1.00], // 1 Caixa
            ['kit_id' => 1, 'material_id' => 3, 'quantidade' => 3.00], // 3 Conectores (Descrição tem coringa)
            ['kit_id' => 1, 'material_id' => 4, 'quantidade' => 0.00], // Cabo (Qtd 0 = Em branco)
            ['kit_id' => 1, 'material_id' => 5, 'quantidade' => 1.00], // Eletroduto
        ]);
        
        // Itens do Kit 1 Haste
         $db->table('kit_itens')->insertBatch([
            ['kit_id' => 2, 'material_id' => 1, 'quantidade' => 1.00], 
            ['kit_id' => 2, 'material_id' => 2, 'quantidade' => 1.00], 
            ['kit_id' => 2, 'material_id' => 3, 'quantidade' => 1.00], 
            ['kit_id' => 2, 'material_id' => 4, 'quantidade' => 0.00], 
            ['kit_id' => 2, 'material_id' => 5, 'quantidade' => 1.00], 
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('kit_itens');
        $this->forge->dropTable('kits');
        $this->forge->dropTable('materiais');
    }
}