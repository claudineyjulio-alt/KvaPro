<?php namespace App\Models;
use CodeIgniter\Model;

class MaterialModel extends Model {
    protected $table = 'materiais';
    protected $primaryKey = 'id';
    protected $allowedFields = ['descricao', 'unidade'];
    protected $returnType = 'array';
}