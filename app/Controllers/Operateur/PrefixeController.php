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
        $val = $this->validate([
            'nom_operateur' => 'required|min_length[2]',
            'prefixe'       => 'required|numeric|exact_length[3]|is_unique[config_prefixes.prefixe]'
        ]);

        if (!$val) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier vos saisies (Préfixe unique de 3 chiffres).');
        }

        $model->save([
            'nom_operateur' => $this->request->getPost('nom_operateur'),
            'prefixe'       => $this->request->getPost('prefixe')
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

        $val = $this->validate([
            'nom_operateur' => 'required|min_length[2]',
            'prefixe'       => "required|numeric|exact_length[3]|is_unique[config_prefixes.prefixe,id,{$id}]"
        ]);

        if (!$val) {
            return redirect()->back()->withInput()->with('error', 'Veuillez vérifier vos saisies (Préfixe unique de 3 chiffres).');
        }

        $model->update($id, [
            'nom_operateur' => $this->request->getPost('nom_operateur'),
            'prefixe'       => $this->request->getPost('prefixe')
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
}