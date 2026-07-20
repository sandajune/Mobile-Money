<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Modifier la tranche</h1>
    </div>
    <a href="<?= base_url('operateur/frais') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('operateur/frais/updateTranche/' . $tranche['id']) ?>" method="post">
                <div class="mb-3">
                    <label for="type_operation" class="form-label">Type d'opération</label>
                    <select class="form-select" id="type_operation" name="type_operation" required>
                        <option value="retrait" <?= $tranche['type_operation'] === 'retrait' ? 'selected' : '' ?>>Retrait</option>
                        <option value="transfert" <?= $tranche['type_operation'] === 'transfert' ? 'selected' : '' ?>>Transfert</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="montant_min" class="form-label">Montant minimum (Ar)</label>
                        <input type="number" class="form-control" id="montant_min" name="montant_min" 
                               value="<?= $tranche['montant_min'] ?>" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="montant_max" class="form-label">Montant maximum (Ar)</label>
                        <input type="number" class="form-control" id="montant_max" name="montant_max" 
                               value="<?= $tranche['montant_max'] ?>" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="frais" class="form-label">Frais (Ar)</label>
                    <input type="number" class="form-control" id="frais" name="frais" 
                           value="<?= $tranche['frais'] ?>" min="0" step="0.01" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('operateur/frais') ?>" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const min = parseFloat(document.getElementById('montant_min').value);
        const max = parseFloat(document.getElementById('montant_max').value);
        const frais = parseFloat(document.getElementById('frais').value);
        
        if (min >= max) {
            e.preventDefault();
            alert('Le montant minimum doit être inférieur au montant maximum.');
        }
        if (frais < 0) {
            e.preventDefault();
            alert('Les frais doivent être positifs ou nuls.');
        }
    });
});
</script>
<?= $this->endSection() ?>
