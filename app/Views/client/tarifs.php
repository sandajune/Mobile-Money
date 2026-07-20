<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Tarifs en vigueur</h1>
        <p class="page-subtitle">Ces tarifs sont appliqués automatiquement lors de vos opérations.</p>
    </div>
    <a href="<?= base_url('client/dashboard') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour au tableau de bord
    </a>
</div>

    <ul class="nav nav-tabs mb-4" id="tarifsTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="retrait-tab" data-bs-toggle="tab" data-bs-target="#retrait" type="button">
                <i class="bi bi-cash-stack"></i> Retraits
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="transfert-tab" data-bs-toggle="tab" data-bs-target="#transfert" type="button">
                <i class="bi bi-arrow-left-right"></i> Transferts
            </button>
        </li>
    </ul>

    <div class="tab-content" id="tarifsTabsContent">
        <!-- Retraits -->
        <div class="tab-pane fade show active" id="retrait">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Barème des frais de retrait</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tranche (Min - Max)</th>
                                    <th>Frais (Ar)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($retraits)): ?>
                                    <?php foreach ($retraits as $tranche): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info"><?= number_format($tranche['montant_min'], 0, '', ' ') ?></span> - 
                                                <span class="badge bg-info"><?= number_format($tranche['montant_max'], 0, '', ' ') ?></span> Ar
                                            </td>
                                            <td><strong><?= number_format($tranche['frais'], 0, '', ' ') ?> Ar</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Aucun tarif configuré pour les retraits</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transferts -->
        <div class="tab-pane fade" id="transfert">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Barème des frais de transfert</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tranche (Min - Max)</th>
                                    <th>Frais (Ar)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transferts)): ?>
                                    <?php foreach ($transferts as $tranche): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info"><?= number_format($tranche['montant_min'], 0, '', ' ') ?></span> - 
                                                <span class="badge bg-info"><?= number_format($tranche['montant_max'], 0, '', ' ') ?></span> Ar
                                            </td>
                                            <td><strong><?= number_format($tranche['frais'], 0, '', ' ') ?> Ar</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Aucun tarif configuré pour les transferts</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
