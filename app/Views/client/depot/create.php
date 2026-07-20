<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-cash-coin"></i> Effectuer un dépôt</h4>
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('client/depot/preview') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label for="montant" class="form-label fw-semibold">Montant à déposer (Ar)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" class="form-control" id="montant" name="montant"
                                   min="1" step="1" placeholder="Ex: 5000" required autofocus>
                            <span class="input-group-text">Ar</span>
                        </div>
                        <div class="form-text">Le dépôt ne génère aucun frais.</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-eye"></i> Prévisualiser le dépôt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
