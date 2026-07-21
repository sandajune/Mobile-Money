<?php
namespace App\Models;
use CodeIgniter\Model;

class EpargneModel extends Model {
    protected $table = 'epargne';
    protected $primaryKey = 'id';
    protected $allowedFields = ['telephone_client', 'solde_epargne', 'date_mise_a_jour'];

    /**
     * Retourne le solde d'épargne d'un client.
     */
    public function getSoldeEpargne(string $telephone): float {
        $epargne = $this->where('telephone_client', $telephone)->first();
        return $epargne ? $epargne['solde_epargne'] : 0.0;
    }

    /**
     * Crée un compte épargne pour un client s'il n'existe pas.
     */
    public function createIfNotExists(string $telephone): void {
        $epargne = $this->where('telephone_client', $telephone)->first();
        if (!$epargne) {
            $this->insert([
                'telephone_client' => $telephone,
                'solde_epargne' => 0.0
            ]);
        }
    }

    /**
     * Ajoute un montant au solde d'épargne d'un client.
     */
    public function ajouterEpargne(string $telephone, float $montant): bool {
        $epargne = $this->where('telephone_client', $telephone)->first();
        
        if (!$epargne) {
            $this->insert([
                'telephone_client' => $telephone,
                'solde_epargne' => $montant
            ]);
            return true;
        }
        
        $nouveauSolde = $epargne['solde_epargne'] + $montant;
        return $this->update($epargne['id'], [
            'solde_epargne' => $nouveauSolde,
            'date_mise_a_jour' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Retire un montant du solde d'épargne d'un client.
     */
    public function retirerEpargne(string $telephone, float $montant): bool {
        $epargne = $this->where('telephone_client', $telephone)->first();
        
        if (!$epargne || $epargne['solde_epargne'] < $montant) {
            return false;
        }
        
        $nouveauSolde = $epargne['solde_epargne'] - $montant;
        return $this->update($epargne['id'], [
            'solde_epargne' => $nouveauSolde,
            'date_mise_a_jour' => date('Y-m-d H:i:s')
        ]);
    }
}
