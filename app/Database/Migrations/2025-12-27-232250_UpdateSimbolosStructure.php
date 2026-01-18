<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSimbolosStructure extends Migration
{
    public function up()
    {
        // 1. Adicionar coluna TIPO e Posicionamento da TAG
        $fields = [
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['mestre', 'escravo', 'comum'],
                'default'    => 'comum',
                'after'      => 'categoria'
            ],
            'tag_x' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'simbolo_svg' // Posição X padrão da tag
            ],
            'tag_y' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => -20.00, // Padrão um pouco acima
                'after'      => 'tag_x'
            ],
            'tag_alinhamento' => [
                'type'       => 'ENUM',
                'constraint' => ['left', 'center', 'right'],
                'default'    => 'center',
                'after'      => 'tag_y'
            ]
        ];
        
        $this->forge->addColumn('simbolos', $fields);

        // 2. (Opcional) Podemos remover 'configuracao_tag' antigo se for JSON, 
        // já que agora temos colunas dedicadas, ou manter por compatibilidade.
        // $this->forge->dropColumn('simbolos', 'configuracao_tag');
    }

    public function down()
    {
        $this->forge->dropColumn('simbolos', ['tipo', 'tag_x', 'tag_y', 'tag_alinhamento']);
    }
}