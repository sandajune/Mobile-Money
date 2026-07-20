<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\TransactionModel;
use App\Models\PrefixeModel;

class TransfertController extends BaseController
{
    public function create()
    {
        return view('client/transfert/create');
    }

    /** Point AJAX – vérifie si un numéro destinataire existe. */
    public function checkDestinataire()
    {
        $telephone = $this->request->getPost('telephone');
        $dest      = (new ClientModel())->where('telephone', $telephone)->first();

        $payload = ['exists' => (bool) $dest];
        if ($dest) {
            $payload['nom'] = $dest['nom_clients'];
        }

        return $this->response->setJSON($payload);
    }

    public function preview()
    {
        $montant      = $this->request->getPost('montant');
        $telephoneDest = trim($this->request->getPost('telephone_destinataire'));

        if (empty($telephoneDest)) {
            return redirect()->back()->with('error', 'Le numéro destinataire est requis.');
        }
        if ($telephoneDest === session()->get('telephone')) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
        }
        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être un nombre supérieur à 0.');
        }

        $montant      = (float) $montant;
        $clientModel  = new ClientModel();
        $destinataire = $clientModel->where('telephone', $telephoneDest)->first();

        if (!$destinataire) {
            return redirect()->back()->with('error', 'Le numéro destinataire « ' . esc($telephoneDest) . ' » n\'existe pas.');
        }

        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'transfert');
        if (!$tranche) {
            return redirect()->back()->with('error', 'Aucun barème de frais disponible pour ce montant.');
        }

        $frais      = $tranche['frais'];
        $totalDebit = $montant + $frais;
        $emetteur   = $clientModel->find(session()->get('id'));

        if ($emetteur['solde'] < $totalDebit) {
            return redirect()->back()->with('error',
                'Solde insuffisant. Il vous faut ' . number_format($totalDebit, 0, '', ' ') . ' Ar (montant + frais ' . number_format($frais, 0, '', ' ') . ' Ar), mais votre solde est de ' . number_format($emetteur['solde'], 0, '', ' ') . ' Ar.');
        }

        return view('client/transfert/confirm', [
            'montant'                => $montant,
            'frais'                  => $frais,
            'total_debit'            => $totalDebit,
            'solde_actuel'           => $emetteur['solde'],
            'nouveau_solde'          => $emetteur['solde'] - $totalDebit,
            'telephone_destinataire' => $telephoneDest,
            'nom_destinataire'       => $destinataire['nom_clients'],
        ]);
    }

    public function store()
    {
        $montant      = $this->request->getPost('montant');
        $telephoneDest = trim($this->request->getPost('telephone_destinataire'));

        if (empty($telephoneDest) || empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Données invalides.');
        }
        if ($telephoneDest === session()->get('telephone')) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Transfert vers vous-même non autorisé.');
        }

        $montant      = (float) $montant;
        $clientModel  = new ClientModel();
        $destinataire = $clientModel->where('telephone', $telephoneDest)->first();

        if (!$destinataire) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Le numéro destinataire n\'existe pas.');
        }

        $tranche = (new BaremeFraisModel())->getFraisForMontant($montant, 'transfert');
        if (!$tranche) {
            return redirect()->to(base_url('client/transfert'))->with('error', 'Aucun barème de frais disponible pour ce montant.');
        }

        $frais            = $tranche['frais'];
        $totalDebit       = $montant + $frais;
        $emetteurId       = session()->get('id');
        $telephoneEmetteur = session()->get('telephone');
        $db               = \Config\Database::connect();

        $db->transBegin();
        try {
            $emetteur = $clientModel->find($emetteurId);

            if ($emetteur['solde'] < $totalDebit) {
                $db->transRollback();
                return redirect()->to(base_url('client/transfert'))->with('error', 'Solde insuffisant.');
            }

            $nouveauSolde = $emetteur['solde'] - $totalDebit;

            // Débiter l'émetteur
            $clientModel->update($emetteurId, ['solde' => $nouveauSolde]);
            // Créditer le destinataire
            $clientModel->update($destinataire['id'], ['solde' => $destinataire['solde'] + $montant]);

            $txModel = new TransactionModel();
            // Ligne émetteur
            $txModel->insert([
                'telephone_client'       => $telephoneEmetteur,
                'telephone_destinataire' => $telephoneDest,
                'type_operation'         => 'transfert_envoi',
                'montant'                => $montant,
                'frais'                  => $frais,
            ]);
            // Ligne destinataire
            $txModel->insert([
                'telephone_client'       => $telephoneDest,
                'telephone_destinataire' => $telephoneEmetteur,
                'type_operation'         => 'transfert_reception',
                'montant'                => $montant,
                'frais'                  => 0.0,
            ]);

            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('client/transfert'))->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        return redirect()->to(base_url('client/dashboard'))
            ->with('success', 'Transfert de ' . number_format($montant, 0, '', ' ') . ' Ar envoyé à ' . esc($telephoneDest) . '. Frais : ' . number_format($frais, 0, '', ' ') . ' Ar.');
    }

    public function createMultiple()
    {
        return view('client/transfert/create_multiple');
    }

    public function previewMultiple()
    {
        $modeDivision = $this->request->getPost('mode_division'); // 'total' ou 'par_destinataire'
        $destinataires = $this->request->getPost('destinataires'); // array of phone numbers
        $montant = $this->request->getPost('montant');

        if (empty($destinataires) || !is_array($destinataires)) {
            return redirect()->back()->with('error', 'Veuillez ajouter au moins un destinataire.');
        }

        // Nettoyer et valider les destinataires
        $destinataires = array_filter(array_map('trim', $destinataires));
        $destinataires = array_unique($destinataires); // Supprimer les doublons

        if (empty($destinataires)) {
            return redirect()->back()->with('error', 'Veuillez ajouter au moins un destinataire valide.');
        }

        if (empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->back()->with('error', 'Le montant doit être un nombre supérieur à 0.');
        }

        $montant = (float)$montant;
        $clientModel = new ClientModel();
        $baremeModel = new BaremeFraisModel();
        $prefixeModel = new PrefixeModel();
        $emetteur = $clientModel->find(session()->get('id'));
        $telephoneEmetteur = session()->get('telephone');

        $detailsEnvois = [];
        $totalDebit = 0;
        $totalFrais = 0;
        $totalCommission = 0;

        foreach ($destinataires as $telephoneDest) {
            // Vérifier que le destinataire n'est pas l'émetteur
            if ($telephoneDest === $telephoneEmetteur) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
            }

            // Vérifier que le destinataire existe
            $destinataire = $clientModel->where('telephone', $telephoneDest)->first();
            if (!$destinataire) {
                return redirect()->back()->with('error', 'Le numéro destinataire « ' . esc($telephoneDest) . ' » n\'existe pas.');
            }

            // Calculer le montant pour ce destinataire
            if ($modeDivision === 'total') {
                // Diviser le montant total équitablement
                $montantParDestinataire = $montant / count($destinataires);
            } else {
                // Montant par destinataire déjà spécifié
                $montantParDestinataire = $montant;
            }

            // Arrondi : si décimale, donner +1 au dernier destinataire
            $index = array_search($telephoneDest, $destinataires);
            if ($index === count($destinataires) - 1 && $modeDivision === 'total') {
                // Dernier destinataire : ajouter le reliquat
                $montantArrondi = round($montantParDestinataire, 2);
                $sommeArrondisPrecedents = array_sum(array_column($detailsEnvois, 'montant'));
                $reliquat = $montant - $sommeArrondisPrecedents;
                $montantFinal = max(0, $reliquat);
            } else {
                $montantFinal = round($montantParDestinataire, 2);
            }

            // Calculer les frais
            $tranche = $baremeModel->getFraisForMontant($montantFinal, 'transfert');
            if (!$tranche) {
                return redirect()->back()->with('error', 'Aucun barème de frais disponible pour le montant ' . number_format($montantFinal, 0, '', ' ') . ' Ar.');
            }
            $frais = $tranche['frais'];

            // Déterminer si c'est un transfert externe et calculer la commission
            $prefixeDest = substr($telephoneDest, 0, 3);
            $configPrefixe = $prefixeModel->where('prefixe', $prefixeDest)->first();
            $commission = 0;
            $estExterne = false;

            if ($configPrefixe && $configPrefixe['type_operateur'] === 'externe') {
                $estExterne = true;
                $commission = ($montantFinal * $configPrefixe['commission_pourcentage']) / 100;
            }

            $totalPourDestinataire = $montantFinal + $frais + $commission;

            $detailsEnvois[] = [
                'telephone' => $telephoneDest,
                'nom' => $destinataire['nom_clients'],
                'montant' => $montantFinal,
                'frais' => $frais,
                'commission' => $commission,
                'est_externe' => $estExterne,
                'total' => $totalPourDestinataire
            ];

            $totalDebit += $totalPourDestinataire;
            $totalFrais += $frais;
            $totalCommission += $commission;
        }

        // Vérifier le solde
        if ($emetteur['solde'] < $totalDebit) {
            return redirect()->back()->with('error',
                'Solde insuffisant. Il vous faut ' . number_format($totalDebit, 0, '', ' ') . ' Ar au total, mais votre solde est de ' . number_format($emetteur['solde'], 0, '', ' ') . ' Ar.');
        }

        return view('client/transfert/confirm_multiple', [
            'mode_division' => $modeDivision,
            'details_envois' => $detailsEnvois,
            'total_debit' => $totalDebit,
            'total_frais' => $totalFrais,
            'total_commission' => $totalCommission,
            'solde_actuel' => $emetteur['solde'],
            'nouveau_solde' => $emetteur['solde'] - $totalDebit,
            'destinataires_post' => $destinataires,
            'montant_post' => $montant
        ]);
    }

    public function storeMultiple()
    {
        $modeDivision = $this->request->getPost('mode_division');
        $destinataires = $this->request->getPost('destinataires');
        $montant = $this->request->getPost('montant');

        if (empty($destinataires) || !is_array($destinataires)) {
            return redirect()->to(base_url('client/transfert/multiple'))->with('error', 'Données invalides.');
        }

        $destinataires = array_filter(array_map('trim', $destinataires));
        $destinataires = array_unique($destinataires);

        if (empty($destinataires) || empty($montant) || !is_numeric($montant) || (float)$montant <= 0) {
            return redirect()->to(base_url('client/transfert/multiple'))->with('error', 'Données invalides.');
        }

        $montant = (float)$montant;
        $clientModel = new ClientModel();
        $baremeModel = new BaremeFraisModel();
        $prefixeModel = new PrefixeModel();
        $txModel = new TransactionModel();
        $emetteurId = session()->get('id');
        $telephoneEmetteur = session()->get('telephone');
        $db = \Config\Database::connect();

        // Générer un UUID pour le groupe d'envoi
        $groupeEnvoi = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $db->transBegin();
        try {
            $emetteur = $clientModel->find($emetteurId);
            
            // Premier passage : calculer tous les montants et vérifications
            $detailsEnvois = [];
            $totalDebit = 0;

            foreach ($destinataires as $telephoneDest) {
                if ($telephoneDest === $telephoneEmetteur) {
                    throw new \Exception('Transfert vers vous-même non autorisé.');
                }

                $destinataire = $clientModel->where('telephone', $telephoneDest)->first();
                if (!$destinataire) {
                    throw new \Exception('Le numéro destinataire n\'existe pas.');
                }

                // Calculer le montant pour ce destinataire
                if ($modeDivision === 'total') {
                    $montantParDestinataire = $montant / count($destinataires);
                } else {
                    $montantParDestinataire = $montant;
                }

                // Arrondi : dernier destinataire récupère le reliquat
                $index = array_search($telephoneDest, $destinataires);
                if ($index === count($destinataires) - 1 && $modeDivision === 'total') {
                    $montantArrondi = round($montantParDestinataire, 2);
                    $sommeArrondisPrecedents = array_sum(array_column($detailsEnvois, 'montant'));
                    $reliquat = $montant - $sommeArrondisPrecedents;
                    $montantFinal = max(0, $reliquat);
                } else {
                    $montantFinal = round($montantParDestinataire, 2);
                }

                // Calculer les frais
                $tranche = $baremeModel->getFraisForMontant($montantFinal, 'transfert');
                if (!$tranche) {
                    throw new \Exception('Aucun barème de frais disponible.');
                }
                $frais = $tranche['frais'];

                // Calculer la commission si externe
                $prefixeDest = substr($telephoneDest, 0, 3);
                $configPrefixe = $prefixeModel->where('prefixe', $prefixeDest)->first();
                $commission = 0;

                if ($configPrefixe && $configPrefixe['type_operateur'] === 'externe') {
                    $commission = ($montantFinal * $configPrefixe['commission_pourcentage']) / 100;
                }

                $totalPourDestinataire = $montantFinal + $frais + $commission;
                $totalDebit += $totalPourDestinataire;

                $detailsEnvois[] = [
                    'telephone' => $telephoneDest,
                    'destinataire' => $destinataire,
                    'montant' => $montantFinal,
                    'frais' => $frais,
                    'commission' => $commission
                ];
            }

            // Vérifier le solde
            if ($emetteur['solde'] < $totalDebit) {
                throw new \Exception('Solde insuffisant.');
            }

            // Débiter l'émetteur une seule fois
            $clientModel->update($emetteurId, ['solde' => $emetteur['solde'] - $totalDebit]);

            // Deuxième passage : créer les transactions et créditer les destinataires
            foreach ($detailsEnvois as $detail) {
                // Créditer le destinataire
                $clientModel->update($detail['destinataire']['id'], ['solde' => $detail['destinataire']['solde'] + $detail['montant']]);

                // Ligne émetteur
                $txModel->insert([
                    'telephone_client' => $telephoneEmetteur,
                    'telephone_destinataire' => $detail['telephone'],
                    'type_operation' => 'transfert_envoi',
                    'montant' => $detail['montant'],
                    'frais' => $detail['frais'],
                    'commission' => $detail['commission'],
                    'frais_inclus' => 0,
                    'groupe_envoi' => $groupeEnvoi
                ]);

                // Ligne destinataire
                $txModel->insert([
                    'telephone_client' => $detail['telephone'],
                    'telephone_destinataire' => $telephoneEmetteur,
                    'type_operation' => 'transfert_reception',
                    'montant' => $detail['montant'],
                    'frais' => 0.0,
                    'commission' => 0.0,
                    'frais_inclus' => 0,
                    'groupe_envoi' => $groupeEnvoi
                ]);
            }

            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('client/transfert/multiple'))->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }

        return redirect()->to(base_url('client/dashboard'))
            ->with('success', 'Envoi multiple à ' . count($destinataires) . ' destinataires effectué avec succès. Groupe : ' . $groupeEnvoi);
    }
}
