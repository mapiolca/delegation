-- ============================================================================
-- Copyright (C) 2020 Pierre Ardoin
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


CREATE TABLE IF NOT EXISTS `llx_LMDB_rating` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `entity` int(11) DEFAULT NULL,
  `rate` float NOT NULL,
  `comment` varchar(264) NOT NULL,
  `datec` datetime NOT NULL,
  `fk_element` int(11) NOT NULL,
  `elementtype` varchar(64) NOT NULL,
  `elementrated` varchar(64) NOT NULL,

  PRIMARY KEY (`rowid`)
)ENGINE=innodb DEFAULT CHARSET=utf8 ;