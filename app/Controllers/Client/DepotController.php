<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\TransactionModel;

class DepotController extends BaseController
{
    public function create()
    {
        return view('client/depot/create');
    }

    public function preview()
    {
        $montant = $this->request->getPost('montant');

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être un nombre supérieur à 0.');
        }

        $montant      = (float) $montant;
        $client       = (new ClientModel())->find(session()->get('id'));
        $nouveauSolde = $client['solde'] + $montant;

        return view('client/depot/confirm', [
            'montant'       => $montant,
            'solde_actuel'  => $client['solde'],
            'nouveau_solde' => $nouveauSolde,
        ]);
    }

    public function store()
    {
        $montant = $this->request->getPost('montant');

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->to(base_url('client/depot'))->with('error', 'Montant invalide.');
        }

        $montant    = (float) $montant;
        $clientId   = session()->get('id');
        $telephone  = session()->get('telephone');
        $db         = \Config\Database::connect();

        $db->transBegin();
        try {
            $clientModel  = new ClientModel();
            $client       = $clientModel->find($clientId);
            $nouveauSolde = $client['solde'] + $montant;

            $clientModel->update($clientId, ['solde' => $nouveauSolde]);

            (new TransactionModel())->insert([
                'telephone_client'       => $telephone,
                'telephone_destinataire' => null,
                'type_operation'         => 'depot',
                'montant'                => $montant,
                'frais'                  => 0.0,
            ]);

            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('client/depot'))->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->to(base_url('client/dashboard'))
            ->with('success', 'Dépôt de ' . number_format($montant, 0, '', ' ') . ' Ar effectué avec succès. Nouveau solde : ' . number_format($nouveauSolde, 0, '', ' ') . ' Ar.');
    }
}
