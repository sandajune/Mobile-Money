<?php
namespace App\Controllers\Client;
use App\Controllers\BaseController;
use App\Models\PrefixeModel;
use App\Models\ClientModel;

class AuthController extends BaseController {
    public function login() {
        if (session()->get('isLoggedIn')) return redirect()->to(base_url('client/dashboard'));
        return view('client/login');
    }

    public function authenticate() {
        $telephone = $this->request->getPost('telephone');
        if (empty($telephone)) {
            return redirect()->back()->with('error', 'Le numéro de téléphone est requis.');
        }

        $prefixeModel = new PrefixeModel();
        $prefixes = $prefixeModel->findAll();
        
        $valide = false;
        foreach ($prefixes as $p) {
            if (strpos($telephone, $p['prefixe']) === 0) {
                $valide = true;
                break;
            }
        }

        if (!$valide) {
            return redirect()->back()->with('error', 'Le numéro ne commence pas par un préfixe valide d\'un opérateur.');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->where('telephone', $telephone)->first();

        // S'il n'existe pas encore : inscription et login automatique
        if (!$client) {
            $clientModel->save([
                'nom_clients' => 'Client_' . $telephone, // Nom généré automatiquement par défaut
                'telephone'   => $telephone,
                'solde'       => 0.0
            ]);
            $client = $clientModel->where('telephone', $telephone)->first();
        }

        session()->set([
            'id'          => $client['id'],
            'nom_clients' => $client['nom_clients'],
            'telephone'   => $client['telephone'],
            'isLoggedIn'  => true
        ]);

        return redirect()->to(base_url('client/dashboard'));
    }

    public function logout() {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}