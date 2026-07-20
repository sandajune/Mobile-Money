<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-shield-lock"></i>
                <h4 class="mb-0">Connexion Opérateur</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('operateur/login/auth') ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-white">
                <small class="text-muted">Accès réservé au personnel autorisé</small>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
