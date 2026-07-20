<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-cash-stack"></i> Effectuer un retrait</h4>
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-dark">
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

                    <div class="alert alert-secondary" id="fraisPreview" style="display:none;">
                        <i class="bi bi-calculator"></i> Frais estimés : <strong id="fraisEstime">-</strong> Ar &mdash;
                        Total débité : <strong id="totalEstime">-</strong> Ar
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg text-dark">
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
const preview = document.getElementById('fraisPreview');

input.addEventListener('input', function() {
    const montant = parseFloat(this.value);
    if (!montant || montant <= 0) { preview.style.display = 'none'; return; }

    fetch('<?= base_url('client/retrait/frais') ?>?montant=' + montant)
        .then(r => r.json())
        .then(data => {
            if (data.frais !== undefined) {
                document.getElementById('fraisEstime').textContent = new Intl.NumberFormat('fr-FR').format(data.frais);
                document.getElementById('totalEstime').textContent  = new Intl.NumberFormat('fr-FR').format(montant + data.frais);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        })
        .catch(() => { preview.style.display = 'none'; });
});
</script>
<?= $this->endSection() ?>
