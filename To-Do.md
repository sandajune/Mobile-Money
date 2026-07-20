# TODO — Projet Mobile Money (Examen S4 Info & Design)

**Binôme :** Sanda & Tsiory
**Tech :** PHP (CodeIgniter 4) + SQLite embarqué · HTML / CSS / JS / Bootstrap


##  Sanda — Version 1

### Côté opérateur

- [ ] **Configuration des préfixes valables de l'opérateur**
  - Back
    - [ ] Modèle `PrefixeModel` (table `prefixes`)
    - [ ] Contrôleur `Operateur\PrefixeController` : `index()` (liste), `create()`/`store()`, `edit($id)`/`update($id)`, `delete($id)`
    - [ ] Validation : format numérique, longueur (ex: 3 chiffres), unicité du préfixe
    - [ ] Empêcher la suppression d'un préfixe déjà utilisé par des clients (ou gérer le cas proprement)
  - Front
    - [ ] Page liste des préfixes (tableau Bootstrap : préfixe, statut actif/inactif, actions modifier/supprimer)
    - [ ] Formulaire d'ajout / modification (modal ou page dédiée)
    - [ ] Messages de succès/erreur (flashdata CodeIgniter)
    - [ ] Confirmation avant suppression (JS `confirm()` ou modal)

- [ ] **Création des types d'opérations (dépôt, retrait, transfert) avec barèmes de frais par tranche de montant (modifiable)**
  - Back
    - [ ] Modèle `TypeOperationModel` + `BaremeFraisModel`
    - [ ] Contrôleur `Operateur\TypeOperationController` : CRUD type d'opération
    - [ ] Endpoint(s) pour gérer les tranches de barème liées à un type (ajout/suppression/modif d'une ligne de tranche)
    - [ ] Validation : tranches non chevauchantes, `montant_min < montant_max`, frais ≥ 0
    - [ ] Réfléchir au format du frais (montant fixe vs pourcentage) — reprendre l'exemple donné dans l'énoncé
  - Front
    - [ ] Page listant les types d'opérations existants
    - [ ] Formulaire de création/édition avec **tableau dynamique de tranches** (bouton "ajouter une tranche" en JS, suppression d'une ligne sans recharger la page)
    - [ ] Tableau récapitulatif du barème (aperçu avant sauvegarde)
    - [ ] Validation côté front des champs numériques (montants positifs, cohérence min/max) avant envoi

### Côté client

- [ ] **Login automatique avec numéro de téléphone** (pas d'inscription préalable)
  - Back
    - [ ] Contrôleur `Client\AuthController` : `login()` (affiche formulaire), `authenticate()` (traite le POST)
    - [ ] Vérifier que le numéro correspond à un préfixe valide (jointure avec `prefixes`)
    - [ ] Créer automatiquement le compte client s'il n'existe pas encore (solde initial à 0, ou selon logique décidée)
    - [ ] Créer la session CodeIgniter (`session()->set(...)`) avec l'id client
    - [ ] Middleware/filtre d'authentification pour protéger les pages client (`app/Filters`)
    - [ ] Route de déconnexion (`logout()`)
  - Front
    - [ ] Formulaire de connexion simple (champ numéro de téléphone, bouton "Se connecter")
    - [ ] Affichage des erreurs (préfixe invalide, numéro mal formaté)
    - [ ] Redirection automatique vers le tableau de bord après connexion

- [ ] **Voir le solde**
  - Back
    - [ ] Méthode dans `ClientModel`/`CompteController` retournant le solde à jour du client connecté
  - Front
    - [ ] Page tableau de bord client : solde affiché en évidence (formaté en Ariary, ex: `1 500 Ar`)
    - [ ] Liens/boutons rapides vers dépôt, retrait, transfert, historique

- [ ] **Voir les historiques**
  - Back
    - [ ] Requête listant les opérations du client connecté (en tant qu'émetteur ET destinataire pour les transferts), triées par date décroissante
    - [ ] Pagination (CodeIgniter Pager) si beaucoup d'opérations
  - Front
    - [ ] Tableau : date, type d'opération, montant, frais, solde après opération, statut
    - [ ] Distinguer visuellement dépôt/retrait/transfert (badges Bootstrap de couleurs différentes)
    - [ ] Filtre optionnel par type d'opération ou par date

---

##  Tsiory — Version 1

### Côté opérateur

- [ ] **Situation des gains via les différents frais (retrait et transfert)**
  - Back
    - [ ] Requête d'agrégation `SUM(frais)` sur la table `operations`, groupée par type d'opération (retrait, transfert)
    - [ ] Exclure le dépôt du calcul des gains (sauf si l'énoncé prévoit des frais dessus)
    - [ ] Prévoir un filtre par période (jour/mois) si le temps le permet
  - Front
    - [ ] Page de reporting : cartes/chiffres clés (total gains, gains retrait, gains transfert)
    - [ ] Tableau détaillé optionnel (liste des opérations ayant généré des frais)

- [ ] **Situation des comptes clients**
  - Back
    - [ ] Requête listant tous les clients avec leur solde actuel
    - [ ] Trier par solde ou par date de dernière opération
  - Front
    - [ ] Page tableau : numéro de téléphone, solde, date de dernière opération
    - [ ] Barre de recherche par numéro (JS simple, filtre côté client ou requête AJAX)

### Côté client

- [ ] **Faire un dépôt** (supposé automatique)
  - Back
    - [ ] Contrôleur `Client\DepotController` : `create()` (formulaire), `store()` (traitement)
    - [ ] Logique métier : incrémenter le solde du client, enregistrer l'opération dans `operations` (type = dépôt, frais = 0 sauf indication contraire)
    - [ ] Utiliser une transaction SQL (BDD) pour garantir la cohérence solde/opération
    - [ ] Validation : montant > 0, montant numérique
  - Front
    - [ ] Formulaire de dépôt (champ montant)
    - [ ] Page/modal de confirmation avec récapitulatif (montant déposé, nouveau solde)

- [ ] **Faire un retrait** (supposé automatique)
  - Back
    - [ ] Contrôleur `Client\RetraitController`
    - [ ] Calculer les frais applicables selon `baremes_frais` en fonction du montant demandé
    - [ ] Vérifier que le solde est suffisant (montant + frais)
    - [ ] Décrémenter le solde, enregistrer l'opération (montant, frais, solde avant/après)
    - [ ] Gérer le cas d'échec (solde insuffisant) avec message clair
  - Front
    - [ ] Formulaire de retrait (champ montant)
    - [ ] Afficher dynamiquement (JS ou après soumission) les frais qui seront appliqués **avant** confirmation
    - [ ] Page de confirmation finale (montant, frais, nouveau solde)

- [ ] **Faire un transfert**
  - Back
    - [ ] Contrôleur `Client\TransfertController`
    - [ ] Vérifier que le numéro destinataire existe et est différent de l'émetteur
    - [ ] Calculer les frais applicables (barème transfert) selon le montant
    - [ ] Vérifier le solde suffisant chez l'émetteur, débiter l'émetteur, créditer le destinataire
    - [ ] Enregistrer l'opération avec `client_id` (émetteur) et `client_destinataire_id`
    - [ ] Transaction SQL pour garantir que débit/crédit se font ensemble ou pas du tout
  - Front
    - [ ] Formulaire de transfert (numéro destinataire, montant)
    - [ ] Vérification du numéro destinataire (message si inexistant) avant validation
    - [ ] Récapitulatif avant confirmation (destinataire, montant, frais, total débité)

