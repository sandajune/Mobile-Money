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
        if ($montant <= 0) {
            return $this->response->setJSON(['frais' => null, 'error' => 'Montant invalide']);
        }
        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');
        if (!$tranche) {
            return $this->response->setJSON(['frais' => null, 'error' => 'Hors barème']);
        }
        return $this->response->setJSON(['frais' => $tranche['frais']]);
    }

    public function preview()
    {
        $montant = $this->request->getPost('montant');

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être un nombre supérieur à 0.');
        }

        $montant  = (float) $montant;
        $tranche  = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');

        if (!$tranche) {
            return redirect()->back()->with('error', 'Aucun barème de frais disponible pour ce montant. Vérifiez les tarifs en vigueur.');
        }

        $frais      = $tranche['frais'];
        $totalDebit = $montant + $frais;
        $client     = (new ClientModel())->find(session()->get('id'));

        if ($client['solde'] < $totalDebit) {
            return redirect()->back()->with('error',
                'Solde insuffisant. Il vous faut ' . number_format($totalDebit, 0, '', ' ') . ' Ar (montant + frais ' . number_format($frais, 0, '', ' ') . ' Ar), mais votre solde est de ' . number_format($client['solde'], 0, '', ' ') . ' Ar.');
        }

        return view('client/retrait/confirm', [
            'montant'       => $montant,
            'frais'         => $frais,
            'total_debit'   => $totalDebit,
            'solde_actuel'  => $client['solde'],
            'nouveau_solde' => $client['solde'] - $totalDebit,
        ]);
    }

    public function store()
    {
        $montant = $this->request->getPost('montant');

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->to(base_url('client/retrait'))->with('error', 'Montant invalide.');
        }

        $montant = (float) $montant;
        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'retrait');

        if (!$tranche) {
            return redirect()->to(base_url('client/retrait'))->with('error', 'Aucun barème de frais disponible pour ce montant.');
        }

        $frais      = $tranche['frais'];
        $totalDebit = $montant + $frais;
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
                'montant'                => $montant,
                'frais'                  => $frais,
            ]);

            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('client/retrait'))->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->to(base_url('client/dashboard'))
            ->with('success', 'Retrait de ' . number_format($montant, 0, '', ' ') . ' Ar effectué. Frais : ' . number_format($frais, 0, '', ' ') . ' Ar. Nouveau solde : ' . number_format($nouveauSolde, 0, '', ' ') . ' Ar.');
    }
}
