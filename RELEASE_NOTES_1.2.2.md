# Notes de version 1.2.2

Date : 2026-04-18

## FIX

- PDF `crabe_btp_inpose` : correction du calcul de la ligne **"Total à payer à échéance"** pour ne plus déduire les délégations sur **HT** et **TVA**.
- PDF `crabe_btp_inpose` : maintien de la déduction des délégations sur la ligne dédiée **"Total des Délégations à déduire"**.
- PDF `crabe_btp_inpose` : alignement du calcul de la ligne **"Montant à payer"** avec des montants nets cohérents en **HT / TVA / TTC** après délégation.
- PDF `crabe_btp_inpose` : correction du style pour que seul l'habillage visuel de la ligne **"Situation actuelle"** soit harmonisé avec la ligne **"Total à payer à échéance"** (sans impacter les lignes suivantes).

## UIUX

- Harmonisation visuelle ciblée de la ligne **"Situation actuelle"** dans le tableau des montants du PDF.

## Traductions

- `fr_FR` : libellé **CompteProrataTTCDeduit** simplifié en **"Compte Prorata"**.

## Compatibilité

- Correctifs compatibles avec le fonctionnement actuel du module de délégation et du modèle PDF LMDB.
- Aucun changement de schéma SQL.
