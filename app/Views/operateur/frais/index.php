<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Gestion des barèmes de frais</h1>
        <p class="page-subtitle">Définissez les tranches de frais applicables aux retraits et transferts.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-lg"></i> Ajouter une tranche
    </button>
</div>

<ul class="nav nav-tabs mb-4" id="fraisTabs" role="tablist">
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

<div class="tab-content" id="fraisTabsContent">
    <!-- Retraits -->
    <div class="tab-pane fade show active" id="retrait">
        <div class="card shadow-sm">
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
                                <th>Actions</th>
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
                                        <td>
                                            <a href="<?= base_url('operateur/frais/editTranche/' . $tranche['id']) ?>"
                                               class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('operateur/frais/deleteTranche/' . $tranche['id']) ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tranche ?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Aucune tranche configurée pour les retraits</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links('retraits', 'pager_bootstrap') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Transferts -->
    <div class="tab-pane fade" id="transfert">
        <div class="card shadow-sm">
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
                                <th>Actions</th>
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
                                        <td>
                                            <a href="<?= base_url('operateur/frais/editTranche/' . $tranche['id']) ?>"
                                               class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('operateur/frais/deleteTranche/' . $tranche['id']) ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tranche ?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Aucune tranche configurée pour les transferts</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links('transferts', 'pager_bootstrap') ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une tranche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('operateur/frais/storeTranche') ?>" method="post">
                    <div class="mb-3">
                        <label for="type_operation" class="form-label">Type d'opération</label>
                        <select class="form-select" id="type_operation" name="type_operation" required>
                            <option value="retrait">Retrait</option>
                            <option value="transfert">Transfert</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="montant_min" class="form-label">Montant minimum (Ar)</label>
                            <input type="number" class="form-control" id="montant_min" name="montant_min"
                                   min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="montant_max" class="form-label">Montant maximum (Ar)</label>
                            <input type="number" class="form-control" id="montant_max" name="montant_max"
                                   min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="frais" class="form-label">Frais (Ar)</label>
                        <input type="number" class="form-control" id="frais" name="frais"
                               min="0" step="0.01" required>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation JS avant soumission
    const form = document.querySelector('#createModal form');
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
