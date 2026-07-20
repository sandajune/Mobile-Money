<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\TransactionModel;

class RetraitController extends BaseController
{
    public function create()
    {
        return view('client/retrait/create');
    }

    /** AJAX – retourne les frais pour un montant donné (GET ?montant=X). */
    public function frais()
    {
        $montant = (float) $this->request->getGet('montant');
        $fraisInclus = $this->request->getGet('frais_inclus') === '1';
        
        if ($montant <= 0) {
            return $this->response->setJSON(['frais' => null, 'error' => 'Montant invalide']);
        }
        
        // Si frais inclus, le montant saisi est le montant total débité
        // Il faut trouver la tranche basée sur le montant net (montant - frais)
        if ($fraisInclus) {
            // Pour déterminer les frais, on itère pour trouver la bonne tranche
            // Le montant net doit être tel que: montant_net + frais(montant_net) = montant_total
            $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');
            if (!$tranche) {
                return $this->response->setJSON(['frais' => null, 'error' => 'Hors barème']);
            }
            
            // Approximation: on suppose que les frais sont basés sur le montant net
            // On utilise le montant saisi comme référence pour la tranche (simplification)
            $frais = $tranche['frais'];
            $montantNet = $montant - $frais;
            
            return $this->response->setJSON([
                'frais' => $frais,
                'montant_net' => max(0, $montantNet),
                'montant_total' => $montant,
                'frais_inclus' => true
            ]);
        } else {
            // Comportement normal: frais s'ajoutent au montant
            $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');
            if (!$tranche) {
                return $this->response->setJSON(['frais' => null, 'error' => 'Hors barème']);
            }
            
            $frais = $tranche['frais'];
            $montantTotal = $montant + $frais;
            
            return $this->response->setJSON([
                'frais' => $frais,
                'montant_net' => $montant,
                'montant_total' => $montantTotal,
                'frais_inclus' => false
            ]);
        }
    }

    public function preview()
    {
        $montant = $this->request->getPost('montant');
        $fraisInclus = $this->request->getPost('frais_inclus') === '1';

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être un nombre supérieur à 0.');
        }

        $montant  = (float) $montant;
        $tranche  = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');

        if (!$tranche) {
            return redirect()->back()->with('error', 'Aucun barème de frais disponible pour ce montant. Vérifiez les tarifs en vigueur.');
        }

        $frais = $tranche['frais'];
        
        if ($fraisInclus) {
            // Le montant saisi est le total débité (frais inclus)
            $totalDebit = $montant;
            $montantNet = $montant - $frais;
        } else {
            // Le montant saisi est le montant net, frais s'ajoutent
            $montantNet = $montant;
            $totalDebit = $montant + $frais;
        }
        
        $client     = (new ClientModel())->find(session()->get('id'));

        if ($client['solde'] < $totalDebit) {
            return redirect()->back()->with('error',
                'Solde insuffisant. Il vous faut ' . number_format($totalDebit, 0, '', ' ') . ' Ar (montant + frais ' . number_format($frais, 0, '', ' ') . ' Ar), mais votre solde est de ' . number_format($client['solde'], 0, '', ' ') . ' Ar.');
        }

        return view('client/retrait/confirm', [
            'montant'       => $montantNet,
            'frais'         => $frais,
            'total_debit'   => $totalDebit,
            'solde_actuel'  => $client['solde'],
            'nouveau_solde' => $client['solde'] - $totalDebit,
            'frais_inclus'  => $fraisInclus,
        ]);
    }

    public function store()
    {
        $montant = $this->request->getPost('montant');
        $fraisInclus = $this->request->getPost('frais_inclus') === '1';

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->to(base_url('client/retrait'))->with('error', 'Montant invalide.');
        }

        $montant = (float) $montant;
        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');

        if (!$tranche) {
            return redirect()->to(base_url('client/retrait'))->with('error', 'Aucun barème de frais disponible pour ce montant.');
        }

        $frais = $tranche['frais'];
        
        if ($fraisInclus) {
            $totalDebit = $montant;
            $montantNet = $montant - $frais;
        } else {
            $montantNet = $montant;
            $totalDebit = $montant + $frais;
        }
        
        $clientId   = session()->get('id');
        $telephone  = session()->get('telephone');
        $db         = \Config\Database::connect();

        $db->transBegin();
        try {
            $clientModel = new ClientModel();
            $client      = $clientModel->find($clientId);

            if ($client['solde'] < $totalDebit) {
                $db->transRollback();
                return redirect()->to(base_url('client/retrait'))->with('error', 'Solde insuffisant.');
            }

            $nouveauSolde = $client['solde'] - $totalDebit;
            $clientModel->update($clientId, ['solde' => $nouveauSolde]);

            (new TransactionModel())->insert([
                'telephone_client'       => $telephone,
                'telephone_destinataire' => null,
                'type_operation'         => 'retrait',
                'montant'                => $montantNet,
                'frais'                  => $frais,
                'frais_inclus'           => $fraisInclus ? 1 : 0,
            ]);

            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('client/retrait'))->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->to(base_url('client/dashboard'))
            ->with('success', 'Retrait de ' . number_format($montantNet, 0, '', ' ') . ' Ar effectué. Frais : ' . number_format($frais, 0, '', ' ') . ' Ar. Total débité : ' . number_format($totalDebit, 0, '', ' ') . ' Ar. Nouveau solde : ' . number_format($nouveauSolde, 0, '', ' ') . ' Ar.');
    }
}
