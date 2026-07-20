<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12 text-center mb-4">
        <h2>Bienvenue sur votre espace Mobile Money</h2>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 shadow">
            <div class="card-header">Solde Disponible</div>
            <div class="card-body text-center">
                <h3 class="card-title" id="soldeView">**** Ar</h3>
                <button class="btn btn-sm btn-light mt-2" onclick="toggleSolde(<?= $client['solde'] ?>)">👁️ Afficher / Masquer</button>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">Actions Rapides</div>
            <div class="card-body d-flex justify-content-around">
                <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-primary btn-lg p-3">📊 Voir l'historique</a>
                <button class="btn btn-outline-secondary btn-lg p-3" disabled>💸 Faire un Transfert (V2)</button>
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