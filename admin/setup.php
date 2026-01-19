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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbank.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/delegation/class/lmdb.class.php';

$delegationLibPath = dol_buildpath('/delegation/lib/delegation.lib.php', 0);
if ((function_exists('dol_is_file') && dol_is_file($delegationLibPath)) || (! function_exists('dol_is_file') && is_file($delegationLibPath))) {
	require_once $delegationLibPath;
}
$delegationAdminLibPath = dol_buildpath('/delegation/lib/admin.lib.php', 0);
if ((function_exists('dol_is_file') && dol_is_file($delegationAdminLibPath)) || (! function_exists('dol_is_file') && is_file($delegationAdminLibPath))) {
	require_once $delegationAdminLibPath;
}

if(!$user->admin or empty($conf->delegation->enabled))
	accessforbidden();

global $mysoc;

$langs->loadLangs(array("admin", "delegation@delegation"));

if (empty($conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID)) {
	setEventMessages($langs->trans('DelegationConfigMissingClearingAccount'), null, 'warnings');
}

// EN: Enable linked object line imports by default.
// FR: Activer par défaut l'import des lignes d'objets liés.
if (! isset($conf->global->MAIN_ENABLE_IMPORT_LINKED_OBJECT_LINES)) {
	dolibarr_set_const($db, 'MAIN_ENABLE_IMPORT_LINKED_OBJECT_LINES', 1, 'int', 0, '', $conf->entity);
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

// Handle admin actions.
$action = GETPOST('action', 'aZ09');

if ($user->admin && preg_match('/^set_([A-Z0-9_]+)$/', $action, $reg)) {
	$constname = $reg[1];
	dolibarr_set_const($db, $constname, 1, 'chaine', 0, '', $conf->entity);
	setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

if ($user->admin && preg_match('/^del_([A-Z0-9_]+)$/', $action, $reg)) {
	$constname = $reg[1];
	dolibarr_del_const($db, $constname, $conf->entity);
	setEventMessages($langs->trans("SetupDeleted"), null, 'mesgs');
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

if ($user->admin && in_array($action, array('set_clearing_account', 'create_clearing_account', 'set_vat_reverse_charge_scope', 'set_vat_reverse_charge_legal_text'), true)) {
	$tokenIsValid = true;
	if (function_exists('checkToken')) {
		$tokenIsValid = checkToken();
	} elseif (! empty($_SESSION['newtoken'])) {
		$tokenIsValid = (GETPOST('token', 'alphanohtml') === $_SESSION['newtoken']);
	}
	if (! $tokenIsValid) {
		accessforbidden();
	}
}

// Handle admin actions for clearing account.
if ($user->admin && $action == 'set_clearing_account') {
	$bankAccountId = (int) GETPOST('delegation_clearing_bank_account', 'int');
	dolibarr_set_const($db, 'DELEGATION_CLEARING_BANKACCOUNT_ID', $bankAccountId, 'int', 0, '', $conf->entity);
	setEventMessages($langs->trans('DelegationClearingAccountSaved'), null, 'mesgs');
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

if ($user->admin && $action == 'create_clearing_account') {
	$account = new Account($db);
	$accountId = 0;
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
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

// EN: Handle admin actions for VAT reverse charge configuration.
// FR: Gérer les actions d'administration pour l'autoliquidation de TVA.
if ($user->admin && $action == 'set_vat_reverse_charge_scope') {
	$scope = GETPOST('delegation_vat_reverse_charge_scope', 'alpha');
	if (! in_array($scope, array('services_only', 'all_lines'), true)) {
		$scope = 'services_only';
	}
	dolibarr_set_const($db, 'DELEGATION_VAT_REVERSE_CHARGE_SCOPE', $scope, 'chaine', 0, '', $conf->entity);
	setEventMessages($langs->trans('DelegationVatReverseChargeScopeSaved'), null, 'mesgs');
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

if ($user->admin && $action == 'set_vat_reverse_charge_legal_text') {
	$text = GETPOST('delegation_vat_reverse_charge_legal_text', 'restricthtml');
	dolibarr_set_const($db, 'DELEGATION_VAT_REVERSE_CHARGE_LEGAL_TEXT', $text, 'chaine', 0, '', $conf->entity);
	setEventMessages($langs->trans('DelegationVatReverseChargeLegalTextSaved'), null, 'mesgs');
	header("Location: ".$_SERVER["PHP_SELF"]);
	exit;
}

/*
*	View
*/

llxHeader('', $langs->trans("DelegationSetup"), '', '', 0, 0, array(), array(), '', $langs->trans("DelegationSetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("DelegationSetup"), $linkback);
// Configuration header

$head = lmdb_prepare_head();
dol_fiche_head($head, 'SetupG', $langs->trans("DelegationSetup"), 0, "delegation@delegation");

print '<br>';
print '<table class="noborder" width="100%">'."\n";
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td align="center" width="300">'.$langs->trans("Value").'</td>';
print '</tr>';

$form = new Form($db);
$formbank = new FormBank($db);
$var = false;

delegation_setup_print_title($langs->trans("DelegationClearingAccountSection"));

// EN: Render clearing account selector.
// FR: Afficher le sélecteur du compte de passage.
if (method_exists($formbank, 'select_comptes')) {
	$accountSelect = $formbank->select_comptes($conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID, 'delegation_clearing_bank_account', 0, '', 1);
} elseif (method_exists($formbank, 'selectAccount')) {
	$accountSelect = $formbank->selectAccount($conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID, 'delegation_clearing_bank_account', 0, '', 1);
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
	$accountSelect = $form->selectarray('delegation_clearing_bank_account', $options, $conf->global->DELEGATION_CLEARING_BANKACCOUNT_ID, 1);
}
delegation_setup_print_input_form_part($langs->trans("DelegationClearingBankAccount"), $accountSelect, 'set_clearing_account');
delegation_setup_print_input_form_part($langs->trans("DelegationCreateClearingAccount"), '', 'create_clearing_account', $langs->trans("DelegationCreateClearingAccount"));

delegation_setup_print_title($langs->trans("DelegationTabsSection"));

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
	delegation_setup_print_on_off($langs->trans($tabInfo['label']), $constName, $langs->trans($tabInfo['help']));
}

delegation_setup_print_title($langs->trans("DelegationImportSection"));
delegation_setup_print_on_off(
	$langs->trans("DelegationEnableImportLinkedObjectLines"),
	'MAIN_ENABLE_IMPORT_LINKED_OBJECT_LINES',
	$langs->trans("DelegationEnableImportLinkedObjectLinesHelp")
);

delegation_setup_print_title($langs->trans("DelegationVatReverseChargeSection"));
delegation_setup_print_on_off($langs->trans("DelegationEnableVatReverseCharge"), 'DELEGATION_ENABLE_VAT_REVERSE_CHARGE');
delegation_setup_print_on_off($langs->trans("DelegationVatReverseChargeForceVat0"), 'DELEGATION_VAT_REVERSE_CHARGE_FORCE_VAT0');

$scopeOptions = array(
	'services_only' => $langs->trans('DelegationVatReverseChargeScopeServicesOnly'),
	'all_lines' => $langs->trans('DelegationVatReverseChargeScopeAllLines'),
);
$currentScope = getDolGlobalString('DELEGATION_VAT_REVERSE_CHARGE_SCOPE', 'services_only');
$scopeSelect = $form->selectarray('delegation_vat_reverse_charge_scope', $scopeOptions, $currentScope, 0);
delegation_setup_print_input_form_part($langs->trans("DelegationVatReverseChargeScope"), $scopeSelect, 'set_vat_reverse_charge_scope');

$defaultText = $langs->trans('DelegationVatReverseChargeLegalTextDefault');
$currentText = getDolGlobalString('DELEGATION_VAT_REVERSE_CHARGE_LEGAL_TEXT', $defaultText);
$legalTextInput = '<textarea name="delegation_vat_reverse_charge_legal_text" class="flat" rows="3" cols="40">'.dol_escape_htmltag($currentText).'</textarea>';
delegation_setup_print_input_form_part($langs->trans("DelegationVatReverseChargeLegalText"), $legalTextInput, 'set_vat_reverse_charge_legal_text');

print '</table>';

llxFooter();
$db->close();
