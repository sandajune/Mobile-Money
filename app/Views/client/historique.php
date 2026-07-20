<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="card shadow">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h4>Mon Historique des Transactions</h4>
        <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-light">Retour Dashboard</a>
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Frais</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($transactions)): foreach($transactions as $tx): ?>
                <tr>
                    <td><?= $tx['date_transaction'] ?></td>
                    <td>
                        <?php if($tx['type_operation'] == 'depot'): ?>
                            <span class="badge bg-success">Dépôt</span>
                        <?php elseif($tx['type_operation'] == 'retrait'): ?>
                            <span class="badge bg-warning text-dark">Retrait</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark">Transfert</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</strong></td>
                    <td class="text-danger"><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                    <td>
                        <?= $tx['telephone_destinataire'] ? 'Vers : ' . $tx['telephone_destinataire'] : 'N/A' ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" class="text-center">Aucune transaction enregistrée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            <?= $pager->links('default', 'bootstrap_full') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>