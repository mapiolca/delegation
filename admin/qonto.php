<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2016 INOVEA CONSEIL <info@inovea-conseil.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/chantier_param.php
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// Dolibarr environment
$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/delegation/class/lmdb.class.php';

if(!$user->admin or empty($conf->delegation->enabled))
  accessforbidden();

$langs->load("bank@delegation");
$langs->load("admin");

$action = GETPOST('action','alpha');
if($action == 'save'){
  $per_page = intval(GETPOST('per_page', 'int'));
  if($per_page and $per_page <= 50){
    dolibarr_set_const($db, "LMDB_QONTO_SLUG", GETPOST('slug', 'alpha'), 'chaine', 0, "Identifiant QONTO", $conf->entity);
    dolibarr_set_const($db, "LMDB_QONTO_AUTHORIZATION", GETPOST('key', 'alpha'), 'chaine', 0, "Clé Secrète QONTO", $conf->entity);
    dolibarr_set_const($db, "LMDB_QONTO_PER_PAGE", $per_page, 'chaine', 0, "Nombre de ligne à afficher par page", $conf->entity);
  }else{
    setEventMessage($langs->trans('messagemax'), 'errors');
  }
	header("Location: ".$_SERVER["PHP_SELF"]);
  exit;
}

/*
 *	View
 */

llxHeader('',$langs->trans("LMDBQontoSetup"));

// Configuration header

$head = lmdb_prepare_head();
dol_fiche_head($head,'Qonto', $langs->trans("Les Métiers du Bâtiment"), 0, "");

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup"),$linkback);
print '<br>';

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="action" value="save">';

print '<table class="noborder" width="100%">'."\n";

print '<tr class="liste_titre">';
print '  <td>'.$langs->trans("Name").'</td>';
print '  <td >'.$langs->trans("Value").'</td>';
print '</tr>';

print '<tr class="impair">';
print '  <td align="left" class="fieldrequired">'.$langs->trans("IdAPI").'</td>';
print '  <td>';
print '    <input type="account" name="slug" value="'.$conf->global->LMDB_QONTO_SLUG.'" required>';
print '  </td>';
print '</tr>';

print '<tr class="pair">';
print '  <td align="left" class="fieldrequired">'.$langs->trans("SecretKey").'</td>';
print '  <td>';
print '    <input type="account" name="key" value="'.$conf->global->LMDB_QONTO_AUTHORIZATION.'" required>';
print '  </td>';
print '</tr>';

print '<tr class="impair">';
print '  <td align="left" class="fieldrequired">'.$langs->trans("LineMax").'';
print '  <td>';
print '    <input type="account" name="per_page" value="'.($conf->global->LMDB_QONTO_PER_PAGE ? $conf->global->LMDB_QONTO_PER_PAGE : '25').'" required>';
print '  </td>';
print '</tr>';


print '</table><br>';

print '<center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

dol_fiche_end();

llxFooter();

?>