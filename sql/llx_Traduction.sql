-- ============================================================================
-- Copyright (C) 2018 Pierre Ardoin
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ============================================================================

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_commande_account', 'Compte Bancaire');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_commande_account_help', 'Utile pour les DC4, etc.');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_compte_prorata', 'Compte prorata');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_date_devis_fourn', 'Date du devis Fournisseur');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_link', 'Lien vers les fichiers');
INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'linkfiles', 'Lien vers les Fichiers');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_marque', 'Marque | Fabricant');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_n_devis_fourn', 'N° de devis Fournisseur');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_poste_info', 'Libellé');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_project_amount', 'Montant du Projet (HT)');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'lmdb_retenue_garantie', 'Retenue de garantie');

INSERT IGNORE INTO `llx_overwrite_trans` (`rowid`, `entity`, `lang`, `transkey`, `transvalue`) VALUES (NULL, '1', 'fr_FR', 'YouReceiveMailBecauseOfNotification', "Vous recevez ce message car votre email a été abonné à certaines notifications automatiques pour vous informer d'événements particuliers issus du logiciel ERP fournit par <a href='https://lesmetiersdubatiment.fr'>Les Métiers du Bâtiment</a>. </br> Les données personnelles enregistrées dans le cadre de nos relations professionnelles font l'objet de traitements internes à notre société, conformes à la Loi Informatique & Liberté du 6 janvier 1978 modifiée en 2018. Vous pouvez exercer vos droits d'accès, de rectification, d'effacement ou de portabilité des informations vous concernant ; et pour des motifs légitimes, vos droits de limitation ou d'opposition aux traitements. Pour cela adressez-vous à votre interlocuteur ou à notre délégué à la protection des données (<a href='mailto:dpo@lesmetiersdubatiment.fr'>dpo@lesmetiersdubatiment.fr</a>).");
