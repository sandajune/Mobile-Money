<?php
namespace App\Models;
use CodeIgniter\Model;

class TransactionModel extends Model {
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['telephone_client', 'telephone_destinataire', 'type_operation', 'montant', 'frais', 'date_transaction', 'frais_inclus', 'groupe_envoi', 'commission'];

    // Récupère l'historique d'un numéro (envois et réceptions)
    public function getHistorique($telephone) {
        return $this->where('telephone_client', $telephone)
                    ->orWhere('telephone_destinataire', $telephone)
                    ->orderBy('date_transaction', 'DESC');
    }
}