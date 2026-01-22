<?php

namespace App\Models;

use CodeIgniter\Model;

class RegraNormaModel extends Model
{
    protected $table            = 'regras_normas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'concessionaria_id', 
        'prioridade', 
        'descricao', 
        'variavel', 
        'condicao', 
        'valor_min', 
        'valor_max', 
        'norma_resultante_id', 
        'observacao'
    ];

    // Dates
    protected $useTimestamps = false;
}