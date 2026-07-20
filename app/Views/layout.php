<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money Operator</title>
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <?php if(session()->get('isLoggedIn')): ?>
    <!-- Navbar Client -->
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('client/dashboard') ?>">
                <i class="bi bi-wallet2"></i> M-Money
                <span class="badge-role">Client</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="clientNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('client/dashboard') ?>"><i class="bi bi-house"></i> Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('client/historique') ?>"><i class="bi bi-clock-history"></i> Historique</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('client/tarifs') ?>"><i class="bi bi-currency-exchange"></i> Tarifs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('client/depot') ?>"><i class="bi bi-cash-coin"></i> Dépôt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('client/retrait') ?>"><i class="bi bi-cash-stack"></i> Retrait</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('client/transfert') ?>"><i class="bi bi-arrow-left-right"></i> Transfert</a>
                    </li>
                </ul>
                <div class="navbar-nav ms-auto align-items-lg-center">
                    <span class="navbar-user me-2">
                        <i class="bi bi-person-circle"></i> <?= esc(session()->get('telephone')) ?>
                    </span>
                    <a class="btn btn-sm btn-logout" href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php elseif(session()->get('isOperatorLoggedIn')): ?>
    <!-- Navbar Opérateur Connecté -->
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('operateur/prefixes') ?>">
                <i class="bi bi-shield-lock"></i> M-Money
                <span class="badge-role">Opérateur</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#operatorNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="operatorNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('operateur/prefixes') ?>"><i class="bi bi-list-ol"></i> Préfixes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('operateur/frais') ?>"><i class="bi bi-currency-exchange"></i> Barèmes de frais</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('operateur/reporting/gains') ?>"><i class="bi bi-graph-up-arrow"></i> Gains</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('operateur/reporting/clients') ?>"><i class="bi bi-people"></i> Comptes clients</a>
                    </li>
                </ul>
                <div class="navbar-nav ms-auto align-items-lg-center">
                    <span class="navbar-user me-2">
                        <i class="bi bi-person-circle"></i> <?= esc(session()->get('operator_nom')) ?>
                    </span>
                    <a class="btn btn-sm btn-logout" href="<?= base_url('operateur/logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php else: ?>
    <!-- Navbar Opérateur Non Connecté -->
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('operateur/login') ?>">
                <i class="bi bi-wallet2"></i> M-Money
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#operatorNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="operatorNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('operateur/login') ?>"><i class="bi bi-shield-lock"></i> Connexion Opérateur</a>
                    </li>
                </ul>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="<?= base_url('login') ?>">
                        <i class="bi bi-person"></i> Espace Client
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="app-main">
        <div class="container mt-4">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <footer class="app-footer">
        <div class="container d-flex flex-wrap justify-content-between gap-2">
            <span>Mobile Money Operator</span>
            <span>&copy; <?= date('Y') ?> Tous droits réservés</span>
        </div>
    </footer>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
