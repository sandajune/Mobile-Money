# 🚀 VERSION 2 — Livraison à 17h10 (Tag v2)
 
> Basé sur le `base.sql` réel du projet (tables `operateurs`, `config_prefixes`, `bareme_frais`, `clients`, `transactions`).
> Constat important : `config_prefixes` contient déjà Orange/Airtel/Yas sans distinction "notre réseau" vs "réseau externe" — il faut donc faire évoluer le schéma avant de coder les fonctionnalités.
 
---
 
## 🔧 Tâches communes V2 (à faire ensemble, avant de se séparer)
 
### 1. Évolution du schéma (`base.sql` à mettre à jour + migration CodeIgniter)
 
- [ ] **`config_prefixes`** : ajouter une notion "interne / externe"
```sql
  ALTER TABLE config_prefixes ADD COLUMN type_operateur TEXT NOT NULL DEFAULT 'interne';
  ALTER TABLE config_prefixes ADD COLUMN commission_pourcentage REAL DEFAULT 0; 
```
  - [ ] Décider qui est "interne" (notre opérateur, ex: le préfixe utilisé par les clients de la plateforme) vs "externe" (Orange, Airtel, Yas... vers qui on peut transférer avec commission)
  - [ ] Mettre à jour les données de test dans `base.sql` en conséquence (ex: `033`/`037` = interne, `032`/`031`/`038` = externe)
  - [ ] Ajouter une contrainte logique : `commission_pourcentage` obligatoire (>0) si `type_operateur = 'externe'`, forcé à 0 si `interne` (à valider côté back, SQLite ne gère pas bien les CHECK conditionnels après coup)
- [ ] **`transactions`** : ajouter le nécessaire pour frais inclus + envoi multiple
```sql
  ALTER TABLE transactions ADD COLUMN frais_inclus INTEGER NOT NULL DEFAULT 0; 
  ALTER TABLE transactions ADD COLUMN groupe_envoi TEXT DEFAULT NULL; 
  ALTER TABLE transactions ADD COLUMN commission REAL NOT NULL DEFAULT 0; 
```
  - [ ] ⚠️ Attention : SQLite ne permet pas de modifier un `CHECK` existant avec un simple `ALTER TABLE`. Si on doit élargir `type_operation` (ex: ajouter `'transfert_externe'`), il faudra soit s'en passer (garder `transfert_envoi`/`transfert_reception` et distinguer via `config_prefixes`), soit recréer la table (`CREATE TABLE ... RENAME`, copier les données, `DROP`/`RENAME`). **Décider ensemble de l'approche avant de coder.**
  - [ ] Mettre à jour les migrations CodeIgniter correspondantes (nouvelle migration `V2_AlterTables` plutôt que modifier la migration V1 déjà livrée)
  - [ ] Mettre à jour `base.sql` avec le schéma final + nouvelles données de test (au moins 1 client avec un préfixe externe simulé pour tester les transferts sortants)
- [ ] Mettre à jour `Taches.md` (nouvelle section "Version 2") au fur et à mesure, pas seulement à la fin
- [ ] Se répartir qui merge/tag `v2` à 17h10
---
 
## 👤 Sanda — Version 2
 
### Côté opérateur
 
- [ ] **Configuration des préfixes valables pour les autres opérateurs (ex: 032, 031, …)**
  - Back
    - [ ] Étendre `Operateur\PrefixeController` (déjà créé en V1) pour gérer le champ `type_operateur` (interne/externe)
    - [ ] Validation : un préfixe externe ne doit pas entrer en conflit avec un préfixe interne existant
    - [ ] Endpoint/traitement pour lister uniquement les préfixes externes (utile pour le formulaire de commission ci-dessous)
  - Front
    - [ ] Ajouter un champ "Type" (radio ou select : Interne / Externe) dans le formulaire de préfixe existant
    - [ ] Dans la liste des préfixes, ajouter une colonne/badge "Interne" ou "Externe" (couleurs différentes)
    - [ ] Filtrer/regrouper visuellement la liste par type
- [ ] **Configuration du % de commission en plus pour les transferts vers les autres opérateurs**
  - Back
    - [ ] Ajouter la logique de sauvegarde de `commission_pourcentage` sur un préfixe externe (via le même contrôleur ou un contrôleur dédié `CommissionController`)
    - [ ] Validation : pourcentage entre 0 et 100, obligatoire si externe
    - [ ] Fonction utilitaire réutilisable : `calculerCommission(montant, pourcentage)` (à utiliser côté client pour le transfert externe)
  - Front
    - [ ] Champ "% commission" visible uniquement quand "Externe" est sélectionné (afficher/masquer en JS)
    - [ ] Afficher le % de commission dans la liste des préfixes externes
    - [ ] Formulaire d'édition rapide du % pour un opérateur externe déjà créé
### Côté client
 
- [ ] **Envoi multiple vers plusieurs numéros (diviser le montant pour chaque numéro)**
  - Back
    - [ ] Étendre `Client\TransfertController` avec une méthode `envoiMultiple()`
    - [ ] Traiter une liste de numéros destinataires + un montant total (ou un montant par destinataire, à décider avec Tsiory — cf. son option "frais inclus")
    - [ ] Diviser le montant total équitablement entre les numéros (gérer les arrondis : ex. 1000 Ar / 3 = 333,33 → décider d'une règle d'arrondi, ex. dernier destinataire récupère le reliquat)
    - [ ] Vérifier que chaque numéro destinataire existe et est différent de l'émetteur ; rejeter les doublons dans la liste
    - [ ] Calculer les frais/commission **pour chaque envoi individuel** selon son montant (le barème dépend de la tranche, donc chaque envoi peut avoir un frais différent si les montants divisés diffèrent)
    - [ ] Vérifier que le solde de l'émetteur couvre la somme (montants + tous les frais)
    - [ ] Générer un `groupe_envoi` (UUID) commun à toutes les lignes de `transactions` créées pour cet envoi multiple
    - [ ] Transaction SQL globale : soit tous les envois passent, soit aucun (rollback si un destinataire est invalide en cours de route)
  - Front
    - [ ] Formulaire dynamique : bouton "Ajouter un destinataire" (JS, ajout de champs numéro sans recharger la page), bouton pour retirer une ligne
    - [ ] Champ montant total à répartir (ou choix "montant total à diviser" vs "même montant pour chacun" si le temps le permet)
    - [ ] Récapitulatif avant confirmation : tableau (destinataire, montant reçu, frais) + total débité
    - [ ] Affichage du résultat groupé dans l'historique (les lignes partageant le même `groupe_envoi` affichées ensemble, ex: "Envoi multiple à 3 destinataires")
---
 
## 👤 Tsiory — Version 2
 
### Côté opérateur
 
- [ ] **Sur la page "Situation gain via les différents frais", séparer opérateur (interne) et autres opérateurs (externe)**
  - Back
    - [ ] Adapter la requête d'agrégation de V1 : `JOIN` entre `transactions` et `config_prefixes` (sur le préfixe du `telephone_destinataire` ou `telephone_client` selon le sens) pour déterminer `interne`/`externe`
    - [ ] Deux agrégats séparés : somme des `frais` (interne) et somme des `frais` + `commission` (externe)
    - [ ] Fonction utilitaire pour extraire le préfixe d'un numéro de téléphone (ex: 3 premiers chiffres après le `0`) et le matcher à `config_prefixes.prefixe`
  - Front
    - [ ] Scinder la page reporting existante en deux blocs/onglets : "Gains réseau interne" et "Gains réseau externe (commissions)"
    - [ ] Garder les chiffres clés déjà présents en V1 pour la partie interne, ajouter les mêmes indicateurs pour l'externe
- [ ] **Situation des montants à envoyer à chaque opérateur**
  - Back
    - [ ] Requête agrégeant les transferts sortants vers chaque opérateur externe (`GROUP BY` sur l'opérateur identifié via le préfixe destinataire), somme des montants transférés (hors frais/commission, ou avec — à préciser dans l'affichage)
    - [ ] Prendre en compte les lignes issues d'un envoi multiple (`groupe_envoi`) sans les compter en double si un même destinataire apparaît sur deux préfixes différents (cas limite à documenter)
  - Front
    - [ ] Nouvelle page/tableau : Opérateur externe | Nombre de transferts | Montant total à reverser
    - [ ] Total général en bas de tableau
### Côté client
 
- [ ] **Option "inclure les frais de retrait lors de l'envoi"**
  - Back
    - [ ] Étendre `Client\RetraitController` (ou `TransfertController` selon ce que "envoi" désigne ici — à clarifier : cela concerne le retrait ou le transfert ? probablement le **retrait**, à confirmer avec l'énoncé/l'enseignant) pour gérer le paramètre `frais_inclus`
    - [ ] Logique si `frais_inclus = 1` : le montant saisi par le client est le montant **total débité** (frais compris), donc le montant net reçu/retiré = montant saisi − frais correspondant à la tranche
    - [ ] Logique si `frais_inclus = 0` (comportement V1 par défaut) : le montant saisi est le montant net, les frais s'ajoutent en plus
    - [ ] Attention à la détermination de la tranche de frais : se base-t-elle sur le montant saisi (brut) ou sur le montant net final ? Décider d'une règle simple et cohérente et la documenter dans `Taches.md`
    - [ ] Enregistrer `frais_inclus` dans la ligne `transactions` créée
  - Front
    - [ ] Ajouter une case à cocher "Inclure les frais dans le montant" sur le formulaire de retrait
    - [ ] Recalcul dynamique (JS ou après soumission) du récapitulatif selon l'état de la case : montant net vs montant débité vs frais
    - [ ] Affichage clair dans la confirmation finale des deux montants (brut débité / net reçu)
---
