<?php namespace App\Models;
use CodeIgniter\Model;

class TensaoModel extends Model {
    protected $table = 'tensoes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['classe', 'descricao', 'fase_neutro', 'fase_fase'];
    protected $returnType = 'array';
}