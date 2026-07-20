<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-warning">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-check-circle"></i> Confirmer le retrait</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Les frais seront prélevés en plus du montant demandé.
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th class="table-light">Montant retiré</th>
                        <td class="fw-bold fs-5"><?= number_format($montant, 0, '', ' ') ?> Ar</td>
                    </tr>
                    <tr>
                        <th class="table-light">Frais appliqués</th>
                        <td class="text-danger fw-bold"><?= number_format($frais, 0, '', ' ') ?> Ar</td>
                    </tr>
                    <tr class="table-warning">
                        <th>Total débité</th>
                        <td class="fw-bold fs-5"><?= number_format($total_debit, 0, '', ' ') ?> Ar</td>
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
                    <a href="<?= base_url('client/retrait') ?>" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                    <form action="<?= base_url('client/retrait/store') ?>" method="post" class="flex-fill">
                        <?= csrf_field() ?>
                        <input type="hidden" name="montant" value="<?= esc($montant) ?>">
                        <button type="submit" class="btn btn-warning text-dark w-100">
                            <i class="bi bi-check-lg"></i> Confirmer le retrait
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
