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
});

// Espace Opérateur Configuration
$routes->group('operateur', function($routes) {
    // Préfixes
    $routes->get('prefixes', 'Operateur\PrefixeController::index');
    $routes->post('prefixes/store', 'Operateur\PrefixeController::store');
    $routes->get('prefixes/edit/(:num)', 'Operateur\PrefixeController::edit/$1');
    $routes->post('prefixes/update/(:num)', 'Operateur\PrefixeController::update/$1');
    $routes->get('prefixes/delete/(:num)', 'Operateur\PrefixeController::delete/$1');

    // Barèmes & Tarifs
    $routes->get('frais', 'Operateur\TypeOperationController::index');
    $routes->post('frais/storeTranche', 'Operateur\TypeOperationController::storeTranche');
    $routes->get('frais/editTranche/(:num)', 'Operateur\TypeOperationController::editTranche/$1');
    $routes->post('frais/updateTranche/(:num)', 'Operateur\TypeOperationController::updateTranche/$1');
    $routes->get('frais/deleteTranche/(:num)', 'Operateur\TypeOperationController::deleteTranche/$1');
});