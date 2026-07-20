<?php
namespace App\Controllers\Operateur;
use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;

class TypeOperationController extends BaseController {
    public function index() {
        $model = new BaremeFraisModel();
        // Regroupement par type pour la vue générale
        $data['retraits'] = $model->where('type_operation', 'retrait')->orderBy('montant_min', 'ASC')->findAll();
        $data['transferts'] = $model->where('type_operation', 'transfert')->orderBy('montant_min', 'ASC')->findAll();
        return view('operateur/frais/index', $data);
    }

    public function storeTranche() {
        $model = new BaremeFraisModel();
        $min = $this->request->getPost('montant_min');
        $max = $this->request->getPost('montant_max');
        $frais = $this->request->getPost('frais');
        $type = $this->request->getPost('type_operation');

        if ($min >= $max || $frais < 0) {
            return redirect()->back()->with('error', 'Données de tranche incohérentes (Min doit être inférieur à Max et frais positifs).');
        }

        $model->save([
            'type_operation' => $type,
            'montant_min'    => $min,
            'montant_max'    => $max,
            'frais'          => $frais
        ]);

        return redirect()->back()->with('success', 'Nouvelle tranche ajoutée.');
    }

    public function editTranche($id) {
        $model = new BaremeFraisModel();
        $data['tranche'] = $model->find($id);
        if (!$data['tranche']) return redirect()->back()->with('error', 'Tranche introuvable.');
        return view('operateur/frais/edit', $data);
    }

    public function updateTranche($id) {
        $model = new BaremeFraisModel();
        $tranche = $model->find($id);
        if (!$tranche) return redirect()->back()->with('error', 'Tranche introuvable.');

        $min = $this->request->getPost('montant_min');
        $max = $this->request->getPost('montant_max');
        $frais = $this->request->getPost('frais');
        $type = $this->request->getPost('type_operation');

        if ($min >= $max || $frais < 0) {
            return redirect()->back()->with('error', 'Données de tranche incohérentes (Min doit être inférieur à Max et frais positifs).');
        }

        $model->update($id, [
            'type_operation' => $type,
            'montant_min'    => $min,
            'montant_max'    => $max,
            'frais'          => $frais
        ]);

        return redirect()->to(base_url('operateur/frais'))->with('success', 'Tranche modifiée avec succès.');
    }

    public function deleteTranche($id) {
        (new BaremeFraisModel())->delete($id);
        return redirect()->back()->with('success', 'Tranche supprimée.');
    }
}