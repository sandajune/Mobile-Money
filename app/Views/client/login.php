
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-wallet2"></i>
                <h4 class="mb-0">Connexion Client</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('login/auth') ?>" method="POST" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input type="text" name="telephone" id="telephone" class="form-control" placeholder="Ex: 0331234567" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Accéder au service</button>
                </form>
            </div>
            <div class="card-footer text-center bg-white">
                <small class="text-muted">Espace réservé aux clients enregistrés</small>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const tel = document.getElementById('telephone').value;
    if(isNaN(tel) || tel.length < 3) {
        alert("Veuillez saisir un numéro de téléphone valide composé uniquement de chiffres.");
        e.preventDefault();
    }
});
</script>
<?= $this->endSection() ?>