<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Bienvenue, <?= esc($client['nom_clients']) ?></h1>
        <p class="page-subtitle">Numéro de compte : <?= esc($client['telephone']) ?></p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="stat-icon bg-success-subtle text-success">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="stat-label">Solde disponible</div>
            <div class="stat-value" id="soldeView">**** Ar</div>
            <button class="btn btn-sm btn-outline-secondary mt-2" onclick="toggleSolde(<?= $client['solde'] ?>)">
                <i class="bi bi-eye"></i> Afficher / Masquer
            </button>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <i class="bi bi-lightning-charge"></i> Actions rapides
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <a href="<?= base_url('client/depot') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-cash-coin d-block fs-4 mb-1"></i> Dépôt
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('client/retrait') ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-cash-stack d-block fs-4 mb-1"></i> Retrait
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('client/transfert') ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-arrow-left-right d-block fs-4 mb-1"></i> Transfert
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-secondary w-100 py-3">
                            <i class="bi bi-clock-history d-block fs-4 mb-1"></i> Historique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
