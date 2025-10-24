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


CREATE TABLE IF NOT EXISTS `llx_projet_detail`( 
  	`rowid`				int(11) AUTO_INCREMENT,
  	`fk_object` 		int(11)  NOT NULL,
  	`fk_element` 		VARCHAR(255)  NOT NULL,
  	`type_mou` 			int(11) NULL,
  	`ref_chantier` 		VARCHAR(255)  NOT NULL,   
  	`adresse_chantier`	TEXT  NOT NULL,  
	`nature_travaux`    TEXT NOT NULL,
	`fk_moe` 			int(11) NULL,
	`n_lot` 			VARCHAR(255)  NOT NULL,
	`libelle_lot`		VARCHAR(255) NOT NULL,
	`marche_defense`	int(11)  NOT NULL,
	`rg_sstt`			VARCHAR(255) NOT NULL,

	PRIMARY KEY (`rowid`) 
)ENGINE=innodb DEFAULT CHARSET=utf8 ;