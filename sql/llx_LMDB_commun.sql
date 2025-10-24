-- ============================================================================
-- Copyright (C) 2019 Pierre Ardoin
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


CREATE TABLE IF NOT EXISTS `llx_DC4_deleg_csst`( 
	`rowid`							int(11) AUTO_INCREMENT,
	`fk_object` 					int(11)  NOT NULL,
	`fk_element` 					VARCHAR(255)  NOT NULL,
	`dc4_object_declaration` 		int(11) NULL,
	`dc4_date_initiale` 			DATE NOT NULL,
	`dc4_hypothese`			 		int(11) NULL,   
	`avance`		   				int(11) NULL,
	`dc4_documents_fournis`         int(11) NULL,
	`paiement_direct` 				int(11) NULL,
	`libelle_poste_cctp` 			VARCHAR(255)  NOT NULL,
	`sps_travaux` 					int(11) NULL,
	`sps_date_remise` 				DATE NOT NULL,  
	`cissct` 						int(11) NULL,
	`DIUO` 							int(11) NULL,
	`responsabilite` 				int(11) NULL,

	PRIMARY KEY (`rowid`) 
)ENGINE=innodb DEFAULT CHARSET=utf8 ;