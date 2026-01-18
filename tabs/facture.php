<?php
/* Copyright (C) 2012-2020      Pierre Ardoin        <mapiolca@me.com>
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

/**	    \file       htdocs/delegation/tabs/facture.php
 *		\ingroup    delegation
 *		\brief      Delegation Facture module tabs view
 */


$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/modules/facture/modules_facture.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/invoice.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/price.lib.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.facture.class.php';
if (! empty($conf->projet->enabled)) 
{
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}

dol_include_once("/delegation/class/delegation.class.php");

$langs->load('bills');
$langs->load('companies');
$langs->load('compta');
$langs->load('products');
$langs->load('banks');
$langs->load('main');
$langs->load("delegation@delegation");
$langs->load('projects');


$id = GETPOST('id', 'int');
$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;

$action = GETPOST('action', 'alpha');
$cancel = GETPOST('cancel') ? true : false;
$error = false;
$message = '';
$formconfirm = null;

$form = new Form($db);
$object = new Facture($db);
$delegation = new Delegation($db);

// EN: Check module tab toggle and permissions.
// FR: Vérifier l'activation de l'onglet et les permissions.
if (empty($conf->global->DELEGATION_ENABLE_TAB_DELEGATION)) {
	accessforbidden();
}

$canReadTab = $user->admin || $user->rights->delegation->tab_delegation_read;
$canWriteTab = $user->admin || $user->rights->delegation->tab_delegation_write;
$canAddLines = $canWriteTab;
$canDeleteLines = $canWriteTab;

if (!$canReadTab)
{
	accessforbidden();
}

if (! empty($action) && ! $canWriteTab) {
	accessforbidden();
}

if ($id > 0)
{
	$result = $object->fetch($id);

	if ($result > 0)
	{
		$delegation->fetch();
		
		if ($object->element != 'facture')// || $object->type != 5)
		{
			$error = true;
			$message = $langs->trans('NotAnInvoice');
		}

	}
	else
	{
		$error = true;
		$message = $langs->trans('ObjectNotFound');
	}
}
else
{
	$error = true;
	$message = $langs->trans('ObjectNotFound');
}

if (!$error && !$cancel)
{
    $result = $delegation->call($action, array($user));

    if ($result > 0)
    {
        $message = $delegation->error; //
    }
    else
    {
        $message = $delegation->error;
        $error = true;
    }
}

$head = facture_prepare_head($object);
$current_head = 'delegation';
if (function_exists('complete_head_from_modules')) {
	complete_head_from_modules($conf, $langs, $object, $head, $current_head, 'invoice');
}
 
$soc = new Societe($db);
$soc->fetch($object->socid);

$totalpaye  = $object->getSommePaiement();

if (! empty($conf->projet->enabled)) 
{
	$project = new Project($db);
	$project->fetch($object->fk_project);
}
else
{
	$project = new stdClass();
	$project->id = 0;
}

$numLines = sizeof($delegation->lines);

if ($delegation->getSumDelegation() > $object->total_ttc) {
	setEventMessages($langs->trans('DelegationAmountExceeded'), null, 'warnings');
}

// EN: Prepare supplier invoice options and enrich delegation lines.
// FR: Préparer la liste des factures fournisseurs et enrichir les lignes de délégation.
$supplierInvoiceOptions = array();
$paymentModeId = ! empty($conf->global->DELEGATION_PAYMENT_MODE_ID) ? (int) $conf->global->DELEGATION_PAYMENT_MODE_ID : 0;

if (! empty($project->id) && $paymentModeId > 0) {
	$sql = "SELECT f.rowid";
	$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn as f";
	$sql.= " WHERE f.entity = ".(int) $conf->entity;
	$sql.= " AND f.fk_projet = ".(int) $project->id;
	$sql.= " AND f.fk_mode_reglement = ".(int) $paymentModeId;
	$sql.= " AND f.paye = 0";
	$sql.= " ORDER BY f.datef DESC";

	$resql = $db->query($sql);
	if ($resql) {
		while ($obj = $db->fetch_object($resql)) {
			$invoice = new FactureFournisseur($db);
			if ($invoice->fetch($obj->rowid) > 0) {
				$invoice->fetch_thirdparty();
				$paid = $invoice->getSommePaiement();
				$remaining = price2num($invoice->total_ttc - $paid, 'MT');
				if ($remaining > 0) {
					$supplierInvoiceOptions[$invoice->id] = $invoice->ref.' - '.$invoice->thirdparty->name.' - '.$langs->trans('RemainToPay').' '.price($remaining);
				}
			}
		}
	}
}

foreach ($delegation->lines as $line) {
	$line->supplier_invoice = null;
	if (! empty($line->fk_facture_fourn)) {
		$invoice = new FactureFournisseur($db);
		if ($invoice->fetch($line->fk_facture_fourn) > 0) {
			$invoice->fetch_thirdparty();
			$line->supplier_invoice = $invoice;
			$line->supplier_invoice_paid = $invoice->getSommePaiement();
			$line->supplier_invoice_remaining = price2num($invoice->total_ttc - $line->supplier_invoice_paid, 'MT');
		}
	}
}
	    	
include '../tpl/delegation.default.tpl.php';

$db->close();
?>
