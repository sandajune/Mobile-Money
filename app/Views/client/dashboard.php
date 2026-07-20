<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 text-center mb-4">
            <h2><i class="bi bi-person-circle"></i> Bienvenue, <?= esc($client['nom_clients']) ?></h2>
            <p class="text-muted">Numéro: <?= esc($client['telephone']) ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-header">
                    <i class="bi bi-wallet2"></i> Solde Disponible
                </div>
                <div class="card-body text-center">
                    <h3 class="card-title" id="soldeView">**** Ar</h3>
                    <button class="btn btn-sm btn-light mt-2" onclick="toggleSolde(<?= $client['solde'] ?>)">
                        <i class="bi bi-eye"></i> Afficher / Masquer
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-3">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-lightning-charge"></i> Actions Rapides
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <a href="<?= base_url('client/historique') ?>" class="btn btn-info btn-lg w-100 p-3">
                                <i class="bi bi-clock-history"></i> Voir l'historique
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->get('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->get('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->get('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->get('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<script>
let visible = false;
function toggleSolde(solde) {
    const soldeView = document.getElementById('soldeView');
    if(!visible) {
        soldeView.innerText = new Intl.NumberFormat('fr-FR').format(solde) + " Ar";
    } else {
        soldeView.innerText = "**** Ar";
    }
    visible = !visible;
}
</script>
<?= $this->endSection() ?>