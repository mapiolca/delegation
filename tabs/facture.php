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

// Load Dolibarr environment.
$res = false;
$paths = array(
	__DIR__.'/../../main.inc.php',
	__DIR__.'/../../../main.inc.php',
	__DIR__.'/../../../../main.inc.php',
	__DIR__.'/../../../../../main.inc.php',
);
foreach ($paths as $path) {
	if (file_exists($path)) {
		$res = include $path;
		if ($res) {
			break;
		}
	}
}
if (! $res) {
	die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
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

$langs->loadLangs(array('bills','companies','compta','products','banks','main','delegation@delegation','projects'));

$id = GETPOST('id', 'int');
$lineid = GETPOST('lineid', 'int');

$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel') ? true : false;
$error = false;
$message = '';
$formconfirm = null;

$form = new Form($db);
$object = new Facture($db);
$delegation = new Delegation($db);

// Check module tab toggle and permissions.
if (! getDolGlobalInt('DELEGATION_ENABLE_TAB_DELEGATION', 1)) {
	accessforbidden();
}

$canReadTab = $user->admin
	|| (! empty($user->rights->delegation->tab_delegation_read))
	|| (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read));
$canWriteTab = $user->admin
	|| (! empty($user->rights->delegation->tab_delegation_write))
	|| (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->create));
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
		$object->fetch_thirdparty();
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
	$h = 0;
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'invoice');
}

$soc = $object->thirdparty;

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

// Prepare supplier invoice options and enrich delegation lines.
$supplierInvoiceOptions = array();
$paymentModeId = ! empty($conf->global->DELEGATION_PAYMENT_MODE_ID) ? (int) $conf->global->DELEGATION_PAYMENT_MODE_ID : 0;

if (! empty($project->id) && $paymentModeId > 0) {
	$sql = "SELECT f.rowid, f.ref, f.total_ttc, f.datef, s.rowid as socid, s.nom as thirdparty_name,";
	$sql.= " COALESCE(SUM(pf.amount), 0) as paid";
	$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn as f";
	$sql.= " JOIN ".MAIN_DB_PREFIX."societe as s ON s.rowid = f.fk_soc";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_facturefourn as pf ON pf.fk_facturefourn = f.rowid";
	$sql.= " WHERE f.entity = ".(int) $conf->entity;
	$sql.= " AND f.fk_projet = ".(int) $project->id;
	$sql.= " AND f.fk_mode_reglement = ".(int) $paymentModeId;
	$sql.= " AND f.paye = 0";
	$sql.= " GROUP BY f.rowid, f.ref, f.total_ttc, f.datef, s.rowid, s.nom";
	$sql.= " ORDER BY f.datef DESC";

	$resql = $db->query($sql);
	if ($resql) {
		while ($obj = $db->fetch_object($resql)) {
			$remaining = price2num($obj->total_ttc - $obj->paid, 'MT');
			if ($remaining > 0) {
				$supplierInvoiceOptions[$obj->rowid] = $obj->ref.' - '.$obj->thirdparty_name.' - '.$langs->trans('RemainToPay').' '.price($remaining);
			}
		}
	}
}

$supplierInvoiceMap = array();
$supplierInvoiceIds = array();
foreach ($delegation->lines as $line) {
	if (! empty($line->fk_facture_fourn)) {
		$supplierInvoiceIds[] = (int) $line->fk_facture_fourn;
	}
}
$supplierInvoiceIds = array_unique($supplierInvoiceIds);

if (! empty($supplierInvoiceIds)) {
	$sql = "SELECT f.rowid, f.ref, f.total_ttc, f.datef, s.rowid as socid, s.nom as thirdparty_name,";
	$sql.= " COALESCE(SUM(pf.amount), 0) as paid";
	$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn as f";
	$sql.= " JOIN ".MAIN_DB_PREFIX."societe as s ON s.rowid = f.fk_soc";
	$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."paiementfourn_facturefourn as pf ON pf.fk_facturefourn = f.rowid";
	$sql.= " WHERE f.rowid IN (".implode(',', $supplierInvoiceIds).")";
	$sql.= " GROUP BY f.rowid, f.ref, f.total_ttc, f.datef, s.rowid, s.nom";

	$resql = $db->query($sql);
	if ($resql) {
		while ($obj = $db->fetch_object($resql)) {
			$invoice = new FactureFournisseur($db);
			$invoice->id = (int) $obj->rowid;
			$invoice->ref = $obj->ref;
			$invoice->total_ttc = (float) $obj->total_ttc;
			$invoice->datef = $obj->datef;
			$invoice->thirdparty = new Societe($db);
			$invoice->thirdparty->id = (int) $obj->socid;
			$invoice->thirdparty->name = $obj->thirdparty_name;

			$supplierInvoiceMap[$invoice->id] = array(
				'invoice' => $invoice,
				'paid' => (float) $obj->paid,
			);
		}
	}
}

foreach ($delegation->lines as $line) {
	$line->supplier_invoice = null;
	if (! empty($line->fk_facture_fourn) && ! empty($supplierInvoiceMap[$line->fk_facture_fourn])) {
		$line->supplier_invoice = $supplierInvoiceMap[$line->fk_facture_fourn]['invoice'];
		$line->supplier_invoice_paid = $supplierInvoiceMap[$line->fk_facture_fourn]['paid'];
		$line->supplier_invoice_remaining = price2num($line->supplier_invoice->total_ttc - $line->supplier_invoice_paid, 'MT');
	}
}

include '../tpl/delegation.default.tpl.php';

$db->close();
?>
