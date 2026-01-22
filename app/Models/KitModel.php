<?php namespace App\Models;
use CodeIgniter\Model;

class KitModel extends Model {
    protected $table = 'kits';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'slug'];
    protected $returnType = 'array';
}