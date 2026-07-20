<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Situation des comptes clients</h1>
        <p class="page-subtitle">Vue d'ensemble des comptes et de leurs soldes.</p>
    </div>
    <a href="<?= base_url('operateur/reporting/gains') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-graph-up-arrow"></i> Gains
    </a>
</div>

<!-- Barre de recherche -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Rechercher par numéro de téléphone</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           data-phone-input maxlength="13"
                           placeholder="Ex: 033..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Rechercher
                </button>
                <a href="<?= base_url('operateur/reporting/clients') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tableau clients -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table"></i> Liste des clients</span>
        <span class="badge bg-secondary"><?= $total ?? count($clients) ?> client(s)</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="clientsTable">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th class="text-end">Solde (Ar)</th>
                        <th>Dernière opération</th>
                        <th>Inscrit le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= esc($c['nom_clients']) ?></td>
                            <td><code data-phone-display><?= esc($c['telephone']) ?></code></td>
                            <td class="text-end fw-bold <?= $c['solde'] > 0 ? 'text-success' : 'text-muted' ?>">
                                <?= number_format($c['solde'], 0, '', ' ') ?> Ar
                            </td>
                            <td>
                                <?php if ($c['derniere_operation']): ?>
                                    <?= date('d/m/Y H:i', strtotime($c['derniere_operation'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Aucune</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Aucun client trouvé.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center mt-3">
            <?= $pager->links('default', 'pager_bootstrap') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
