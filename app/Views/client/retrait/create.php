<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-cash-stack"></i> Effectuer un retrait</h4>
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('client/retrait/preview') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label for="montant" class="form-label fw-semibold">Montant à retirer (Ar)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" class="form-control" id="montant" name="montant"
                                   min="1" step="1" placeholder="Ex: 5000" required autofocus>
                            <span class="input-group-text">Ar</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="frais_inclus" name="frais_inclus" value="1">
                            <label class="form-check-label" for="frais_inclus">
                                <i class="bi bi-info-circle"></i> Inclure les frais dans le montant
                            </label>
                        </div>
                        <small class="text-muted">Si coché, le montant saisi est le total débité (frais inclus). Sinon, les frais s'ajoutent au montant.</small>
                    </div>

                    <div class="alert alert-secondary" id="fraisPreview" style="display:none;">
                        <i class="bi bi-calculator"></i> Frais estimés : <strong id="fraisEstime">-</strong> Ar &mdash;
                        Montant net : <strong id="montantNetEstime">-</strong> Ar &mdash;
                        Total débité : <strong id="totalEstime">-</strong> Ar
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="bi bi-eye"></i> Prévisualiser le retrait
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
// Fetch barème depuis l'API et afficher estimation de frais
const input = document.getElementById('montant');
const fraisInclusCheckbox = document.getElementById('frais_inclus');
const preview = document.getElementById('fraisPreview');

function updateFraisPreview() {
    const montant = parseFloat(input.value);
    const fraisInclus = fraisInclusCheckbox.checked;
    
    if (!montant || montant <= 0) { 
        preview.style.display = 'none'; 
        return; 
    }

    const url = '<?= base_url('client/retrait/frais') ?>?montant=' + montant + '&frais_inclus=' + (fraisInclus ? '1' : '0');
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.frais !== undefined) {
                document.getElementById('fraisEstime').textContent = new Intl.NumberFormat('fr-FR').format(data.frais);
                document.getElementById('montantNetEstime').textContent = new Intl.NumberFormat('fr-FR').format(data.montant_net);
                document.getElementById('totalEstime').textContent = new Intl.NumberFormat('fr-FR').format(data.montant_total);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        })
        .catch(() => { preview.style.display = 'none'; });
}

input.addEventListener('input', updateFraisPreview);
fraisInclusCheckbox.addEventListener('change', updateFraisPreview);
</script>
<?= $this->endSection() ?>
