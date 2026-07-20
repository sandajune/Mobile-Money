<?php
namespace App\Controllers\Client;
use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\TransactionModel;
use App\Models\BaremeFraisModel;

class CompteController extends BaseController {
    public function dashboard() {
        $clientModel = new ClientModel();
        $client = $clientModel->find(session()->get('id'));
        
        $data['client'] = $client;
        return view('client/dashboard', $data);
    }

    public function historique() {
        $txModel = new TransactionModel();
        $tel = session()->get('telephone');

        $data['transactions'] = $txModel->getHistorique($tel)->paginate(10, 'default');
        $data['pager'] = $txModel->pager;

        return view('client/historique', $data);
    }

    public function depot() {
        return view('client/depot');
    }

    public function processDepot() {
        $montant = $this->request->getPost('montant');
        
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être positif.');
        }

        $clientModel = new ClientModel();
        $txModel = new TransactionModel();
        
        $clientId = session()->get('id');
        $telephone = session()->get('telephone');
        
        // Récupérer le solde actuel
        $client = $clientModel->find($clientId);
        $nouveauSolde = $client['solde'] + $montant;
        
        // Mettre à jour le solde
        $clientModel->update($clientId, ['solde' => $nouveauSolde]);
        
        // Enregistrer la transaction
        $txModel->save([
            'telephone_client' => $telephone,
            'telephone_destinataire' => null,
            'type_operation' => 'depot',
            'montant' => $montant,
            'frais' => 0
        ]);
        
        return redirect()->to(base_url('client/dashboard'))->with('success', 'Dépôt effectué avec succès.');
    }

    public function retrait() {
        return view('client/retrait');
    }

    public function processRetrait() {
        $montant = $this->request->getPost('montant');
        
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être positif.');
        }

        $clientModel = new ClientModel();
        $txModel = new TransactionModel();
        $baremeModel = new BaremeFraisModel();
        
        $clientId = session()->get('id');
        $telephone = session()->get('telephone');
        
        // Récupérer le solde actuel
        $client = $clientModel->find($clientId);
        
        // Calculer les frais
        $frais = $baremeModel->where('type_operation', 'retrait')
                              ->where('montant_min <=', $montant)
                              ->where('montant_max >=', $montant)
                              ->first();
        
        $montantFrais = $frais ? $frais['frais'] : 0;
        $total = $montant + $montantFrais;
        
        // Vérifier le solde
        if ($client['solde'] < $total) {
            return redirect()->back()->with('error', 'Solde insuffisant. Solde actuel: ' . number_format($client['solde'], 0, '', ' ') . ' Ar. Montant requis: ' . number_format($total, 0, '', ' ') . ' Ar.');
        }
        
        // Mettre à jour le solde
        $nouveauSolde = $client['solde'] - $total;
        $clientModel->update($clientId, ['solde' => $nouveauSolde]);
        
        // Enregistrer la transaction
        $txModel->save([
            'telephone_client' => $telephone,
            'telephone_destinataire' => null,
            'type_operation' => 'retrait',
            'montant' => $montant,
            'frais' => $montantFrais
        ]);
        
        return redirect()->to(base_url('client/dashboard'))->with('success', 'Retrait effectué avec succès. Frais: ' . number_format($montantFrais, 0, '', ' ') . ' Ar.');
    }

    public function transfert() {
        return view('client/transfert');
    }

    public function processTransfert() {
        $montant = $this->request->getPost('montant');
        $destinataire = $this->request->getPost('telephone_destinataire');
        
        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être positif.');
        }
        
        if (empty($destinataire)) {
            return redirect()->back()->with('error', 'Le numéro du destinataire est requis.');
        }
        
        if ($destinataire === session()->get('telephone')) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer à vous-même.');
        }

        $clientModel = new ClientModel();
        $txModel = new TransactionModel();
        $baremeModel = new BaremeFraisModel();
        
        $clientId = session()->get('id');
        $telephone = session()->get('telephone');
        
        // Vérifier que le destinataire existe
        $destinataireClient = $clientModel->where('telephone', $destinataire)->first();
        if (!$destinataireClient) {
            return redirect()->back()->with('error', 'Le destinataire n\'existe pas.');
        }
        
        // Récupérer le solde actuel de l'émetteur
        $client = $clientModel->find($clientId);
        
        // Calculer les frais
        $frais = $baremeModel->where('type_operation', 'transfert')
                              ->where('montant_min <=', $montant)
                              ->where('montant_max >=', $montant)
                              ->first();
        
        $montantFrais = $frais ? $frais['frais'] : 0;
        $total = $montant + $montantFrais;
        
        // Vérifier le solde de l'émetteur
        if ($client['solde'] < $total) {
            return redirect()->back()->with('error', 'Solde insuffisant. Solde actuel: ' . number_format($client['solde'], 0, '', ' ') . ' Ar. Montant requis: ' . number_format($total, 0, '', ' ') . ' Ar.');
        }
        
        // Déduire le montant total du solde de l'émetteur
        $nouveauSoldeEmetteur = $client['solde'] - $total;
        $clientModel->update($clientId, ['solde' => $nouveauSoldeEmetteur]);
        
        // Ajouter le montant au solde du destinataire
        $nouveauSoldeDestinataire = $destinataireClient['solde'] + $montant;
        $clientModel->update($destinataireClient['id'], ['solde' => $nouveauSoldeDestinataire]);
        
        // Enregistrer la transaction d'envoi
        $txModel->save([
            'telephone_client' => $telephone,
            'telephone_destinataire' => $destinataire,
            'type_operation' => 'transfert_envoi',
            'montant' => $montant,
            'frais' => $montantFrais
        ]);
        
        // Enregistrer la transaction de réception
        $txModel->save([
            'telephone_client' => $destinataire,
            'telephone_destinataire' => $telephone,
            'type_operation' => 'transfert_reception',
            'montant' => $montant,
            'frais' => 0
        ]);
        
        return redirect()->to(base_url('client/dashboard'))->with('success', 'Transfert effectué avec succès. Frais: ' . number_format($montantFrais, 0, '', ' ') . ' Ar.');
    }
}