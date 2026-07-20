<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-check-circle"></i> Confirmer le dépôt</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vérifiez les informations avant de confirmer.
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th class="table-light">Montant déposé</th>
                        <td class="text-success fw-bold fs-5"><?= number_format($montant, 0, '', ' ') ?> Ar</td>
                    </tr>
                    <tr>
                        <th class="table-light">Frais</th>
                        <td class="text-muted">0 Ar <span class="badge bg-success">Gratuit</span></td>
                    </tr>
                    <tr>
                        <th class="table-light">Solde actuel</th>
                        <td><?= number_format($solde_actuel, 0, '', ' ') ?> Ar</td>
                    </tr>
                    <tr>
                        <th class="table-light">Nouveau solde</th>
                        <td class="text-success fw-bold fs-5"><?= number_format($nouveau_solde, 0, '', ' ') ?> Ar</td>
                    </tr>
                </table>

                <div class="d-flex gap-2 mt-3">
                    <a href="<?= base_url('client/depot') ?>" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                    <form action="<?= base_url('client/depot/store') ?>" method="post" class="flex-fill">
                        <?= csrf_field() ?>
                        <input type="hidden" name="montant" value="<?= esc($montant) ?>">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg"></i> Confirmer le dépôt
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
