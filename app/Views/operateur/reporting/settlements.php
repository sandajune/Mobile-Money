<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Situation des reversements opérateurs</h1>
        <p class="page-subtitle">Montants à envoyer à chaque opérateur externe.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('operateur/reporting/gains') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-currency-exchange"></i> Gains
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
                <a href="<?= base_url('operateur/reporting/settlements') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Carte chiffre clé global -->
<div class="row mb-4 g-3">
    <div class="col-md-6">
        <div class="stat-card h-100">
            <div class="stat-icon bg-info-subtle text-info">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-label">Total général à reverser</div>
            <div class="stat-value"><?= number_format($total_general, 0, '', ' ') ?> Ar</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card h-100">
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-building"></i>
            </div>
            <div class="stat-label">Nombre d'opérateurs concernés</div>
            <div class="stat-value"><?= count($settlements) ?></div>
        </div>
    </div>
</div>

<!-- Tableau des reversements -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <i class="bi bi-table"></i> Détail par opérateur externe
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Opérateur externe</th>
                        <th class="text-end">Nombre de transferts</th>
                        <th class="text-end">Montant total à reverser (Ar)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($settlements)): ?>
                        <?php foreach ($settlements as $row): ?>
                        <tr>
                            <td>
                                <span class="badge bg-info fs-6"><i class="bi bi-building"></i> <?= esc($row['nom_operateur']) ?></span>
                            </td>
                            <td class="text-end"><?= number_format($row['nb_transferts'], 0, '', ' ') ?></td>
                            <td class="text-end fw-bold text-info"><?= number_format($row['total_montant'], 0, '', ' ') ?> Ar</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Aucun transfert vers opérateur externe enregistré pour cette période.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($settlements)): ?>
                <tfoot class="table-dark">
                    <tr>
                        <th>TOTAL GÉNÉRAL</th>
                        <th class="text-end"><?= number_format(array_sum(array_column($settlements, 'nb_transferts')), 0, '', ' ') ?></th>
                        <th class="text-end text-info"><?= number_format($total_general, 0, '', ' ') ?> Ar</th>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
