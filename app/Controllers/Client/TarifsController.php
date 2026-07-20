<?php
namespace App\Controllers\Client;
use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;

class TarifsController extends BaseController {
    public function index() {
        $model = new BaremeFraisModel();
        // Lecture seule - clients peuvent voir les tarifs mais pas les modifier
        $data['retraits'] = $model->where('type_operation', 'retrait')->orderBy('montant_min', 'ASC')->findAll();
        $data['transferts'] = $model->where('type_operation', 'transfert')->orderBy('montant_min', 'ASC')->findAll();
        return view('client/tarifs', $data);
    }
}
