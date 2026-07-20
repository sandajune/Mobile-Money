<?php
namespace App\Controllers\Operateur;
use App\Controllers\BaseController;
use App\Models\PrefixeModel;
use App\Models\ClientModel;

class PrefixeController extends BaseController {
    public function index() {
        $model = new PrefixeModel();
        $data['prefixes'] = $model->orderBy('id', 'DESC')->paginate(10, 'default');
        $data['pager']    = $model->pager;
        return view('operateur/prefixes/index', $data);
    }

    public function store() {
        $model = new PrefixeModel();
        $typeOperateur = $this->request->getPost('type_operateur');
        $prefixe = $this->request->getPost('prefixe');
        
        $val = $this->validate([
            'nom_operateur' => 'required|min_length[2]',
            'prefixe'       => 'required|numeric|exact_length[3]|is_unique[config_prefixes.prefixe]',
            'type_operateur'=> 'required|in_list[interne,externe]'
        ]);

        if (!$val) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier vos saisies (Préfixe unique de 3 chiffres).');
        }

        // Validation : un préfixe externe ne doit pas entrer en conflit avec un préfixe interne existant
        if ($typeOperateur === 'externe') {
            $prefixeInterneExistant = $model->where('prefixe', $prefixe)->where('type_operateur', 'interne')->first();
            if ($prefixeInterneExistant) {
                return redirect()->back()->withInput()->with('error', 'Ce préfixe existe déjà comme opérateur interne. Les préfixes externes et internes doivent être différents.');
            }
        }

        // Validation commission pour externe
        $commissionPourcentage = $this->request->getPost('commission_pourcentage');
        if ($typeOperateur === 'externe') {
            if ($commissionPourcentage === '' || $commissionPourcentage === null) {
                return redirect()->back()->withInput()->with('error', 'Le pourcentage de commission est obligatoire pour les opérateurs externes.');
            }
            if (!is_numeric($commissionPourcentage) || (float)$commissionPourcentage < 0 || (float)$commissionPourcentage > 100) {
                return redirect()->back()->withInput()->with('error', 'Le pourcentage de commission doit être entre 0 et 100.');
            }
        } else {
            $commissionPourcentage = 0;
        }

        $model->save([
            'nom_operateur' => $this->request->getPost('nom_operateur'),
            'prefixe'       => $prefixe,
            'type_operateur' => $typeOperateur,
            'commission_pourcentage' => (float)$commissionPourcentage
        ]);
        return redirect()->to(base_url('operateur/prefixes'))->with('success', 'Opérateur et préfixe ajoutés avec succès.');
    }

    public function edit($id) {
        $model = new PrefixeModel();
        $data['prefixe'] = $model->find($id);
        if (!$data['prefixe']) return redirect()->back()->with('error', 'Préfixe introuvable.');
        return view('operateur/prefixes/edit', $data);
    }

    public function update($id) {
        $model = new PrefixeModel();
        $prefixe = $model->find($id);
        if (!$prefixe) return redirect()->back()->with('error', 'Préfixe introuvable.');

        $typeOperateur = $this->request->getPost('type_operateur');
        $nouveauPrefixe = $this->request->getPost('prefixe');
        
        $val = $this->validate([
            'nom_operateur' => 'required|min_length[2]',
            'prefixe'       => "required|numeric|exact_length[3]|is_unique[config_prefixes.prefixe,id,{$id}]",
            'type_operateur'=> 'required|in_list[interne,externe]'
        ]);

        if (!$val) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier vos saisies (Préfixe unique de 3 chiffres).');
        }

        // Validation : un préfixe externe ne doit pas entrer en conflit avec un préfixe interne existant
        if ($typeOperateur === 'externe' && $nouveauPrefixe !== $prefixe['prefixe']) {
            $prefixeInterneExistant = $model->where('prefixe', $nouveauPrefixe)->where('type_operateur', 'interne')->first();
            if ($prefixeInterneExistant) {
                return redirect()->back()->withInput()->with('error', 'Ce préfixe existe déjà comme opérateur interne. Les préfixes externes et internes doivent être différents.');
            }
        }

        // Validation commission pour externe
        $commissionPourcentage = $this->request->getPost('commission_pourcentage');
        if ($typeOperateur === 'externe') {
            if ($commissionPourcentage === '' || $commissionPourcentage === null) {
                return redirect()->back()->withInput()->with('error', 'Le pourcentage de commission est obligatoire pour les opérateurs externes.');
            }
            if (!is_numeric($commissionPourcentage) || (float)$commissionPourcentage < 0 || (float)$commissionPourcentage > 100) {
                return redirect()->back()->withInput()->with('error', 'Le pourcentage de commission doit être entre 0 et 100.');
            }
        } else {
            $commissionPourcentage = 0;
        }

        $model->update($id, [
            'nom_operateur' => $this->request->getPost('nom_operateur'),
            'prefixe'       => $nouveauPrefixe,
            'type_operateur'=> $typeOperateur,
            'commission_pourcentage' => (float)$commissionPourcentage
        ]);
        return redirect()->to(base_url('operateur/prefixes'))->with('success', 'Préfixe modifié avec succès.');
    }

    public function delete($id) {
        $prefixeModel = new PrefixeModel();
        $clientModel = new ClientModel();
        
        $prefixe = $prefixeModel->find($id);
        if (!$prefixe) return redirect()->back()->with('error', 'Préfixe introuvable.');

        // Empêche la suppression si le préfixe correspond au début du numéro d'un client
        $clientsExistants = $clientModel->like('telephone', $prefixe['prefixe'], 'after')->countAllResults();
        if ($clientsExistants > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer : des clients possèdent ce préfixe.');
        }

        $prefixeModel->delete($id);
        return redirect()->to(base_url('operateur/prefixes'))->with('success', 'Préfixe supprimé.');
    }

    /** Endpoint pour lister uniquement les préfixes externes (utile pour formulaire commission) */
    public function getExternes() {
        $model = new PrefixeModel();
        $externes = $model->where('type_operateur', 'externe')->findAll();
        return $this->response->setJSON($externes);
    }

    /** Fonction utilitaire pour calculer la commission */
    public function calculerCommission($montant, $pourcentage) {
        return ($montant * $pourcentage) / 100;
    }
}