<?php
/* Copyright (C) 2020-2024	Pierre Ardoin		<developpeur@lesmetiersdubatiment.fr>
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
* 	\file		admin/chantier_param.php
* 	\brief		This file is an example module setup page
* 				Put some comments here
*/
// Dolibarr environment
$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbank.class.php';

if (dol_is_file(dol_buildpath('/delegation/lib/delegation.lib.php', 0))) {
	dol_include_once('/delegation/lib/delegation.lib.php');
}

if(!$user->admin or empty($conf->delegation->enabled))
	accessforbidden();

global $mysoc;

$langs->load("admin");
$langs->load("delegation@delegation");

if (empty($conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID)) {
	setEventMessages($langs->trans('DelegationConfigMissingClearingAccount'), null, 'warnings');
}

// Ensure payment mode exists on admin access (upgrade safety).
if (empty($conf->global->DELEGATION_PAYMENT_MODE_ID)) {
	$paymentCode = 'DELPAY';
	$paymentLabel = $langs->trans('DelegationPaymentMode');
	$paymentId = 0;

	$sql = "SELECT id, active FROM ".MAIN_DB_PREFIX."c_paiement";
	// Force binary comparison to avoid collation mix errors.
	$sql.= " WHERE BINARY code = '".$db->escape($paymentCode)."'";
	$resql = $db->query($sql);
	if ($resql) {
		if ($db->num_rows($resql) > 0) {
			$obj = $db->fetch_object($resql);
			$paymentId = (int) $obj->id;
			if ((int) $obj->active !== 1) {
				$db->query("UPDATE ".MAIN_DB_PREFIX."c_paiement SET active = 1 WHERE id = ".(int) $paymentId);
			}
		}
	}

	if ($paymentId <= 0) {
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_paiement (code, libelle, type, active)";
		$sql.= " VALUES ('".$db->escape($paymentCode)."', '".$db->escape($paymentLabel)."', 2, 1)";
		if ($db->query($sql)) {
			$paymentId = (int) $db->last_insert_id(MAIN_DB_PREFIX."c_paiement");
		}
	}

	if ($paymentId > 0) {
		dolibarr_set_const($db, 'DELEGATION_PAYMENT_MODE_ID', $paymentId, 'int', 0, '', $conf->entity);
	}
}

// Handle admin actions for clearing account.
$action = GETPOST('action', 'aZ09');

if ($user->admin && $action == 'set_clearing_account') {
	if (! checkToken()) {
		accessforbidden();
	}
	$bankAccountId = (int) GETPOST('delegation_clearing_bank_account', 'int');
	dolibarr_set_const($db, 'DELEGATION_CLEARING_BANKACCOUNT_ID', $bankAccountId, 'int', 0, '', $conf->entity);
	setEventMessages($langs->trans('DelegationClearingAccountSaved'), null, 'mesgs');
}

if ($user->admin && $action == 'create_clearing_account') {
	if (! checkToken()) {
		accessforbidden();
	}
	$account = new Account($db);
	$account->ref = 'DELPASS';
	// Limit label length to database column size.
	$account->label = dol_trunc($langs->trans('DelegationClearingAccountLabel'), 30, 'right', 'UTF-8', 1);
	$account->currency_code = $conf->currency;
	$account->clos = 0;
	// Provide mandatory initial balance date and amount.
	$account->date_solde = dol_now();
	$account->solde = 0;
	// Set default country to satisfy mandatory field.
	$defaultCountryId = 0;
	if (! empty($mysoc->country_id)) {
		$defaultCountryId = (int) $mysoc->country_id;
	} elseif (! empty($conf->global->MAIN_INFO_SOCIETE_COUNTRY)) {
		$defaultCountryId = (int) $conf->global->MAIN_INFO_SOCIETE_COUNTRY;
	}
	$account->country_id = $defaultCountryId;

	if (empty($account->country_id)) {
		setEventMessages($langs->trans('DelegationClearingAccountMissingCountry'), null, 'errors');
	} else {
		$accountId = $account->create($user);
	}
	if ($accountId > 0) {
		dolibarr_set_const($db, 'DELEGATION_CLEARING_BANKACCOUNT_ID', $accountId, 'int', 0, '', $conf->entity);
		setEventMessages($langs->trans('DelegationClearingAccountCreated'), null, 'mesgs');
	} else {
		setEventMessages($account->error, $account->errors, 'errors');
	}
}

/*
*	View
*/

llxHeader('', $langs->trans("DelegationSetup"));

// Configuration header

$head = array();
if (function_exists('delegation_admin_prepare_head')) {
	$head = delegation_admin_prepare_head();
} elseif (function_exists('delegation_prepare_head')) {
	$head = delegation_prepare_head();
} else {
	$h = 0;
	$head[$h][0] = dol_buildpath('/delegation/admin/setup.php', 1);
	$head[$h][1] = $langs->trans("Setup");
	$head[$h][2] = 'setup';
	$h++;
	$head[$h][0] = dol_buildpath('/delegation/admin/other.php', 1).'?mode=desc';
	$head[$h][1] = $langs->trans("Description");
	$head[$h][2] = 'Description';
	$h++;
	$head[$h][0] = dol_buildpath('/delegation/admin/other.php', 1).'?mode=feature';
	$head[$h][1] = $langs->trans("Features");
	$head[$h][2] = 'Features';
	$h++;
	$head[$h][0] = dol_buildpath('/delegation/admin/other.php', 1).'?mode=changelog';
	$head[$h][1] = $langs->trans("Changelog");
	$head[$h][2] = 'Changelog';
}
dol_fiche_head($head, 'setup', $langs->trans("DelegationSetup"), 0, "delegation@delegation");

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("DelegationSetup"), $linkback);
print '<br>';
print '<table class="noborder" width="100%">'."\n";
	print '<thead>';
		print '<tr class="liste_titre">';
			print '<th>'.$langs->trans("Name").'</th>';
			print '<th  align="center" width="300">'.$langs->trans("Value").'</th>';
		print '</tr>';
	print '</thead>';
	print '<tbody>';
showParameters();

function showParameters()
{
	global $conf, $langs, $bc, $db;

	$formbank = new FormBank($db);
	$form = new Form($db);
	$token = newToken();
	$var = false;

	$var = ! $var;
	print '<tr '.$bc[$var].'>';
		print '<td align="left" class="">'.$langs->trans("DelegationClearingBankAccount").'</td>';
		print '<td align="center" width="300">';
			print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
				print '<input type="hidden" name="token" value="'.$token.'">';
				print '<input type="hidden" name="action" value="set_clearing_account">';
				// Use FormBank selector when available, fallback to generic list otherwise.
				if (method_exists($formbank, 'select_comptes')) {
					print $formbank->select_comptes($conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID, 'delegation_clearing_bank_account', 0, '', 1);
				} elseif (method_exists($formbank, 'selectAccount')) {
					print $formbank->selectAccount($conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID, 'delegation_clearing_bank_account', 0, '', 1);
				} else {
					$options = array();
					$sql = "SELECT rowid, label FROM ".MAIN_DB_PREFIX."bank_account";
					$sql.= " WHERE entity = ".(int) $conf->entity;
					$sql.= " ORDER BY label";
					$resql = $db->query($sql);
					if ($resql) {
						while ($obj = $db->fetch_object($resql)) {
							$options[$obj->rowid] = $obj->label;
						}
					}
					print $form->selectarray('delegation_clearing_bank_account', $options, $conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID, 1);
				}
				print ' <input type="submit" class="button" value="'.$langs->trans("Save").'">';
			print '</form>';
		print '</td>';
	print '</tr>';

	$var = ! $var;
	print '<tr '.$bc[$var].'>';
		print '<td align="left" class="">'.$langs->trans("DelegationCreateClearingAccount").'</td>';
		print '<td align="center" width="300">';
			print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
				print '<input type="hidden" name="token" value="'.$token.'">';
				print '<input type="hidden" name="action" value="create_clearing_account">';
				print '<input type="submit" class="button" value="'.$langs->trans("DelegationCreateClearingAccount").'">';
			print '</form>';
		print '</td>';
	print '</tr>';

	$var = ! $var;
	print '<tr class="liste_titre">';
		print '<td colspan="2">'.$langs->trans("DelegationTabsSection").'</td>';
	print '</tr>';

	$tabs = array(
		'DELEGATION_ENABLE_TAB_DELEGATION' => array(
			'label' => 'DelegationTabDelegationLabel',
			'help' => 'DelegationTabDelegationHelp',
		),
		'DELEGATION_ENABLE_TAB_DETAILS' => array(
			'label' => 'DelegationTabDetailsLabel',
			'help' => 'DelegationTabDetailsHelp',
		),
		'DELEGATION_ENABLE_TAB_DC4_SUPPLIER' => array(
			'label' => 'DelegationTabDc4SupplierLabel',
			'help' => 'DelegationTabDc4SupplierHelp',
		),
		'DELEGATION_ENABLE_TAB_DC4_CUSTOMER' => array(
			'label' => 'DelegationTabDc4CustomerLabel',
			'help' => 'DelegationTabDc4CustomerHelp',
		),
	);

	foreach ($tabs as $constName => $tabInfo) {
		$var = ! $var;
		print '<tr '.$bc[$var].'>';
			print '<td align="left" class="">';
				print $form->textwithtooltip($langs->trans($tabInfo['label']), $langs->trans($tabInfo['help']), 2, 1, img_info());
			print '</td>';
			print '<td align="center" width="300">';
				if (function_exists('ajax_constantonoff')) {
					print ajax_constantonoff($constName);
				} else {
					print $form->selectyesno($constName, (int) getDolGlobalInt($constName), 1);
				}
			print '</td>';
		print '</tr>';
	}
}
print '</tbody>';
print '</table>';

llxFooter();
$db->close();
