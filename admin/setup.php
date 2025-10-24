<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2020 Ardoin Pierre <mapiolca@me.com>
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

$langs->load("admin");
$langs->load("delegation@delegation");
$langs->load("bank@delegation");
$langs->load("budget@delegation");

$action = GETPOST('action','alpha');
if($action == 'save'){

  dolibarr_set_const($db, "LMDB_BUDGET_ORDER_STATUS", GETPOST('order_status'), 'int', 0, "Statut des commandes fournisseurs à prendre en charge", $conf->entity);

	header("Location: ".$_SERVER["PHP_SELF"]);
  exit;
}

/*
 *	View
 */

llxHeader('',$langs->trans("LMDBSetup"));

// Configuration header

$head = lmdb_prepare_head();
dol_fiche_head($head,'SetupG', $langs->trans("Les Métiers du Bâtiment"), 0, "");

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ModuleSetup"),$linkback);
print '<br>';
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<table class="noborder" width="100%">'."\n";
  print '<thead>';
    print '<tr class="liste_titre">';
      print '<th>'.$langs->trans("Name").'</th>';
      print '<th  align="center" width="300">'.$langs->trans("Value").'</th>';
    print '</tr>';
  print '</thead>';
showParameters();
  print '<tbody>';

function showParameters() {
    global $db,$conf,$langs,$bc;
    
    $html=new Form($db);

    $var=!$var;
    print '<tr '.$bc[$var].'>';
      print '<td align="left" class="">'.$langs->trans("LMDB_USE_IDPROF3_DICTIONARY").'</td>';
        print '<td align="center" width="300">';
          print ajax_constantonoff('LMDB_USE_IDPROF3_DICTIONARY');
      print '</td>';
    print '</tr>';
    $var=!$var;
    print '<tr '.$bc[$var].'>';
      print '<td align="left" class="">'.$langs->trans("LMDB_DELEG_ACTIVATED").'</td>';
      print '<td align="center" width="300">';
        print ajax_constantonoff('LMDB_DELEG_ACTIVATED');
      print '</td>';
    print '</tr>';
    $var=!$var;
    print '<tr '.$bc[$var].'>';
      print '<td align="left" class="">'.$langs->trans("LMDB_DC1_ACTIVATED").'</td>';
      print '<td align="center" width="300">';
        print ajax_constantonoff('LMDB_DC1_ACTIVATED');
      print '</td>';
    print '</tr>';
    $var=!$var;
    print '<tr '.$bc[$var].'>';
      print '<td align="left" class="">'.$langs->trans("LMDB_DC2_ACTIVATED").'</td>';
      print '<td align="center" width="300">';
        print ajax_constantonoff('LMDB_DC2_ACTIVATED');
      print '</td>';
    print '</tr>';
    $var=!$var;
    print '<tr '.$bc[$var].'>';
      print '<td align="left" class="">'.$langs->trans("LMDB_DC4_ACTIVATED").'</td>';
      print '<td align="center" width="300">';
        print ajax_constantonoff('LMDB_DC4_ACTIVATED');
      print '</td>';
    print '</tr>';
    $var=!$var;
    print '<tr '.$bc[$var].'>';
      print '<td align="left" class="">'.$langs->trans("order_status_9").'</td>';
      print '<td align="center" width="300">';
        print ajax_constantonoff('LMDB_BUDGET_ORDER_STATUS_REFUSED');
      print '</td>';
    print '</tr>';

  }
    print '<tr class="impair">';
      print '<td align="left">'.$langs->trans("order_status").'</td>';
      print '<td>';
        print '<input type="hidden" name="action" value="save">';
        print '<select name="order_status">';
          print '<option value="0" ';
          if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '0') {
            print 'selected';
          } 
          print '>'.$langs->trans("order_status_0").'</option>';
          print '<option value="1" ';
          if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '1') {
            print 'selected';
          } 
          print '>'.$langs->trans("order_status_1").'</option>';
          print '<option value="2" ';
          if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '2') {
            print 'selected';
          } 
          print '>'.$langs->trans("order_status_2").'</option>';
          print '<option value="3" ';
          if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '3') {
            print 'selected';
          } 
          print '>'.$langs->trans("order_status_3").'</option>';
          print '<option value="4" ';
          if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '4') {
            print 'selected';
          } 
          print '>'.$langs->trans("order_status_4").'</option>';
          print '<option value="5" ';
          if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '5') {
            print 'selected';
          } 
          print '>'.$langs->trans("order_status_5").'</option>';
        print '</select>';
      print '</td>';
    print '</tr>';
  print '</tbody>';
print '</table>';

print '<center><input type="submit" class="button" value="'.$langs->trans("Modify").'"></center>';

print '</form>';

?>