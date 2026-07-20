<?php
namespace App\Controllers\Operateur;

use App\Controllers\BaseController;

class ReportingController extends BaseController
{
    public function gains()
    {
        $db        = \Config\Database::connect();
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $builder = $db->table('transactions')
            ->select('type_operation, SUM(frais) as total_frais, COUNT(*) as nb_operations, SUM(montant) as total_montant')
            ->whereIn('type_operation', ['retrait', 'transfert_envoi']);

        if ($dateDebut) {
            $builder->where('date_transaction >=', $dateDebut . ' 00:00:00');
        }
        if ($dateFin) {
            $builder->where('date_transaction <=', $dateFin . ' 23:59:59');
        }

        $builder->groupBy('type_operation');
        $rows = $builder->get()->getResultArray();

        $gains       = [];
        $totalGains  = 0.0;
        foreach ($rows as $row) {
            $gains[$row['type_operation']] = $row;
            $totalGains += (float) $row['total_frais'];
        }

        return view('operateur/reporting/gains', [
            'gains'       => $gains,
            'total_gains' => $totalGains,
            'date_debut'  => $dateDebut,
            'date_fin'    => $dateFin,
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
}
