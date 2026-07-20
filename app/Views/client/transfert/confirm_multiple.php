<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-check-circle"></i> Confirmer l'envoi multiple</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vérifiez les informations avant de confirmer. Cette action est irréversible.
                </div>

                <h5 class="mb-3">Détails des envois</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Destinataire</th>
                                <th>Numéro</th>
                                <th>Montant reçu</th>
                                <th>Frais</th>
                                <th>Commission</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details_envois as $detail): ?>
                                <tr>
                                    <td><?= esc($detail['nom']) ?></td>
                                    <td><span data-phone-display><?= esc($detail['telephone']) ?></span></td>
                                    <td class="fw-bold"><?= number_format($detail['montant'], 0, '', ' ') ?> Ar</td>
                                    <td class="text-danger"><?= number_format($detail['frais'], 0, '', ' ') ?> Ar</td>
                                    <td class="text-warning">
                                        <?php if ($detail['est_externe']): ?>
                                            <?= number_format($detail['commission'], 0, '', ' ') ?> Ar
                                            <span class="badge bg-warning text-dark ms-1">Externe</span>
                                        <?php else: ?>
                                            0 Ar
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= number_format($detail['total'], 0, '', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="5" class="text-end">Total débité de votre compte</th>
                                <th class="fw-bold fs-5"><?= number_format($total_debit, 0, '', ' ') ?> Ar</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Récapitulatif</h6>
                                <p class="mb-1"><strong>Mode de division :</strong> 
                                    <?= $mode_division === 'total' ? 'Montant total divisé' : 'Montant par destinataire' ?>
                                </p>
                                <p class="mb-1"><strong>Nombre de destinataires :</strong> <?= count($details_envois) ?></p>
                                <p class="mb-1"><strong>Total frais :</strong> <?= number_format($total_frais, 0, '', ' ') ?> Ar</p>
                                <p class="mb-0"><strong>Total commission :</strong> <?= number_format($total_commission, 0, '', ' ') ?> Ar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Solde</h6>
                                <p class="mb-1"><strong>Solde actuel :</strong> <?= number_format($solde_actuel, 0, '', ' ') ?> Ar</p>
                                <p class="mb-0"><strong>Nouveau solde :</strong> 
                                    <span class="text-success fw-bold"><?= number_format($nouveau_solde, 0, '', ' ') ?> Ar</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?= base_url('client/transfert/multiple') ?>" class="btn btn-secondary flex-fill">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                    <form action="<?= base_url('client/transfert/storeMultiple') ?>" method="post" class="flex-fill">
                        <?= csrf_field() ?>
                        <input type="hidden" name="mode_division" value="<?= esc($mode_division) ?>">
                        <input type="hidden" name="montant" value="<?= esc($montant_post) ?>">
                        <?php foreach ($destinataires_post as $dest): ?>
                            <input type="hidden" name="destinataires[]" value="<?= esc($dest) ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Confirmer l'envoi multiple
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
