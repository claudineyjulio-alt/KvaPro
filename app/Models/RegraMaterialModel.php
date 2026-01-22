<?php

namespace App\Models;

use CodeIgniter\Model;

class RegraMaterialModel extends Model
{
    protected $table            = 'regras_materiais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'concessionaria_id', 
        'tipo_kit', 
        'prioridade', 
        'descricao', 
        'variavel', 
        'condicao', 
        'valor_min', 
        'valor_max', 
        'kit_id', 
        'observacao'
    ];

    // Dates
    protected $useTimestamps = false;
}