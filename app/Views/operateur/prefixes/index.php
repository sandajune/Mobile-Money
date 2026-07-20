<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Gestion des préfixes</h1>
        <p class="page-subtitle">Associez chaque opérateur mobile à son préfixe téléphonique.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-lg"></i> Ajouter un préfixe
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Opérateur</th>
                        <th>Préfixe</th>
                        <th>Type d'opérateur</th>
                        <th>% Commission</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prefixes)): ?>
                        <?php foreach ($prefixes as $prefixe): ?>
                            <tr>
                                <td><?= $prefixe['id'] ?></td>
                                <td><?= esc($prefixe['nom_operateur']) ?></td>
                                <td><span class="badge bg-primary"><?= esc($prefixe['prefixe']) ?></span></td>
                                <td>
                                    <?php if ($prefixe['type_operateur'] === 'interne'): ?>
                                        <span class="badge bg-success">Interne</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Externe</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($prefixe['type_operateur'] === 'externe'): ?>
                                        <span class="badge bg-info"><?= number_format($prefixe['commission_pourcentage'], 2) ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($prefixe['date_creation'])) ?></td>
                                <td>
                                    <a href="<?= base_url('operateur/prefixes/edit/' . $prefixe['id']) ?>"
                                       class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </a>
                                    <a href="<?= base_url('operateur/prefixes/delete/' . $prefixe['id']) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce préfixe ?');">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Aucun préfixe configuré.
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

<!-- Modal Création -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un préfixe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('operateur/prefixes/store') ?>" method="post">
                    <div class="mb-3">
                        <label for="nom_operateur" class="form-label">Nom de l'opérateur</label>
                        <input type="text" class="form-control" id="nom_operateur" name="nom_operateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="prefixe" class="form-label">Préfixe (3 chiffres)</label>
                        <input type="text" class="form-control" id="prefixe" name="prefixe"
                               pattern="[0-9]{3}" maxlength="3" required>
                        <small class="text-muted">Ex: 032, 033, 038</small>
                    </div>
                    <div class="mb-3">
                        <label for="type_operateur" class="form-label">Type d'opérateur</label>
                        <select class="form-select" id="type_operateur" name="type_operateur" required onchange="toggleCommissionField()">
                            <option value="" selected disabled>Choisir le type...</option>
                            <option value="interne">Interne</option>
                            <option value="externe">Externe</option>
                        </select>
                    </div>
                    <div class="mb-3" id="commission_field" style="display: none;">
                        <label for="commission_pourcentage" class="form-label">% Commission</label>
                        <input type="number" class="form-control" id="commission_pourcentage" name="commission_pourcentage"
                               min="0" max="100" step="0.01">
                        <small class="text-muted">Pourcentage de commission pour les transferts vers cet opérateur externe (0-100)</small>
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
function toggleCommissionField() {
    const typeOperateur = document.getElementById('type_operateur').value;
    const commissionField = document.getElementById('commission_field');
    if (typeOperateur === 'externe') {
        commissionField.style.display = 'block';
        document.getElementById('commission_pourcentage').required = true;
    } else {
        commissionField.style.display = 'none';
        document.getElementById('commission_pourcentage').required = false;
        document.getElementById('commission_pourcentage').value = '';
    }
}
</script>
<?= $this->endSection() ?>
