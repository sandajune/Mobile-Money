<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-arrow-left-right"></i> Effectuer un transfert</h4>
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('client/transfert/preview') ?>" method="post" id="transfertForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="telephone_destinataire" class="form-label fw-semibold">Numéro du destinataire</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input type="text" class="form-control" id="telephone_destinataire"
                                   name="telephone_destinataire" placeholder="Ex: 0331234567"
                                   maxlength="10" required autofocus>
                            <button type="button" class="btn btn-outline-secondary" id="checkBtn">
                                <i class="bi bi-search"></i> Vérifier
                            </button>
                        </div>
                        <div id="destFeedback" class="mt-1"></div>
                    </div>

                    <div class="mb-4">
                        <label for="montant" class="form-label fw-semibold">Montant à envoyer (Ar)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" class="form-control" id="montant" name="montant"
                                   min="1" step="1" placeholder="Ex: 5000" required>
                            <span class="input-group-text">Ar</span>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye"></i> Prévisualiser le transfert
                        </button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="<?= base_url('client/tarifs') ?>" class="text-muted small">
                        <i class="bi bi-info-circle"></i> Consulter les tarifs en vigueur
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkBtn').addEventListener('click', function() {
    const tel = document.getElementById('telephone_destinataire').value.trim();
    const feedback = document.getElementById('destFeedback');

    if (!tel) {
        feedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Veuillez saisir un numéro.</span>';
        return;
    }

    const formData = new FormData();
    formData.append('telephone', tel);
    // Add CSRF token
    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]');
    if (csrfToken) formData.append(csrfToken.name, csrfToken.value);

    fetch('<?= base_url('client/transfert/check') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.exists) {
                feedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Destinataire trouvé : <strong>' + data.nom + '</strong></span>';
            } else {
                feedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Ce numéro n\'est pas enregistré.</span>';
            }
        })
        .catch(() => {
            feedback.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-circle"></i> Impossible de vérifier le numéro.</span>';
        });
});
</script>
<?= $this->endSection() ?>
