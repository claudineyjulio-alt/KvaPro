<?php namespace App\Models;
use CodeIgniter\Model;

class KitItemModel extends Model {
    protected $table = 'kit_itens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kit_id', 'material_id', 'quantidade'];
    protected $returnType = 'array';
}