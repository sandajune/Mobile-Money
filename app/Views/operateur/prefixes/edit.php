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
<?= $this->endSection() ?>
