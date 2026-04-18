# Délégation ChangeLog

## 1.3.0 (2026-04-18)
- Ajoute un type de contact externe `DELEGDC4` pour les commandes fournisseurs et l’exploite comme représentant DC4 / délégation de paiement.
- Remplace l’usage des extrafields `lmdb_representant` / `lmdb_qualite_representant` par le contact externe `DELEGDC4` dans les modèles fournisseurs concernés.
- Fiabilise la récupération des informations du représentant (nom, poste, affichage) via une librairie dédiée.
- Corrige un crash de génération du PDF `dc42017` quand le tiers fournisseur n’est pas chargé au moment de la récupération bancaire.
- Corrige l’élément dictionnaire du type de contact pour cibler `order_supplier`.

## 1.2.2 (2026-04-18)
- Corrige les calculs du tableau des montants du PDF BTP LMDB : la ligne « Total à payer à échéance » ne déduit plus les délégations en HT/TVA.
- Conserve la déduction des délégations sur la ligne dédiée « Total des Délégations à déduire » et fiabilise l'affichage du « Montant à payer » en HT/TVA/TTC.
- Harmonise le style de la ligne « Situation actuelle » avec la ligne « Total à payer à échéance », sans impacter les lignes suivantes.
- Simplifie le libellé FR « CompteProrataTTCDeduit » en « Compte Prorata ».
- Durcit l’onglet Délégation des factures client : suppression de l’édition inline, actions limitées à l’ajout/suppression et protection CSRF sur la suppression.
- Filtre les factures fournisseurs éligibles : uniquement impayées et non déjà liées à une délégation de paiement.
- Met à jour le tableau de délégation avec les colonnes « Montant HT » et « TVA », retire « Déjà réglé », « Reste à payer » et « Montant », et masque le bouton « Ajouter » s’il n’y a aucune facture éligible.

## 1.2.1 (2026-02-12)
- Corrige un probème d'affichage des montants dans les lignes des sous-totaux du modèle BTP LMDB

## 1.2.0 (2026-02-02)
- Réduit la constante de largeur des colonnes fixes en résumé BTP de 25 à 23 pour agrandir la colonne description dans _tableauBtpOrdersSummary du fichier core/modules/facture/doc/pdf_crabe_btp_inpose.modules.php.
- Calcule désormais une hauteur par ligne ($row_heights) en utilisant getStringHeight quand disponible afin d'adapter la hauteur des lignes au contenu (description et date) et additionne ces hauteurs pour obtenir la hauteur totale dans _tableauBtpOrdersSummary.
- Refonte de la page des réglages

## 1.1.0 (2026-02-01)

- Ajout du support de la Révision/Valorisation des marchés via montant absolu sur ligne.
- Refonte du modèle LMDB BTP

## 1.0.0 (2026-01-19)

- Redémarrage du versioning (restart versioning).
- Ajout du support de l'autoliquidation de TVA sur les devis, commandes et factures client liés à un contrat de sous-traitance.
- Ajout du mode de règlement dédié « Délégation de paiement ».
- Ajout d’un compte de passage configurable (création automatique + sélection).
- Ajout d'une page récapitulative des factures fournisseurs en délégation dans les modèles PDF Inpose et Crabe BTP.
- Refonte de l’onglet « Délégation » avec sélection de factures fournisseurs filtrées (projet + mode de règlement).
- Suppression des dépendances forcées à d’autres modules lors de l’activation.
- Refonte de la page d’administration (setup) et ajout de helpers d’affichage.
- Suppression des onglets Description/Fonctionnalités/Changelog et de la page admin associée.
- Durcissement des pages admin pour éviter les warnings PHP 8.x.
- Nettoyage du module : périmètre limité à la délégation de paiement, aux contrats de sous-traitance et au formulaire DC4.
- Suppression des éléments hors périmètre (menus, droits, tables, pages, traductions).
- Compatibilité Dolibarr v21+.
