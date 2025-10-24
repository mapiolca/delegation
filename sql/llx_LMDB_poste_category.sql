-- ============================================================================
-- Copyright (C) 2023 Pierre Ardoin
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


-- Data file with LMDB_poste_category (Post Category) codes and category

CREATE TABLE IF NOT EXISTS `llx_LMDB_poste_category`( 
  	`rowid`				int(11) AUTO_INCREMENT,
  	`code` 			VARCHAR(255) NOT NULL UNIQUE, 
  	`category` 			VARCHAR(255) NOT NULL,   
	`country_code`      VARCHAR(255) NOT NULL,
	`position`      TINYINT(4) NOT NULL,
	`active`           	TINYINT(4)    NOT NULL DEFAULT 1,

	PRIMARY KEY (`rowid`) 
)ENGINE=innodb DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;

-- France
INSERT IGNORE INTO llx_LMDB_poste_category (code, category, country_code, position, active) VALUES
('SUR', 'Traitement de surface', 'FR', 3, 1),
('TRA', 'Transport', 'FR', 5, 1),
('FAB', 'Fabrication', 'FR', 2, 1),
('QUI', 'Visserie et Quincaillerie', 'FR', 4, 1),
('POS', 'Assemblage et Pose', 'FR', 6, 1),
('MAT', 'Matière', 'FR', 1, 1);
