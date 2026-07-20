<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * S'exécute avant le traitement de la requête du contrôleur.
     * Permet de vérifier si la session de l'utilisateur est active.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Vérifie si la variable de session 'isLoggedIn' n'est pas définie ou est fausse
        if (!session()->get('isLoggedIn')) {
            // Redirige immédiatement vers la page de login avec un message flash d'erreur
            return redirect()->to(base_url('login'))->with('error', 'Veuillez vous connecter pour accéder à votre espace.');
        }
    }

    /**
     * S'exécute après le traitement de la requête du contrôleur.
     * Généralement laissé vide pour les filtres d'authentification simples.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après l'exécution de la requête
    }
}