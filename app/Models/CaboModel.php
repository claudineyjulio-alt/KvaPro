<?php
namespace App\Models;
use CodeIgniter\Model;

class CaboModel extends Model
{
    protected $table            = 'cabos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['fase', 'neutro', 'terra', 'isolacao'];
}