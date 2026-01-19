<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    // Adicionei os novos campos aqui
    protected $allowedFields    = [
        'google_id', 'nome', 'email', 'foto', 'nivel', 
        'razao_social', 'cnpj', 'telefone', 'tipo_profissional','area_atuacao',
        'registro_orgao', 'registro_uf', 'registro_numero',
        'email_contato', 'termos_aceite', 'validade', 'cadastro_completo'
    ];

    // Datas automáticas
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
