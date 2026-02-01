# Délégation ChangeLog

## 1.1.0 (2026-02-01)

- Ajout du support de la Révision/Valorisation des marchés via montant absolu sur ligne.

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
