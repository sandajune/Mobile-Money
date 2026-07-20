<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h1 class="h3 mb-0">Modifier le préfixe</h1>
    </div>
    <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('operateur/prefixes/update/' . $prefixe['id']) ?>" method="post">
                <div class="mb-3">
                    <label for="nom_operateur" class="form-label">Nom de l'opérateur</label>
                    <input type="text" class="form-control" id="nom_operateur" name="nom_operateur" 
                           value="<?= esc($prefixe['nom_operateur']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="prefixe" class="form-label">Préfixe (3 chiffres)</label>
                    <input type="text" class="form-control" id="prefixe" name="prefixe" 
                           value="<?= esc($prefixe['prefixe']) ?>" pattern="[0-9]{3}" maxlength="3" required>
                    <small class="text-muted">Ex: 032, 033, 038</small>
                </div>
                <div class="mb-3">
                    <label for="type_operateur" class="form-label">Type d'opérateur</label>
                    <select class="form-select" id="type_operateur" name="type_operateur" required onchange="toggleCommissionField()">
                        <option value="" disabled>Choisir le type...</option>
                        <option value="interne" <?= $prefixe['type_operateur'] === 'interne' ? 'selected' : '' ?>>Interne</option>
                        <option value="externe" <?= $prefixe['type_operateur'] === 'externe' ? 'selected' : '' ?>>Externe</option>
                    </select>
                </div>
                <div class="mb-3" id="commission_field" style="display: <?= $prefixe['type_operateur'] === 'externe' ? 'block' : 'none' ?>;">
                    <label for="commission_pourcentage" class="form-label">% Commission</label>
                    <input type="number" class="form-control" id="commission_pourcentage" name="commission_pourcentage"
                           value="<?= $prefixe['type_operateur'] === 'externe' ? number_format($prefixe['commission_pourcentage'], 2) : '' ?>"
                           min="0" max="100" step="0.01" <?= $prefixe['type_operateur'] === 'externe' ? 'required' : '' ?>>
                    <small class="text-muted">Pourcentage de commission pour les transferts vers cet opérateur externe (0-100)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date de création</label>
                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($prefixe['date_creation'])) ?>" disabled>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

<script>
function toggleCommissionField() {
    const typeOperateur = document.getElementById('type_operateur').value;
    const commissionField = document.getElementById('commission_field');
    const commissionInput = document.getElementById('commission_pourcentage');
    if (typeOperateur === 'externe') {
        commissionField.style.display = 'block';
        commissionInput.required = true;
    } else {
        commissionField.style.display = 'none';
        commissionInput.required = false;
        commissionInput.value = '';
    }
}
</script>
<?= $this->endSection() ?>
