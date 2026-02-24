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

    // ATUALIZADO: Removemos os campos de condição (variavel, valor_min, etc) 
    // e adicionamos o campo 'nome'
    protected $allowedFields    = [
        'concessionaria_id',
        'nome',
        'tipo_kit',
        'prioridade',
        'descricao',
        'kit_id',
        'observacao'
    ];

    // Dates (Se você adicionou created_at e updated_at no banco, 
    // mude para true. Caso contrário, mantenha false)
    protected $useTimestamps = false;
}
