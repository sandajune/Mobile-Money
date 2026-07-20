<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-check-circle"></i> Confirmer le transfert</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vérifiez les informations avant de confirmer. Cette action est irréversible.
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th class="table-light">Destinataire</th>
                        <td>
                            <strong><?= esc($nom_destinataire) ?></strong><br>
                            <span class="text-muted"><?= esc($telephone_destinataire) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th class="table-light">Montant envoyé</th>
                        <td class="fw-bold fs-5"><?= number_format($montant, 0, '', ' ') ?> Ar</td>
                    </tr>
                    <tr>
                        <th class="table-light">Frais appliqués</th>
                        <td class="text-danger fw-bold"><?= number_format($frais, 0, '', ' ') ?> Ar</td>
                    </tr>
                    <tr class="table-primary">
                        <th>Total débité de votre compte</th>
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
                    <a href="<?= base_url('client/transfert') ?>" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                    <form action="<?= base_url('client/transfert/store') ?>" method="post" class="flex-fill">
                        <?= csrf_field() ?>
                        <input type="hidden" name="montant"                value="<?= esc($montant) ?>">
                        <input type="hidden" name="telephone_destinataire" value="<?= esc($telephone_destinataire) ?>">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Confirmer le transfert
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
