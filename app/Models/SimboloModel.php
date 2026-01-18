<?php

namespace App\Models;

use CodeIgniter\Model;

class SimboloModel extends Model
{
    protected $table            = 'simbolos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'nome', 
        'sigla_padrao', 
        'categoria', 
        'simbolo_svg', 
        'footprint_layout', 
        'configuracao_tag', 
        'bornes', 
        'logica_contatos'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // A CORREÇÃO: Adicionamos '?' antes de 'json'
    // Isso diz: "Trate como JSON, mas se for NULL, aceite como NULL"
    protected array $casts = [
        'footprint_layout' => '?json',
        'configuracao_tag' => '?json',
        'bornes'           => '?json', 
        'logica_contatos'  => '?json',
    ];
}