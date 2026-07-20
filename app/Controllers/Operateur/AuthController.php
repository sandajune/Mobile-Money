<?php
namespace App\Controllers\Operateur;
use App\Controllers\BaseController;
use App\Models\OperateurModel;

class AuthController extends BaseController {
    public function login() {
        if (session()->get('isOperatorLoggedIn')) return redirect()->to(base_url('operateur/prefixes'));
        return view('operateur/login');
    }

    public function authenticate() {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Veuillez remplir tous les champs.');
        }

        $operateurModel = new OperateurModel();
        $operateur = $operateurModel->where('username', $username)->first();

        if (!$operateur || $operateur['password'] !== $password) {
            return redirect()->back()->with('error', 'Identifiants incorrects.');
        }

        session()->set([
            'operator_id' => $operateur['id'],
            'operator_username' => $operateur['username'],
            'operator_nom' => $operateur['nom'],
            'isOperatorLoggedIn' => true
        ]);

        return redirect()->to(base_url('operateur/prefixes'))->with('success', 'Bienvenue ' . $operateur['nom']);
    }

    public function logout() {
        session()->remove(['operator_id', 'operator_username', 'operator_nom', 'isOperatorLoggedIn']);
        return redirect()->to(base_url('operateur/login'))->with('success', 'Déconnexion réussie.');
    }
}
