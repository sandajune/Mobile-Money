<?php
namespace App\Models;
use CodeIgniter\Model;

class BaremeFraisModel extends Model {
    protected $table = 'bareme_frais';
    protected $primaryKey = 'id';
    protected $allowedFields = ['type_operation', 'montant_min', 'montant_max', 'frais'];

    /**
     * Retourne la tranche de frais applicable pour un montant et un type d'opération.
     */
    public function getFraisForMontant(float $montant, string $type): ?array {
        return $this->where('type_operation', $type)
                    ->where('montant_min <=', $montant)
                    ->where('montant_max >=', $montant)
                    ->first();
    }
}
