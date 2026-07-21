<?php
namespace App\Models;
use CodeIgniter\Model;

class PromotionModel extends Model {
    protected $table = 'promotions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pourcentage', 'est_actif'];

    /**
     * Retourne la promotion active actuelle.
     */
    public function getActivePromotion(): ?array {
        return $this->where('est_actif', 1)->first();
    }

    /**
     * Applique la promotion active à un montant de frais.
     */
    public function applyPromotion(float $frais): float {
        $promotion = $this->getActivePromotion();
        
        if ($promotion === null) {
            return $frais;
        }

        $reduction = $frais * ($promotion['pourcentage'] / 100);
        $nouveauxFrais = max(0, $frais - $reduction);
        
        return $nouveauxFrais;
    }
}
