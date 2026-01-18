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

/**	    \file       htdocs/delegation/tpl/DC4.php
 *		\ingroup    delegation
 *		\brief      DC4 module tabs view
 */

$res=@include("../../main.inc.php");				// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory
//include 'core/lib/includeMain.lib.php';
require_once DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php";
//require_once DOL_DOCUMENT_ROOT ."/core/lib/generic.lib.php";

require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_order/modules_commandefournisseur.php';

require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

if (! empty($conf->supplier_proposal->enabled))
	require_once DOL_DOCUMENT_ROOT . '/supplier_proposal/class/supplier_proposal.class.php';
if (!empty($conf->produit->enabled))
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP

if (!empty($conf->variants->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/variants/class/ProductCombination.class.php';
}

dol_include_once("/delegation/class/dc4.class.php");

$langs->loadLangs(array("suppliers", "orders", "companies", "stocks"));


$id 			= GETPOST('id','int');
$ref 			= GETPOST('ref','alpha');

$confirm		= GETPOST('confirm','alpha');
$comclientid 	= GETPOST('comid','int');
$socid			= GETPOST('socid','int');
$projectid		= GETPOST('projectid','int');

$action 		= GETPOST('action', 'alpha');
$cancel         = GETPOST('cancel', 'alpha');

$lineid         = GETPOST('lineid', 'int');
$field          = GETPOST('field', 'alpha');

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'fournisseur', $id, 'commande_fournisseur', 'commande');

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('ordersuppliercard','globalcard'));

$form = new Form($db);
$object = new CommandeFournisseur($db);
$dc4 = new DC4($db);

// EN: Check module tab toggle and permissions.
// FR: Vérifier l'activation de l'onglet et les permissions.
if (empty($conf->global->DELEGATION_ENABLE_TAB_DC4_SUPPLIER)) {
	accessforbidden();
}

$canReadTab = $user->admin || $user->rights->delegation->tab_dc4_supplier_read;
$canWriteTab = $user->admin || $user->rights->delegation->tab_dc4_supplier_write;
$canAddLines = $canWriteTab;
$canDeleteLines = $canWriteTab;

// Load object
if ($id > 0 || ! empty($ref))
{
	$ret = $object->fetch($id, $ref);
	if ($ret < 0) dol_print_error($db, $object->error);
	$ret = $object->fetch_thirdparty();
	if ($ret < 0) dol_print_error($db, $object->error);
}
elseif (! empty($socid) && $socid > 0)
{
	$fourn = new Fournisseur($db);
	$ret=$fourn->fetch($socid);
	if ($ret < 0) dol_print_error($db, $object->error);
	$object->socid = $fourn->id;
	$ret = $object->fetch_thirdparty();
	if ($ret < 0) dol_print_error($db, $object->error);
}

$permissionnote=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_setnotes.inc.php
$permissiondellink=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_dellink.inc.php
$permissiontoedit=$user->rights->fournisseur->commande->creer;	// Used by the include of actions_lineupdown.inc.php
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
		$dc4->fetch();
		
		if ($object->element != 'order_supplier')// || $object->type != 5)
		{
			$error = true;
			$message = $langs->trans('NotASupplierOrder');
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
    $result = $dc4->call($action, array($user));

    if ($result > 0)
    {
        $message = $dc4->error; //
    }
    else
    {
        $message = $dc4->error;
        $error = true;
    }
}

if (! empty($conf->projet->enabled)) 
{
	$project = new Project($db);
	$project->fetch($object->fk_project);
}

$numLines = sizeof($dc4->lines);
$current_head = 'dc4_supplier';

include '../tpl/dc4.default.tpl.php';

$db->close();
?>
