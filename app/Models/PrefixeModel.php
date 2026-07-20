<?php
namespace App\Models;
use CodeIgniter\Model;

class PrefixeModel extends Model {
    protected $table = 'config_prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom_operateur', 'prefixe'];
}