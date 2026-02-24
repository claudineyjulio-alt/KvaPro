<?php

namespace App\Models;

use CodeIgniter\Model;

class DefinicaoRegraMatModel extends Model
{
    protected $table            = 'definicao_regra_mat';
    protected $primaryKey       = 'id';
    
    // Como é uma tabela de relacionamento/detalhes, geralmente retornamos array mesmo
    protected $returnType       = 'array';
    
    // Campos que o CodeIgniter tem permissão para inserir/atualizar
    protected $allowedFields    = [
        'regra_id', 
        'variavel', 
        'condicao', 
        'valor_min', 
        'valor_max'
    ];

    // Não precisamos de timestamps de criação/atualização nesta tabela filha, 
    // pois a tabela pai (regras_materiais) já controla quando a regra foi modificada.
    protected $useTimestamps    = false;
}