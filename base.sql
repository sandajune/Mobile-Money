-- =====================================================================
-- STRUCTURE DE LA BASE DE DONNÉES (SQLite) - MOBILE MONEY V1
-- Fichier unique : base.sql
-- =====================================================================

-- Désactiver temporairement les clés étrangères pour éviter les conflits de suppression
PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS bareme_frais;
DROP TABLE IF EXISTS config_prefixes;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS operateurs;

PRAGMA foreign_keys = ON;

-- =====================================================================
-- 0. TABLE : OPÉRATEURS (AUTHENTIFICATION)
-- =====================================================================
CREATE TABLE operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    nom TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
-- 1. TABLE : CONFIGURATION DES PRÉFIXES
-- =====================================================================
CREATE TABLE config_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom_operateur text  not null,
    prefixe TEXT NOT NULL UNIQUE, -- ex: '033', '037'
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
-- 2. TABLE : BARÈME DES FRAIS
-- =====================================================================
CREATE TABLE bareme_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation TEXT NOT NULL,          -- 'retrait' ou 'transfert'
    montant_min REAL NOT NULL,             -- Borne inférieure de la tranche
    montant_max REAL NOT NULL,             -- Borne supérieure de la tranche
    frais REAL NOT NULL,                   -- Montant fixe des frais pour cette tranche
    CHECK (type_operation IN ('retrait', 'transfert')),
    CHECK (montant_max >= montant_min)
);

-- =====================================================================
-- 3. TABLE : CLIENTS
-- =====================================================================
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom_clients text   not null,
    telephone TEXT NOT NULL UNIQUE,        -- Numéro de téléphone unique servant de login
    solde REAL NOT NULL DEFAULT 0.0,       -- Solde actuel du client
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (solde >= 0.0)                   -- Le solde ne peut pas être négatif
);

-- Index pour accélérer la vérification du numéro lors du login automatique
CREATE INDEX idx_clients_telephone ON clients(telephone);

-- =====================================================================
-- 4. TABLE : TRANSACTIONS (HISTORIQUE)
-- =====================================================================
CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone_client TEXT NOT NULL,                    -- Le client principal qui déclenche l'action
    telephone_destinataire TEXT DEFAULT NULL,          -- Rempli uniquement en cas de transfert
    type_operation TEXT NOT NULL,                      -- 'depot', 'retrait', 'transfert_envoi', 'transfert_reception'
    montant REAL NOT NULL,                             -- Montant brut de l'opération
    frais REAL NOT NULL DEFAULT 0.0,                   -- Frais appliqués (0 pour dépôt)
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (type_operation IN ('depot', 'retrait', 'transfert_envoi', 'transfert_reception')),
    CHECK (montant > 0.0),
    CHECK (frais >= 0.0)
);



INSERT INTO bareme_frais (type_operation, montant_min, montant_max, frais) VALUES
('retrait', 100, 1000, 50),
('retrait', 1001, 5000, 50),
('retrait', 5001, 10000, 100),
('retrait', 10001, 25000, 200),
('retrait', 25001, 50000, 400),
('retrait', 50001, 100000, 800),
('retrait', 100001, 250000, 1500),
('retrait', 250001, 500000, 1500),
('retrait', 500001, 1000000, 2500),
('retrait', 1000001, 2000000, 3000);

INSERT INTO bareme_frais (type_operation, montant_min, montant_max, frais) VALUES
('transfert', 100, 1000, 50),
('transfert', 1001, 5000, 50),
('transfert', 5001, 10000, 100),
('transfert', 10001, 25000, 200),
('transfert', 25001, 50000, 400),
('transfert', 50001, 100000, 800),
('transfert', 100001, 250000, 1500),
('transfert', 250001, 500000, 1500),
('transfert', 500001, 1000000, 2500),
('transfert', 1000001, 2000000, 3000);

-- Correction des préfixes (Nom opérateur et préfixe associés)
INSERT INTO config_prefixes (nom_operateur, prefixe) VALUES ('Orange', '032');
INSERT INTO config_prefixes (nom_operateur, prefixe) VALUES ('Airtel', '033');
INSERT INTO config_prefixes (nom_operateur, prefixe) VALUES ('Yas', '038');

-- Correction des clients de test (Nom et téléphone associés)
INSERT INTO clients (nom_clients, telephone, solde) VALUES ('Sanda', '0331234567', 50000.0);
INSERT INTO clients (nom_clients, telephone, solde) VALUES ('Tsiory', '0379876543', 500.0);

-- Opérateur de test (mot de passe: admin123 - à hasher en production)
INSERT INTO operateurs (username, password, nom) VALUES ('admin', 'admin123', 'Administrateur Principal');



-- Version 2

-- =====================================================================
-- PASSAGE À LA VERSION 2 (MIGRATION VIA ALTER TABLE)
-- =====================================================================

-- 1. Modifications de la table 'config_prefixes'
ALTER TABLE config_prefixes ADD COLUMN type_operateur TEXT NOT NULL DEFAULT 'externe';
ALTER TABLE config_prefixes ADD COLUMN commission_pourcentage REAL DEFAULT 0; 

-- 2. Modifications de la table 'transactions'
ALTER TABLE transactions ADD COLUMN frais_inclus INTEGER NOT NULL DEFAULT 0; 
ALTER TABLE transactions ADD COLUMN groupe_envoi TEXT DEFAULT NULL; 
ALTER TABLE transactions ADD COLUMN commission REAL NOT NULL DEFAULT 0; 

-- 3. Insertion des nouvelles données de la Version 2
INSERT INTO config_prefixes (nom_operateur, prefixe, type_operateur) VALUES ('M-Money', '035', 'interne');
