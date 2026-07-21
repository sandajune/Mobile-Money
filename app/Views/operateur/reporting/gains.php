<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Situation des gains</h1>
        <p class="page-subtitle">Suivi des revenus générés par les frais d'opération.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('operateur/reporting/settlements') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-currency-dollar"></i> Reversements opérateurs
        </a>
        <a href="<?= base_url('operateur/reporting/clients') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-people"></i> Comptes clients
        </a>
    </div>
</div>

<!-- Filtre par période -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Date début</label>
                <input type="date" name="date_debut" class="form-control" value="<?= esc($date_debut ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date fin</label>
                <input type="date" name="date_fin" class="form-control" value="<?= esc($date_fin ?? '') ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
                <a href="<?= base_url('operateur/reporting/gains') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabs for Internal vs External gains -->
<ul class="nav nav-tabs mb-4" id="gainsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="interne-tab" data-bs-toggle="tab" data-bs-target="#interne" type="button" role="tab">
            <i class="bi bi-building"></i> Gains réseau interne
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="externe-tab" data-bs-toggle="tab" data-bs-target="#externe" type="button" role="tab">
            <i class="bi bi-globe"></i> Gains réseau externe
        </button>
    </li>
</ul>

<div class="tab-content" id="gainsTabsContent">
    <!-- Internal Gains Tab -->
    <div class="tab-pane fade show active" id="interne" role="tabpanel">
        <!-- Cartes chiffres clés - Interne -->
        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <div class="stat-label">Total des gains (interne)</div>
                    <div class="stat-value"><?= number_format($total_gains_interne, 0, '', ' ') ?> Ar</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stat-label">Gains sur retraits (interne)</div>
                    <?php 
                    $retraits = array_filter($gains_interne, fn($r) => $r['type_operation'] === 'retrait');
                    $totalRetraits = array_sum(array_column($retraits, 'frais'));
                    ?>
                    <div class="stat-value"><?= number_format($totalRetraits, 0, '', ' ') ?> Ar</div>
                    <small class="text-muted"><?= count($retraits) ?> opération(s)</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div class="stat-label">Gains sur transferts (interne)</div>
                    <?php 
                    $transferts = array_filter($gains_interne, fn($r) => $r['type_operation'] === 'transfert_envoi');
                    $totalTransferts = array_sum(array_column($transferts, 'frais'));
                    ?>
                    <div class="stat-value"><?= number_format($totalTransferts, 0, '', ' ') ?> Ar</div>
                    <small class="text-muted"><?= count($transferts) ?> opération(s)</small>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé - Interne -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="bi bi-table"></i> Détail des opérations (réseau interne)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Émetteur</th>
                                <th>Destinataire</th>
                                <th class="text-end">Montant (Ar)</th>
                                <th class="text-end">Gains (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gains_interne)): ?>
                                <?php foreach ($gains_interne as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['date_transaction'])) ?></td>
                                    <td>
                                        <?php if ($row['type_operation'] === 'retrait'): ?>
                                            <span class="badge bg-warning"><i class="bi bi-cash-stack"></i> Retrait</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="bi bi-arrow-left-right"></i> Transfert</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($row['telephone_client']) ?></td>
                                    <td><?= esc($row['telephone_destinataire']) ?></td>
                                    <td class="text-end"><?= number_format($row['montant'], 0, '', ' ') ?> Ar</td>
                                    <td class="text-end fw-bold text-success"><?= number_format($row['frais'], 0, '', ' ') ?> Ar</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Aucune opération interne enregistrée pour cette période.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($gains_interne)): ?>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="5">TOTAL</th>
                                <th class="text-end text-success"><?= number_format($total_gains_interne, 0, '', ' ') ?> Ar</th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- External Gains Tab -->
    <div class="tab-pane fade" id="externe" role="tabpanel">
        <!-- Cartes chiffres clés - Externe -->
        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <div class="stat-label">Total des gains (externe)</div>
                    <div class="stat-value"><?= number_format($total_gains_externe, 0, '', ' ') ?> Ar</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stat-label">Gains sur retraits (externe)</div>
                    <?php 
                    $retraits = array_filter($gains_externe, fn($r) => $r['type_operation'] === 'retrait');
                    $totalRetraits = array_sum(array_map(fn($r) => $r['frais'] + $r['commission'], $retraits));
                    ?>
                    <div class="stat-value"><?= number_format($totalRetraits, 0, '', ' ') ?> Ar</div>
                    <small class="text-muted"><?= count($retraits) ?> opération(s)</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div class="stat-label">Gains sur transferts (externe)</div>
                    <?php 
                    $transferts = array_filter($gains_externe, fn($r) => $r['type_operation'] === 'transfert_envoi');
                    $totalTransferts = array_sum(array_map(fn($r) => $r['frais'] + $r['commission'], $transferts));
                    ?>
                    <div class="stat-value"><?= number_format($totalTransferts, 0, '', ' ') ?> Ar</div>
                    <small class="text-muted"><?= count($transferts) ?> opération(s)</small>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé - Externe -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="bi bi-table"></i> Détail des opérations (réseau externe)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Émetteur</th>
                                <th>Destinataire</th>
                                <th class="text-end">Montant (Ar)</th>
                                <th class="text-end">Gains (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gains_externe)): ?>
                                <?php foreach ($gains_externe as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['date_transaction'])) ?></td>
                                    <td>
                                        <?php if ($row['type_operation'] === 'retrait'): ?>
                                            <span class="badge bg-warning"><i class="bi bi-cash-stack"></i> Retrait</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="bi bi-arrow-left-right"></i> Transfert</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($row['telephone_client']) ?></td>
                                    <td><?= esc($row['telephone_destinataire']) ?></td>
                                    <td class="text-end"><?= number_format($row['montant'], 0, '', ' ') ?> Ar</td>
                                    <td class="text-end fw-bold text-info"><?= number_format($row['frais'] + $row['commission'], 0, '', ' ') ?> Ar</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Aucune opération externe enregistrée pour cette période.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($gains_externe)): ?>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="5">TOTAL</th>
                                <th class="text-end text-info"><?= number_format($total_gains_externe, 0, '', ' ') ?> Ar</th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
