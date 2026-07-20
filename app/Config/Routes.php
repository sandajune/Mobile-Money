<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Client\AuthController::login');

// Route d'authentification client
$routes->get('login', 'Client\AuthController::login');
$routes->post('login/auth', 'Client\AuthController::authenticate');
$routes->get('logout', 'Client\AuthController::logout');

// Espace Client Sécurisé
$routes->group('client', ['filter' => 'clientAuth'], function($routes) {
    $routes->get('dashboard', 'Client\CompteController::dashboard');
    $routes->get('historique', 'Client\CompteController::historique');
    // Lecture seule des tarifs pour les clients
    $routes->get('tarifs', 'Client\TarifsController::index');

    // Dépôt
    $routes->get('depot', 'Client\DepotController::create');
    $routes->post('depot/preview', 'Client\DepotController::preview');
    $routes->post('depot/store', 'Client\DepotController::store');

    // Retrait
    $routes->get('retrait', 'Client\RetraitController::create');
    $routes->get('retrait/frais', 'Client\RetraitController::frais');  // AJAX fee lookup
    $routes->post('retrait/preview', 'Client\RetraitController::preview');
    $routes->post('retrait/store', 'Client\RetraitController::store');

    // Transfert
    $routes->get('transfert', 'Client\TransfertController::create');
    $routes->post('transfert/check', 'Client\TransfertController::checkDestinataire');  // AJAX
    $routes->post('transfert/preview', 'Client\TransfertController::preview');
    $routes->post('transfert/store', 'Client\TransfertController::store');
});

// Espace Opérateur - Authentification
$routes->group('operateur', function($routes) {
    $routes->get('login', 'Operateur\AuthController::login');
    $routes->post('login/auth', 'Operateur\AuthController::authenticate');
    $routes->get('logout', 'Operateur\AuthController::logout');
    
    // Routes protégées (modification)
    $routes->group('', ['filter' => 'operateurAuth'], function($routes) {
        // Reporting
        $routes->get('reporting/gains', 'Operateur\ReportingController::gains');
        $routes->get('reporting/clients', 'Operateur\ReportingController::clients');

        // Préfixes
        $routes->get('prefixes', 'Operateur\PrefixeController::index');
        $routes->post('prefixes/store', 'Operateur\PrefixeController::store');
        $routes->get('prefixes/edit/(:num)', 'Operateur\PrefixeController::edit/$1');
        $routes->post('prefixes/update/(:num)', 'Operateur\PrefixeController::update/$1');
        $routes->get('prefixes/delete/(:num)', 'Operateur\PrefixeController::delete/$1');

        // Barèmes & Tarifs (modification)
        $routes->get('frais', 'Operateur\TypeOperationController::index');
        $routes->post('frais/storeTranche', 'Operateur\TypeOperationController::storeTranche');
        $routes->get('frais/editTranche/(:num)', 'Operateur\TypeOperationController::editTranche/$1');
        $routes->post('frais/updateTranche/(:num)', 'Operateur\TypeOperationController::updateTranche/$1');
        $routes->get('frais/deleteTranche/(:num)', 'Operateur\TypeOperationController::deleteTranche/$1');
    });
});