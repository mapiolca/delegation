<?php
/* Copyright (C) 2018-2020      Pierre Ardoin        <mapiolca@me.com>
 *                                             
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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

/**	    \file       htdocs/delegation/tabs/compte.php
 *		\ingroup    delegation
 *		\brief      Compte module tabs view
 */


require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";
require_once DOL_DOCUMENT_ROOT . '/core/lib/bank.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formbank.class.php';
require_once DOL_DOCUMENT_ROOT . '/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
if (! empty($conf->categorie->enabled)) require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
if (! empty($conf->accounting->enabled)) require_once DOL_DOCUMENT_ROOT . '/core/class/html.formaccounting.class.php';
if (! empty($conf->accounting->enabled)) require_once DOL_DOCUMENT_ROOT . '/accountancy/class/accountingaccount.class.php';
if (! empty($conf->accounting->enabled)) require_once DOL_DOCUMENT_ROOT . '/accountancy/class/accountingjournal.class.php';

// Load translation files required by the page
$langs->loadLangs(array("banks","bills","categories","companies","compta", "bank@delegation"));

$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel', 'alpha');

// Security check
if (isset($_GET["id"]) || isset($_GET["ref"]))
{
	$id = isset($_GET["id"])?GETPOST("id"):(isset($_GET["ref"])?GETPOST("ref"):'');
}
$fieldid = isset($_GET["ref"])?'ref':'rowid';
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user, 'banque', $id, 'bank_account&bank_account', '', '', $fieldid);

$object = new Account($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

// Security check
$fieldvalue = (!empty($id) ? $id : (!empty($ref) ? $ref : ''));
$fieldtype = (!empty($ref) ? 'ref' : 'rowid');
if ($fielvalue)
{
	if ($user->socid) $socid = $user->socid;
	$result = restrictedArea($user, 'banque', $fieldvalue, 'bank_account&bank_account', '', '', $fieldtype);
}
else
{
	if ($user->socid) $socid = $user->socid;
	$result = restrictedArea($user, 'banque');
}

/*
 * View
 */

$form = new Form($db);
$title = $langs->trans("FinancialAccount") . " - " . $langs->trans("Card");
$helpurl = "";
llxHeader("", $title, $helpurl);

$action = GETPOST("action", 'alpha');

$page = GETPOST("page", 'int');
if (empty($page) || $page == -1) { $page = 1; }     // If $page is not defined, or '' or -1


$accountid = GETPOST("id", "int");

/* ************************************************************************** */
/*                                                                            */
/* Visu et edition                                                            */
/*                                                                            */
/* ************************************************************************** */

	if (($_GET["id"] || $_GET["ref"]) && $action != 'edit')
	{
		$object = new Account($db);
		if ($_GET["id"])
		{
			$object->fetch($_GET["id"]);
		}
		if ($_GET["ref"])
		{
			$object->fetch(0, $_GET["ref"]);
			$_GET["id"]=$object->id;
		}

		$bank_name = strtoupper($object->bank);

		if (strcmp($bank_name, "QONTO") == 0) {
			include_once DOL_DOCUMENT_ROOT . '/custom/delegation/class/qonto.class.php';

		}else{
			$alert ='
			<div class="fiche">
			<div class="error">Désolé<br>Banque non prise en charge pour le moment...</div>
			</div>';
		}

		// Show tabs
		$head=bank_prepare_head($object);
		dol_fiche_head($head, 'compte', $langs->trans("FinancialAccount"), -1, 'account');

		$formconfirm = '';

		// Print form confirm
		print $formconfirm;

		$linkback = '<a href="'.DOL_URL_ROOT.'/compta/bank/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

		$morehtmlref='';
		dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);


		print '<div class="fichecenter">';
		print '<div class="fichehalfleft">';
		print '<div class="underbanner clearboth"></div>';

		print '<table class="border tableforfield" width="100%">';

		// Type
		print '<tr><td class="titlefield">'.$langs->trans("AccountType").'</td>';
		print '<td>'.$object->type_lib[$object->type].'</td></tr>';

		// Currency
		print '<tr><td>'.$langs->trans("Currency").'</td>';
		print '<td>';
		$selectedcode=$object->currency_code;
		if (! $selectedcode) $selectedcode=$conf->currency;
		print $langs->trans("Currency".$selectedcode);
		print '</td></tr>';

		// Conciliate
		print '<tr><td>'.$langs->trans("Conciliable").'</td>';
		print '<td>';
		$conciliate=$object->canBeConciliated();
		if ($conciliate == -2) print $langs->trans("No").' ('.$langs->trans("CashAccount").')';
		elseif ($conciliate == -3) print $langs->trans("No").' ('.$langs->trans("Closed").')';
		else print ($object->rappro==1 ? $langs->trans("Yes") : ($langs->trans("No").' ('.$langs->trans("ConciliationDisabled").')'));
		print '</td></tr>';

		print '<tr><td>'.$langs->trans("BalanceActual").'</td>';
		print '<td>'.price($balance).'</td></tr>';

		print '<tr><td>'.$langs->trans("BalanceAuthorized").'</td>';
		print '<td>'.price($authorized_balance).'</td></tr>';

		// Accountancy code
		print '<tr class="liste_titre_add"><td class="titlefield">'.$langs->trans("AccountancyCode").'</td>';
		print '<td>';
		if (! empty($conf->accounting->enabled)) {
			$accountingaccount = new AccountingAccount($db);
			$accountingaccount->fetch('', $object->account_number, 1);

			print $accountingaccount->getNomUrl(0, 1, 1, '', 1);
		} else {
			print $object->account_number;
		}
		print '</td></tr>';

		// Other attributes
		$cols = 2;
		include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

		print '</table>';

		print '</div>';
		print '<div class="fichehalfright">';
		print '<div class="ficheaddleft">';

		if ($object->type == Account::TYPE_SAVINGS || $object->type == Account::TYPE_CURRENT)
		{

			print '<div class="underbanner clearboth"></div>';

			print '<table class="border tableforfield centpercent">';

			print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("BankName").'</td>';
			print '<td>'.$object->bank.'</td></tr>';

			// Show fields of bank account
			foreach ($object->getFieldsToShow() as $val) {
				if ($val == 'BankCode') {
					$content = $object->code_banque;
				} elseif ($val == 'DeskCode') {
					$content = $object->code_guichet;
				} elseif ($val == 'BankAccountNumber') {
					$content = $object->number;
				} elseif ($val == 'BankAccountNumberKey') {
					$content = $object->cle_rib;
				}

				print '<tr><td>'.$langs->trans($val).'</td>';
				print '<td>'.$content.'</td>';
				print '</tr>';
			}

			$ibankey = FormBank::getIBANLabel($object);
			$bickey="BICNumber";
			if ($object->getCountryCode() == 'IN') $bickey="SWIFT";

			print '<tr><td>'.$langs->trans($ibankey).'</td>';
			print '<td>'.$object->iban.'&nbsp;';
			if (! empty($object->iban)) {
				if (! checkIbanForAccount($object)) {
					print img_picto($langs->trans("IbanNotValid"), 'warning');
				} else {
					print img_picto($langs->trans("IbanValid"), 'info');
				}
			}
			print '</td></tr>';

			print '</table>';
		}

		print '</div>';
		print '</div>';
		print '</div>';

		print '<div class="clearboth"></div>';

		dol_fiche_end();

		print $pagination;

		print '<div class="fichecenter">';

		print '<div class="underbanner clearboth"></div>';


		if (strcmp($bank_name, "QONTO") == 0) {
			print '
			<div class="div-table-responsive">
				<table class="border" width="100%">
					<thead>
						<tr class="liste_titre">
							<td>
								'.$langs->trans('Id').'
							</td>
							<td>
								'.$langs->trans('ValueDate').'
							</td>
							<td>
								'.$langs->trans('Thirdparty').'
							</td>
							<td>
								'.$langs->trans('Reference').'
							</td>
							<td>
								'.$langs->trans('Debit').'
							</td>
							<td>
								'.$langs->trans('Credit').'
							</td>
							<td>
								'.$langs->trans('PaymentMode').'
							</td>
							<td>
								'.$langs->trans('Status').'
							</td>
							<td>
								'.$langs->trans('Notes').'
							</td>
						</tr>
					</thead>
					<tbody>';
			$qonto = new Qonto($db);

			$tableau = $qonto->QontoTransactions($iban, $page, $accountid, $action, $object);		

			if (!empty($qonto->compte))
			{
				print $qonto->compte;
			}
			print '</tbody></table></div>';

		}else{
			$alert ='
			<div class="fiche">
			<div class="error">Désolé<br>Banque non prise en charge pour le moment...</div>
			</div>';
		}


		print $alert;
				
		

		print $pagination;
	}
// End of page
llxFooter();
$db->close();
