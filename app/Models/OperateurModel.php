<?php
namespace App\Models;
use CodeIgniter\Model;

class OperateurModel extends Model {
    protected $table = 'operateurs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'nom'];
}
