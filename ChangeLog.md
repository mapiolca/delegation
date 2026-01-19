# Délégation ChangeLog

## 1.0.1 (2026-01-19)

- Ajout du support de l'autoliquidation de TVA sur les devis, commandes et factures client liés à un contrat de sous-traitance.

## 1.0.0 (2026-01-18)

- Redémarrage du versioning (restart versioning).
- Ajout du mode de règlement dédié « Délégation de paiement ».
- Ajout d’un compte de passage configurable (création automatique + sélection).
- Refonte de l’onglet « Délégation » avec sélection de factures fournisseurs filtrées (projet + mode de règlement).
- Suppression des dépendances forcées à d’autres modules lors de l’activation.
- Refonte de la page d’administration (setup) et ajout de helpers d’affichage.
- Suppression des onglets Description/Fonctionnalités/Changelog et de la page admin associée.
- Durcissement des pages admin pour éviter les warnings PHP 8.x.

## 4.4.1 (2025-03-10)

- Nettoyage du module : périmètre limité à la délégation de paiement, aux contrats de sous-traitance et au formulaire DC4.
- Suppression des éléments hors périmètre (menus, droits, tables, pages, traductions).
- Compatibilité Dolibarr v21+.
