<?php
namespace App\Controllers\Operateur;

use App\Controllers\BaseController;
use App\Helpers\PhoneHelper;

class ReportingController extends BaseController
{
    public function gains()
    {
        $db        = \Config\Database::connect();
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        // Internal gains (fees only)
        $builderInterne = $db->table('transactions t')
            ->select('t.type_operation, SUM(t.frais) as total_frais, COUNT(*) as nb_operations, SUM(t.montant) as total_montant')
            ->join('config_prefixes cp', 'SUBSTR(t.telephone_destinataire, 1, 3) = cp.prefixe OR SUBSTR(t.telephone_client, 1, 3) = cp.prefixe', 'left')
            ->where('cp.type_operateur', 'interne')
            ->whereIn('t.type_operation', ['retrait', 'transfert_envoi']);

        if ($dateDebut) {
            $builderInterne->where('t.date_transaction >=', $dateDebut . ' 00:00:00');
        }
        if ($dateFin) {
            $builderInterne->where('t.date_transaction <=', $dateFin . ' 23:59:59');
        }

        $builderInterne->groupBy('t.type_operation');
        $rowsInterne = $builderInterne->get()->getResultArray();

        // External gains (fees + commission)
        $builderExterne = $db->table('transactions t')
            ->select('t.type_operation, SUM(t.frais + t.commission) as total_gains, COUNT(*) as nb_operations, SUM(t.montant) as total_montant')
            ->join('config_prefixes cp', 'SUBSTR(t.telephone_destinataire, 1, 3) = cp.prefixe OR SUBSTR(t.telephone_client, 1, 3) = cp.prefixe', 'left')
            ->where('cp.type_operateur', 'externe')
            ->whereIn('t.type_operation', ['retrait', 'transfert_envoi']);

        if ($dateDebut) {
            $builderExterne->where('t.date_transaction >=', $dateDebut . ' 00:00:00');
        }
        if ($dateFin) {
            $builderExterne->where('t.date_transaction <=', $dateFin . ' 23:59:59');
        }

        $builderExterne->groupBy('t.type_operation');
        $rowsExterne = $builderExterne->get()->getResultArray();

        $gainsInterne  = [];
        $totalGainsInterne = 0.0;
        foreach ($rowsInterne as $row) {
            $gainsInterne[$row['type_operation']] = $row;
            $totalGainsInterne += (float) $row['total_frais'];
        }

        $gainsExterne  = [];
        $totalGainsExterne = 0.0;
        foreach ($rowsExterne as $row) {
            $gainsExterne[$row['type_operation']] = $row;
            $totalGainsExterne += (float) $row['total_gains'];
        }

        return view('operateur/reporting/gains', [
            'gains_interne'     => $gainsInterne,
            'total_gains_interne' => $totalGainsInterne,
            'gains_externe'     => $gainsExterne,
            'total_gains_externe' => $totalGainsExterne,
            'date_debut'        => $dateDebut,
            'date_fin'          => $dateFin,
        ]);
    }

    public function clients()
    {
        $db     = \Config\Database::connect();
        $search = $this->request->getGet('search');

        $builder = $db->table('clients c')
            ->select('c.id, c.nom_clients, c.telephone, c.solde, c.date_creation, MAX(t.date_transaction) as derniere_operation')
            ->join('transactions t', 'c.telephone = t.telephone_client', 'left')
            ->groupBy('c.id')
            ->orderBy('c.solde', 'DESC');

        if ($search) {
            $builder->like('c.telephone', $search);
        }

        $tous    = $builder->get()->getResultArray();
        $total   = count($tous);
        $perPage = 10;
        $page    = max((int) ($this->request->getGet('page') ?? 1), 1);
        $clients = array_slice($tous, ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $total, 'pager_bootstrap');

        return view('operateur/reporting/clients', [
            'clients' => $clients,
            'search'  => $search,
            'pager'   => $pager,
            'total'   => $total,
        ]);
    }

    public function settlements()
    {
        $db        = \Config\Database::connect();
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        // Query to get amounts to send to each external operator
        $builder = $db->table('transactions t')
            ->select('cp.nom_operateur, COUNT(DISTINCT t.id) as nb_transferts, SUM(t.montant) as total_montant')
            ->join('config_prefixes cp', 'SUBSTR(t.telephone_destinataire, 1, 3) = cp.prefixe', 'inner')
            ->where('cp.type_operateur', 'externe')
            ->where('t.type_operation', 'transfert_envoi');

        if ($dateDebut) {
            $builder->where('t.date_transaction >=', $dateDebut . ' 00:00:00');
        }
        if ($dateFin) {
            $builder->where('t.date_transaction <=', $dateFin . ' 23:59:59');
        }

        $builder->groupBy('cp.nom_operateur');
        $rows = $builder->get()->getResultArray();

        $totalGeneral = 0;
        foreach ($rows as $row) {
            $totalGeneral += (float) $row['total_montant'];
        }

        return view('operateur/reporting/settlements', [
            'settlements' => $rows,
            'total_general' => $totalGeneral,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);
    }
}
