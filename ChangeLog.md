# Délégation ChangeLog

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
