<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money Operator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">💰 M-Money System</a>
            <div class="navbar-nav ms-auto">
                <?php if(session()->get('isLoggedIn')): ?>
                    <a class="nav-link text-white" href="<?= base_url('client/dashboard') ?>">Mon Compte (<?= session()->get('telephone') ?>)</a>
                    <a class="nav-link btn btn-danger btn-sm text-white ms-2" href="<?= base_url('logout') ?>">Déconnexion</a>
                <?php else: ?>
                    <a class="nav-link text-white" href="<?= base_url('operateur/prefixes') ?>">Espace Opérateur</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>