<?php
namespace App\Models;
use CodeIgniter\Model;

class PromotionModel extends Model {
    protected $table = 'promotions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pourcentage', 'est_actif'];

    public function getpromotionctif(): ?array{
        return $this->where('est_actif', 1)->first();
    }

    public function appliquerpromotion(float $frais): float {
        $promotion = $this->getpromotionctif();
        if ($promotion === null) {
            return $frais;
        }

        $reduction = $frais * ($promotion['pourcentage'] / 100);
        $nouveaufrais = max(0,$frais - $reduction);
        return $nouveaufrais;
    }
}
