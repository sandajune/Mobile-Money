
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow mt-5">
            <div class="card-header bg-primary text-white text-center"><h4>Connexion Client</h4></div>
            <div class="card-body">
                <form action="<?= base_url('login/auth') ?>" method="POST" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Numéro de téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" placeholder="Ex: 0331234567" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Accéder au service</button>
                </form>
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