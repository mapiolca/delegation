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

$canAddLines = $user->admin || $user->rights->delegation->myactions->create;
$canReadLines = $user->admin || $user->rights->delegation->myactions->read;
$canDeleteLines = $user->admin || $user->rights->delegation->myactions->delete;

if (!$canReadLines)
{
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
 
$soc = new Societe($db);
$soc->fetch($object->socid);

$totalpaye  = $object->getSommePaiement();

if (! empty($conf->projet->enabled)) 
{
	$project = new Project($db);
	$project->fetch($object->fk_project);
}

$numLines = sizeof($delegation->lines);
	    	
include '../tpl/delegation.default.tpl.php';

$db->close();
?>
