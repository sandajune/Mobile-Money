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
     * Calcule le montant de la promotion (pourcentage des frais).
     */
    public function calculerMontantPromotion(float $frais): float {
        $promotion = $this->getActivePromotion();
        
        if ($promotion === null) {
            return 0.0;
        }

        return $frais * ($promotion['pourcentage'] / 100);
    }

    /**
     * Applique la promotion en créditant le montant sur l'épargne du client.
     * Retourne les frais inchangés (la promotion va en épargne).
     */
    public function applyPromotion(float $frais, string $telephoneClient): float {
        $montantPromotion = $this->calculerMontantPromotion($frais);
        
        if ($montantPromotion > 0) {
            $epargneModel = new EpargneModel();
            $epargneModel->createIfNotExists($telephoneClient);
            $epargneModel->ajouterEpargne($telephoneClient, $montantPromotion);
        }
        
        return $frais;
    }
}
