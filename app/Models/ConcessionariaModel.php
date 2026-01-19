<?php namespace App\Models;
use CodeIgniter\Model;

class ConcessionariaModel extends Model {
    protected $table = 'concessionarias';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'estado'];
    protected $returnType = 'array';
}