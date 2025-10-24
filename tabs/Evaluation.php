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

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/product/modules_product.class.php';

if (!empty($conf->propal->enabled))     require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (!empty($conf->facture->enabled))    require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
if (!empty($conf->commande->enabled))   require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
if (!empty($conf->accounting->enabled)) require_once DOL_DOCUMENT_ROOT.'/core/lib/accounting.lib.php';
if (!empty($conf->accounting->enabled)) require_once DOL_DOCUMENT_ROOT.'/core/class/html.formaccounting.class.php';
if (!empty($conf->accounting->enabled)) require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';

dol_include_once("/delegation/lib/form.php");
dol_include_once("/delegation/class/rating.class.php");

// Load translation files required by the page
$langs->loadLangs(array('products', 'other'));
if (!empty($conf->stock->enabled)) $langs->load("stocks");
if (!empty($conf->facture->enabled)) $langs->load("bills");
if (!empty($conf->productbatch->enabled)) $langs->load("productbatch");

$langs->load("rating@delegation");

$mesg = ''; $error = 0; $errors = array();

$refalreadyexists = 0;

$id = GETPOST('id', 'int');

$lineid = GETPOST('lineid', 'int');
$rate = GETPOST('edit_rating', 'int');
$elementrated = GETPOST('elementrated', 'alpha');
$elementtype = GETPOST('elementtype', 'alpha');
$comment = GETPOST('comment', 'alpha');


$ref = GETPOST('ref', 'alpha');
$type = (GETPOST('type', 'int') !== '') ? GETPOST('type', 'int') : Product::TYPE_PRODUCT;
$action = (GETPOST('action', 'alpha') ? GETPOST('action', 'alpha') : 'view');
$cancel = GETPOST('cancel', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$socid = GETPOST('socid', 'int');

if (!empty($user->socid)) $socid = $user->socid;

$object = new Product($db);
$object->type = $type; // so test later to fill $usercancxxx is correct


// Init rating
$rating = new Rating($db);
$rating->init($object,'product');

global $langs;

if ($id > 0 || !empty($ref))
{
    $result = $object->fetch($id, $ref);

    if (!empty($conf->product->enabled)) $upload_dir = $conf->product->multidir_output[$object->entity].'/'.get_exdir(0, 0, 0, 0, $object, 'product').dol_sanitizeFileName($object->ref);
    elseif (!empty($conf->service->enabled)) $upload_dir = $conf->service->multidir_output[$object->entity].'/'.get_exdir(0, 0, 0, 0, $object, 'product').dol_sanitizeFileName($object->ref);

    if (!empty($conf->global->PRODUCT_USE_OLD_PATH_FOR_PHOTO))    // For backward compatiblity, we scan also old dirs
    {
        if (!empty($conf->product->enabled)) $upload_dirold = $conf->product->multidir_output[$object->entity].'/'.substr(substr("000".$object->id, -2), 1, 1).'/'.substr(substr("000".$object->id, -2), 0, 1).'/'.$object->id."/photos";
        else $upload_dirold = $conf->service->multidir_output[$object->entity].'/'.substr(substr("000".$object->id, -2), 1, 1).'/'.substr(substr("000".$object->id, -2), 0, 1).'/'.$object->id."/photos";
    }
}

$modulepart = 'product';

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$canvas = !empty($object->canvas) ? $object->canvas : GETPOST("canvas");
$objcanvas = null;
if (!empty($canvas))
{
    require_once DOL_DOCUMENT_ROOT.'/core/class/canvas.class.php';
    $objcanvas = new Canvas($db, $action);
    $objcanvas->getCanvas('product', 'card', $canvas);
}

// Security check
$fieldvalue = (!empty($id) ? $id : (!empty($ref) ? $ref : ''));
$fieldtype = (!empty($id) ? 'rowid' : 'ref');
$result = restrictedArea($user, 'produit|service', $fieldvalue, 'product&product', '', '', $fieldtype);


/*
 * Actions
 */

if ($cancel) $action = '';


/*
 * View
 */

$title = $langs->trans('ProductServiceCard');
$helpurl = '';
$shortlabel = dol_trunc($object->label, 16);
if (GETPOST("type") == '0' || ($object->type == Product::TYPE_PRODUCT))
{
	$title = $langs->trans('Product')." ".$shortlabel." - ".$langs->trans('Card');
	$helpurl = 'EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos';
}
if (GETPOST("type") == '1' || ($object->type == Product::TYPE_SERVICE))
{
	$title = $langs->trans('Service')." ".$shortlabel." - ".$langs->trans('Card');
	$helpurl = 'EN:Module_Services_En|FR:Module_Services|ES:M&oacute;dulo_Servicios';
}

llxHeader('', $title, $helpurl);

$form = new Form($db);

$canAddLines = $user->admin || $user->rights->delegation->myactions->create;
$canReadLines = $user->admin || $user->rights->delegation->myactions->read;
$canDeleteLines = $user->admin || $user->rights->delegation->myactions->delete;

if (!$canReadLines)
{
	accessforbidden();
}

if (!$error && !$cancel)
{
    $result = $rating->call($action, array($user));

    if ($result > 0)
    {
        $message = $rating->error; //
    }
    else
    {
        $message = $rating->error;
        $error = true;
    }
}

$head = product_prepare_head($object);
$titre = $langs->trans("CardProduct".$object->type);
$picto = ($object->type == Product::TYPE_SERVICE ? 'service' : 'product');
dol_fiche_head($head, 'rating', $titre, 0, $picto);
	    	
include '../tpl/rating.default.tpl.php';

$db->close();
?>
