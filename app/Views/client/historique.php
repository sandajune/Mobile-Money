<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4><i class="bi bi-clock-history"></i> Mon Historique des Transactions</h4>
            <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-left"></i> Retour Dashboard
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
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
                            <td><?= date('d/m/Y H:i', strtotime($tx['date_transaction'])) ?></td>
                            <td>
                                <?php if($tx['type_operation'] == 'depot'): ?>
                                    <span class="badge bg-success"><i class="bi bi-cash-coin"></i> Dépôt</span>
                                <?php elseif($tx['type_operation'] == 'retrait'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-cash-stack"></i> Retrait</span>
                                <?php elseif($tx['type_operation'] == 'transfert_envoi'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-arrow-up"></i> Transfert Envoyé</span>
                                <?php elseif($tx['type_operation'] == 'transfert_reception'): ?>
                                    <span class="badge bg-info"><i class="bi bi-arrow-down"></i> Transfert Reçu</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= esc($tx['type_operation']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= number_format($tx['montant'], 0, '', ' ') ?> Ar</strong></td>
                            <td class="<?= $tx['frais'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                <?= number_format($tx['frais'], 0, '', ' ') ?> Ar
                            </td>
                            <td>
                                <?php if($tx['telephone_destinataire']): ?>
                                    <?php if($tx['type_operation'] == 'transfert_envoi'): ?>
                                        <span class="text-muted">Vers: </span><strong><?= esc($tx['telephone_destinataire']) ?></strong>
                                    <?php elseif($tx['type_operation'] == 'transfert_reception'): ?>
                                        <span class="text-muted">De: </span><strong><?= esc($tx['telephone_destinataire']) ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">Dest: </span><?= esc($tx['telephone_destinataire']) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                <i class="bi bi-inbox"></i> Aucune transaction enregistrée.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($pager)): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links('default', 'bootstrap_full') ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>