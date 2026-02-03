<?php
/* Copyright (C) 2024	Pierre Ardoin		<developpeur@lesmetiersdubatiment.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Prepare array with list of tabs
 *
 * @return  array				Array of tabs to show
 */
function lmdb_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = "../../delegation/admin/setup.php";
	$head[$h][1] = $langs->trans("SetupG");
	$head[$h][2] = 'SetupG';
	$h++;
	
	$head[$h][0] = "../../delegation/admin/about.php";
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'About';
	$h++;

	return $head;
}
