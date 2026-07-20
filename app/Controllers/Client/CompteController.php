<?php
namespace App\Controllers\Client;
use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\TransactionModel;

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
}