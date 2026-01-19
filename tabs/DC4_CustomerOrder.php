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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formmargin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/commande/modules_commande.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
if (!empty($conf->propal->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
}
if (!empty($conf->produit->enabled))
	require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}

if (!empty($conf->variants->enabled)) {
	require_once DOL_DOCUMENT_ROOT.'/variants/class/ProductCombination.class.php';
}

dol_include_once("/delegation/class/dc4.class.php");

$langs->loadLangs(array('orders', 'sendings', 'companies', 'bills', 'propal', 'deliveries', 'products', 'other', 'delegation@delegation'));

$id = (GETPOST('id', 'int') ? GETPOST('id', 'int') : GETPOST('orderid', 'int'));
$ref = GETPOST('ref','alpha');

$confirm		= GETPOST('confirm','alpha');
$comclientid 	= GETPOST('comid','int');
$socid			= GETPOST('socid','int');
$projectid		= GETPOST('projectid','int');

$action = GETPOST('action', 'aZ09');
$cancel         = GETPOST('cancel', 'alpha');

$lineid = GETPOST('lineid', 'int');
$field = GETPOST('field', 'alpha');

// Security check
if (!empty($user->socid))
	$socid = $user->socid;
$result = restrictedArea($user, 'commande', $id);

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('ordercard','globalcard'));

$form = new Form($db);
$object = new Commande($db);
$dc4 = new DC4($db);

// Check module tab toggle and permissions.
if (! getDolGlobalInt('DELEGATION_ENABLE_TAB_DC4_CUSTOMER', 1)) {
	accessforbidden();
}

$canReadTab = $user->admin
	|| (! empty($user->rights->delegation->tab_dc4_customer_read))
	|| (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read));
$canWriteTab = $user->admin
	|| (! empty($user->rights->delegation->tab_dc4_customer_write))
	|| (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->create));
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
	$soc = new Societe($db);
	$ret=$soc->fetch($socid);
	if ($ret < 0) dol_print_error($db, $object->error);
	$object->socid = $soc->id;
	$ret = $object->fetch_thirdparty();
	if ($ret < 0) dol_print_error($db, $object->error);
}

$usercanread = $user->rights->commande->lire;
$usercancreate = $user->rights->commande->creer;
$usercanclose = $user->rights->commande->cloturer;
$usercandelete = $user->rights->commande->supprimer;
$usercanvalidate = ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && $usercancreate) || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->commande->order_advance->validate)));
$usercancancel = ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && $usercancreate) || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->commande->order_advance->annuler)));
$usercansend = (empty($conf->global->MAIN_USE_ADVANCED_PERMS) || $user->rights->commande->order_advance->send);

$usercancreatepurchaseorder = $user->rights->fournisseur->commande->creer;

$permissionnote = $usercancreate; 		// Used by the include of actions_setnotes.inc.php
$permissiondellink = $usercancreate; 	// Used by the include of actions_dellink.inc.php
$permissiontoadd = $usercancreate; 		// Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php


if (!$usercanread || ! $canReadTab)
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
		$dc4->fetch();
		
		if ($object->element != 'commande')
		{
			$error = true;
			$message = $langs->trans('NotAnOrder');
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
$current_head = 'dc4_customer';

$head = commande_prepare_head($object);
$linkback = '<a href="'.DOL_URL_ROOT.'/commande/list.php?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';
$title = $langs->trans('Order');

$morehtmlref = '<div class="refidno">';
$morehtmlref .= $form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
$morehtmlref .= $form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
if (! empty($object->thirdparty)) {
	$morehtmlref .= '<br>'.$langs->trans('ThirdParty').' : '.$object->thirdparty->getNomUrl(1);
}
if (! empty($conf->projet->enabled) && ! empty($object->fk_project)) {
	$proj = new Project($db);
	$proj->fetch($object->fk_project);
	$morehtmlref .= '<br>'.$langs->trans('Project').' : '.$proj->getNomUrl(1);
}
$morehtmlref .= '</div>';

llxHeader('', $langs->trans("DC4form").' - '.$langs->trans("Order"), 'EN:Customers_Orders|FR:Commandes_Clients|ES:Pedidos de clientes');
print dol_get_fiche_head($head, $current_head, $title, -1, 'order');
dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);
print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';

include '../tpl/dc4.default.tpl.php';

$print_fiche_end = dol_get_fiche_end();
if ($print_fiche_end) {
	print $print_fiche_end;
}
print '</div>';
llxFooter();

$db->close();
?>
