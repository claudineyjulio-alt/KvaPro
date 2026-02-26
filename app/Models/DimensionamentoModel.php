<?php

namespace App\Models;

use CodeIgniter\Model;

class DimensionamentoModel extends Model
{
    protected $table = 'dimensionamento';
    protected $primaryKey = 'id';

    // Atualizado com os novos campos separados de cabos
    protected $allowedFields    = [
        'id_concessionaria',
        'id_tensao',
        'categoria',
        'subcategoria',
        'pot_min',
        'pot_max',
        'unidade',
        'corrente_disjuntor',
        'qtd_cabos_fase',
        'cabo_aereo_id',
        'cabo_subterraneo_id',
        'eletroduto',
        'tipo_disjuntor',
        'norma',
        'tabela_norma',
        'descricao_material',
        'carga_a_considerar'
    ];
    // protected $allowedFields = [
    //     'id_concessionaria',
    //     'id_tensao',
    //     'categoria',
    //     'subcategoria',
    //     'norma',
    //     'tabela_norma',
    //     'carga_a_considerar',
    //     'pot_min',
    //     'pot_max',
    //     'corrente_disjuntor',
    //     'unidade',
    //     'tipo_disjuntor',
    //     'eletroduto',
    //     'qtd_cabos_fase',
    //     'secao_fase',
    //     'secao_neutro'
    // ];

    protected $returnType = 'array';

    // Busca específica para o filtro (O findAll já traz todas as colunas novas)
    public function buscarPorConfig($concessionariaId, $tensaoId)
    {
        return $this->select('dimensionamento.*, ca.fase as aereo_fase, ca.neutro as aereo_neutro, ca.terra as aereo_terra, cs.fase as sub_fase, cs.neutro as sub_neutro, cs.terra as sub_terra')
            ->join('cabos ca', 'ca.id = dimensionamento.cabo_aereo_id', 'left')
            ->join('cabos cs', 'cs.id = dimensionamento.cabo_subterraneo_id', 'left')
            ->where('dimensionamento.id_concessionaria', $concessionariaId)
            ->where('dimensionamento.id_tensao', $tensaoId)
            ->orderBy('dimensionamento.categoria', 'ASC')
            ->orderBy('dimensionamento.corrente_disjuntor', 'ASC')
            ->findAll();
    }

    // public function buscarPorConfig($concessionariaId, $tensaoId)
    // {
    //     return $this->where('id_concessionaria', $concessionariaId)
    //         ->where('id_tensao', $tensaoId)
    //         ->orderBy('categoria', 'ASC')
    //         ->orderBy('corrente_disjuntor', 'ASC')
    //         ->findAll();
    // }
}
