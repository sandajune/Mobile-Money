<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\TransactionModel;

class TransfertController extends BaseController
{
    public function create()
    {
        return view('client/transfert/create');
    }

    /** Point AJAX – vérifie si un numéro destinataire existe. */
    public function checkDestinataire()
    {
        $telephone = $this->request->getPost('telephone');
        $dest      = (new ClientModel())->where('telephone', $telephone)->first();

        $payload = ['exists' => (bool) $dest];
        if ($dest) {
            $payload['nom'] = $dest['nom_clients'];
        }

        return $this->response->setJSON($payload);
    }

    public function preview()
    {
        $montant      = $this->request->getPost('montant');
        $telephoneDest = trim($this->request->getPost('telephone_destinataire'));

        if (empty($telephoneDest)) {
            return redirect()->back()->with('error', 'Le numéro destinataire est requis.');
        }
        if ($telephoneDest === session()->get('telephone')) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
        }
        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être un nombre supérieur à 0.');
        }

        $montant      = (float) $montant;
        $clientModel  = new ClientModel();
        $destinataire = $clientModel->where('telephone', $telephoneDest)->first();

        if (!$destinataire) {
            return redirect()->back()->with('error', 'Le numéro destinataire « ' . esc($telephoneDest) . ' » n\'existe pas.');
        }

        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'transfert');
        if (!$tranche) {
            return redirect()->back()->with('error', 'Aucun barème de frais disponible pour ce montant.');
        }

        $frais      = $tranche['frais'];
        $totalDebit = $montant + $frais;
        $emetteur   = $clientModel->find(session()->get('id'));

        if ($emetteur['solde'] < $totalDebit) {
            return redirect()->back()->with('error',
                'Solde insuffisant. Il vous faut ' . number_format($totalDebit, 0, '', ' ') . ' Ar (montant + frais ' . number_format($frais, 0, '', ' ') . ' Ar), mais votre solde est de ' . number_format($emetteur['solde'], 0, '', ' ') . ' Ar.');
        }

        return view('client/transfert/confirm', [
            'montant'                => $montant,
            'frais'                  => $frais,
            'total_debit'            => $totalDebit,
            'solde_actuel'           => $emetteur['solde'],
            'nouveau_solde'          => $emetteur['solde'] - $totalDebit,
            'telephone_destinataire' => $telephoneDest,
            'nom_destinataire'       => $destinataire['nom_clients'],
        ]);
    }

    public function store()
    {
        $montant      = $this->request->getPost('montant');
        $telephoneDest = trim($this->request->getPost('telephone_destinataire'));

        if (empty($telephoneDest) || empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Données invalides.');
        }
        if ($telephoneDest === session()->get('telephone')) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Transfert vers vous-même non autorisé.');
        }

        $montant      = (float) $montant;
        $clientModel  = new ClientModel();
        $destinataire = $clientModel->where('telephone', $telephoneDest)->first();

        if (!$destinataire) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Le numéro destinataire n\'existe pas.');
        }

        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'transfert');
        if (!$tranche) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Aucun barème de frais disponible pour ce montant.');
        }

        $frais            = $tranche['frais'];
        $totalDebit       = $montant + $frais;
        $emetteurId       = session()->get('id');
        $telephoneEmetteur = session()->get('telephone');
        $db               = \Config\Database::connect();

        $db->transBegin();
        try {
            $emetteur = $clientModel->find($emetteurId);

            if ($emetteur['solde'] < $totalDebit) {
                $db->transRollback();
                return redirect()->to(base_url('client/transfert'))->with('error', 'Solde insuffisant.');
            }

            $nouveauSolde = $emetteur['solde'] - $totalDebit;

            // Débiter l'émetteur
            $clientModel->update($emetteurId, ['solde' => $nouveauSolde]);
            // Créditer le destinataire
            $clientModel->update($destinataire['id'], ['solde' => $destinataire['solde'] + $montant]);

            $txModel = new TransactionModel();
            // Ligne émetteur
            $txModel->insert([
                'telephone_client'       => $telephoneEmetteur,
                'telephone_destinataire' => $telephoneDest,
                'type_operation'         => 'transfert_envoi',
                'montant'                => $montant,
                'frais'                  => $frais,
            ]);
            // Ligne destinataire
            $txModel->insert([
                'telephone_client'       => $telephoneDest,
                'telephone_destinataire' => $telephoneEmetteur,
                'type_operation'         => 'transfert_reception',
                'montant'                => $montant,
                'frais'                  => 0.0,
            ]);

            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('client/transfert'))->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->to(base_url('client/dashboard'))
            ->with('success', 'Transfert de ' . number_format($montant, 0, '', ' ') . ' Ar envoyé à ' . esc($telephoneDest) . '. Frais : ' . number_format($frais, 0, '', ' ') . ' Ar.');
    }
}
