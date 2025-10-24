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

/**	    \file       htdocs/delegation/tabs/Details.php
 *		\ingroup    delegation
 *		\brief      Project Details module tabs view
 */



$res=@include("../../main.inc.php");					// For root directory
if (! $res) $res=@include("../../../main.inc.php");	// For "custom" directory

require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/modules/project/modules_project.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

dol_include_once("/delegation/class/detailprojet.class.php");

$langs->load('companies');
$langs->load('compta');
$langs->load('products');
$langs->load('banks');
$langs->load('main');
$langs->load("delegation@delegation");
$langs->load('projects');


$id = GETPOST('id', 'int');
$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
$field = GETPOST('field');

$action = GETPOST('action', 'alpha');
$cancel = GETPOST('cancel') ? true : false;
$error = false;
$message = '';
$formconfirm = null;

$form = new Form($db);
$object = new Project($db);
$details = new Details($db);
$soc = new Societe($db);

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
		$details->fetch();
		
		if ($object->element != 'project')// || $object->type != 5)
		{
			$error = true;
			$message = $langs->trans('NotAProject');
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
    $result = $details->call($action, array($user));

    if ($result > 0)
    {
        $message = $details->error; //
    }
    else
    {
        $message = $details->error;
        $error = true;
    }
}

$soc->fetch($object->socid);

$numLines = sizeof($details->lines);

// Security check
$socid=$object->socid;
//if ($user->socid > 0) $socid = $user->socid;    // For external user, no check is done on company because readability is managed by public status of project and assignement.
$result = restrictedArea($user, 'projet',  $object->id, 'projet&project');
	    	
/*
 *  View
 */

$title=$langs->trans("ProjectDetails").' - '.$object->ref.' '.$object->title;
if (! empty($conf->global->MAIN_HTML_TITLE) && preg_match('/projectnameonly/', $conf->global->MAIN_HTML_TITLE) && $object->title) $title=$object->ref.' '.$object->title.' - '.$langs->trans("ProjectDetails");

llxHeader("", $langs->trans("ProjectDetails").' - '.$object->ref);

// To verify role of users
$userAccess = $object->restrictedProjectArea($user);

$head=project_prepare_head($object);

$current_head = 'details';

include '../tpl/projet_fiche.default.tpl.php';	    	

print load_fiche_titre($langs->trans("project_details"), '', 'project');

include '../tpl/details.default.tpl.php';

$db->close();


?>
