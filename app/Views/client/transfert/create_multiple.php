<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-people"></i> Envoi multiple</h4>
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('client/transfert/previewMultiple') ?>" method="post" id="multipleForm">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mode de division du montant</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="mode_division" id="mode_total" value="total" checked>
                            <label class="btn btn-outline-primary" for="mode_total">
                                <i class="bi bi-diagram-3"></i> Diviser le montant total
                            </label>
                            <input type="radio" class="btn-check" name="mode_division" id="mode_par_dest" value="par_destinataire">
                            <label class="btn btn-outline-primary" for="mode_par_dest">
                                <i class="bi bi-person"></i> Même montant pour chacun
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="montant" class="form-label fw-semibold">Montant (Ar)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" class="form-control" id="montant" name="montant"
                                   min="1" step="1" placeholder="Ex: 10000" required>
                            <span class="input-group-text">Ar</span>
                        </div>
                        <small class="text-muted" id="montant_hint">
                            Ce montant sera divisé équitablement entre tous les destinataires.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Destinataires</label>
                        <div id="destinataires_container">
                            <div class="destinataire-row mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" class="form-control destinataire-input" 
                                           name="destinataires[]" placeholder="Numéro (ex: 032 12 345 67)"
                                           maxlength="13" data-phone-input required>
                                    <button type="button" class="btn btn-outline-danger remove-dest" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="dest-feedback mt-1"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm mt-2" id="addDestBtn">
                            <i class="bi bi-plus-circle"></i> Ajouter un destinataire
                        </button>
                    </div>

                    <div class="alert alert-info" id="summaryAlert" style="display: none;">
                        <i class="bi bi-info-circle"></i> 
                        <span id="summaryText">0 destinataire(s)</span>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="previewBtn" disabled>
                            <i class="bi bi-eye"></i> Prévisualiser l'envoi
                        </button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="<?= base_url('client/transfert') ?>" class="text-muted small">
                        <i class="bi bi-arrow-left-right"></i> Retour au transfert simple
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let destCount = 1;

document.getElementById('addDestBtn').addEventListener('click', function() {
    destCount++;
    const container = document.getElementById('destinataires_container');
    const newRow = document.createElement('div');
    newRow.className = 'destinataire-row mb-2';
    newRow.innerHTML = `
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-phone"></i></span>
            <input type="text" class="form-control destinataire-input" 
                   name="destinataires[]" placeholder="Numéro (ex: 032 12 345 67)"
                   maxlength="13" data-phone-input required>
            <button type="button" class="btn btn-outline-danger remove-dest">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="dest-feedback mt-1"></div>
    `;
    container.appendChild(newRow);
    updateRemoveButtons();
    updateSummary();
});

document.getElementById('destinataires_container').addEventListener('click', function(e) {
    if (e.target.closest('.remove-dest')) {
        const row = e.target.closest('.destinataire-row');
        if (document.querySelectorAll('.destinataire-row').length > 1) {
            row.remove();
            destCount--;
            updateRemoveButtons();
            updateSummary();
        }
    }
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.destinataire-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-dest');
        btn.style.display = rows.length > 1 ? 'block' : 'none';
    });
}

document.getElementById('mode_total').addEventListener('change', function() {
    document.getElementById('montant_hint').textContent = 'Ce montant sera divisé équitablement entre tous les destinataires.';
});

document.getElementById('mode_par_dest').addEventListener('change', function() {
    document.getElementById('montant_hint').textContent = 'Chaque destinataire recevra ce montant.';
});

document.getElementById('destinataires_container').addEventListener('input', function() {
    updateSummary();
});

function updateSummary() {
    const inputs = document.querySelectorAll('.destinataire-input');
    const filled = Array.from(inputs).filter(i => i.value.trim() !== '').length;
    const alert = document.getElementById('summaryAlert');
    const text = document.getElementById('summaryText');
    
    if (filled > 0) {
        alert.style.display = 'block';
        text.textContent = filled + ' destinataire(s) saisi(s)';
    } else {
        alert.style.display = 'none';
    }
    
    const previewBtn = document.getElementById('previewBtn');
    previewBtn.disabled = filled < 2;
}

// Vérification des numéros en temps réel
document.getElementById('destinataires_container').addEventListener('blur', function(e) {
    if (e.target.classList.contains('destinataire-input')) {
        const tel = window.PhoneFormatter.clean(e.target.value.trim());
        const feedback = e.target.closest('.destinataire-row').querySelector('.dest-feedback');
        
        if (tel.length === 10) {
            const formData = new FormData();
            formData.append('telephone', tel);
            const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]');
            if (csrfToken) formData.append(csrfToken.name, csrfToken.value);

            fetch('<?= base_url('client/transfert/check') ?>', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.exists) {
                        feedback.innerHTML = '<span class="text-success small"><i class="bi bi-check-circle"></i> ' + data.nom + '</span>';
                    } else {
                        feedback.innerHTML = '<span class="text-danger small"><i class="bi bi-x-circle"></i> Numéro inconnu</span>';
                    }
                })
                .catch(() => {
                    feedback.innerHTML = '<span class="text-warning small"><i class="bi bi-exclamation-circle"></i> Erreur vérification</span>';
                });
        }
    }
}, true);

// Nettoyer les numéros avant soumission
document.getElementById('multipleForm').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('.destinataire-input');
    inputs.forEach(input => {
        input.value = window.PhoneFormatter.clean(input.value);
    });
});
</script>
<?= $this->endSection() ?>
