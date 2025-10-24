<?php


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

	//$head[$h][0] = "../../delegation/admin/qonto.php";
	//$head[$h][1] = $langs->trans("qonto");
	//$head[$h][2] = 'Qonto';
	//$h++;

	$head[$h][0] = "../../delegation/admin/other.php?id=440300&amp;mode=desc";
	$head[$h][1] = $langs->trans("Description");
	$head[$h][2] = 'Description';
	$h++;

	$head[$h][0] = "../../delegation/admin/other.php?id=440300&amp;mode=feature";
	$head[$h][1] = $langs->trans("Features");
	$head[$h][2] = 'Features';
	$h++;

	$head[$h][0] = "../../delegation/admin/other.php?id=440300&amp;mode=changelog";
	$head[$h][1] = $langs->trans("Changelog");
	$head[$h][2] = 'Changelog';
	$h++;


	return $head;
}


