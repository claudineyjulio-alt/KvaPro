<?php namespace App\Models;
use CodeIgniter\Model;

class DimensionamentoModel extends Model {
    protected $table = 'dimensionamento';
    protected $primaryKey = 'id';
    
    // Adicionei 'cabo' e 'eletroduto' aqui para permitir INSERT/UPDATE futuros
    protected $allowedFields = [
        'id_concessionaria', 'id_tensao', 'categoria', 'subcategoria', 
        'pot_min', 'pot_max', 'corrente_disjuntor', 'unidade', 'tipo_disjuntor',
        'cabo', 'eletroduto' 
    ];
    
    protected $returnType = 'array';

    // Busca específica para o filtro
    public function buscarPorConfig($concessionariaId, $tensaoId) {
        return $this->where('id_concessionaria', $concessionariaId)
                    ->where('id_tensao', $tensaoId)
                    ->orderBy('categoria', 'ASC')
                    ->orderBy('corrente_disjuntor', 'ASC')
                    ->findAll();
    }
}