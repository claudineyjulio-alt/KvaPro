<?php

namespace App\Models;

use CodeIgniter\Model;

class DimensionamentoModel extends Model
{
    protected $table = 'dimensionamento';
    protected $primaryKey = 'id';

    // Atualizado com os novos campos separados de cabos
    protected $allowedFields = [
        'id_concessionaria',
        'id_tensao',
        'categoria',
        'subcategoria',
        'pot_min',
        'pot_max',
        'corrente_disjuntor',
        'unidade',
        'tipo_disjuntor',
        'cabo', // Mantido caso tenha dados legados, pode remover se já apagou a coluna
        'eletroduto',
        'qtd_cabos_fase', // NOVO
        'secao_fase',     // NOVO
        'secao_neutro'    // NOVO
    ];

    protected $returnType = 'array';

    // Busca específica para o filtro (O findAll já traz todas as colunas novas)
    public function buscarPorConfig($concessionariaId, $tensaoId)
    {
        return $this->where('id_concessionaria', $concessionariaId)
            ->where('id_tensao', $tensaoId)
            ->orderBy('categoria', 'ASC')
            ->orderBy('corrente_disjuntor', 'ASC')
            ->findAll();
    }
}
