# Module Délégation

## FR

Ce module gère :

- la délégation de paiement ;
- les contrats de sous-traitance ;
- l'autoliquidation de TVA sur les documents clients liés aux contrats de sous-traitance ;
- le formulaire DC4 (saisie, génération et données associées).
- un récapitulatif des factures fournisseurs en délégation dans certains modèles PDF.

Compatibilité : Dolibarr v21+.

Le module ajoute un mode de règlement dédié « Délégation de paiement » et nécessite la configuration d’un compte de passage pour enregistrer les mouvements liés aux délégations.

### Mise à niveau vers la version 1.4.0

La version 1.4.0 supprime la fonction d’envoi automatique des factures. Pour éviter qu’une ancienne tâche planifiée tente de charger le fichier supprimé pendant le déploiement, désactivez d’abord le module, remplacez ses fichiers, puis réactivez-le. La réactivation supprime les anciennes tâches cron et masque les extrafields concernés sans supprimer leurs valeurs historiques.

## EN

This module handles:

- payment delegation;
- subcontracting contracts;
- VAT reverse charge on customer documents linked to subcontracting contracts;
- the DC4 form (entry, generation, and related data).

Compatibility: Dolibarr v21+.

The module adds a dedicated payment mode named “Payment delegation” and requires configuring a clearing account to track delegation-related movements.

### Upgrading to version 1.4.0

Version 1.4.0 removes automatic invoice sending. To prevent a legacy scheduled job from loading the removed file during deployment, disable the module first, replace its files, and then enable it again. Re-enabling the module removes the legacy cron jobs and hides the related extrafields without deleting their historical values.
