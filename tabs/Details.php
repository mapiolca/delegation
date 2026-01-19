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

require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/modules/project/modules_project.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php";
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

dol_include_once("/delegation/class/detailprojet.class.php");

$langs->loadLangs(array('companies','compta','products','banks','main','delegation@delegation','projects'));


$id = GETPOST('id', 'int');
$lineid = GETPOST('lineid', 'int');
$field = GETPOST('field', 'alpha');

$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel') ? true : false;
$error = false;
$message = '';
$formconfirm = null;

$form = new Form($db);
$object = new Project($db);
$details = new Details($db);
$soc = new Societe($db);

// Check module tab toggle and permissions.
if (! getDolGlobalInt('DELEGATION_ENABLE_TAB_DETAILS', 1)) {
	accessforbidden();
}

$canReadTab = $user->admin
	|| (! empty($user->rights->delegation->tab_details_read))
	|| (! empty($user->rights->delegation->myactions) && ! empty($user->rights->delegation->myactions->read));
$canWriteTab = $user->admin
	|| (! empty($user->rights->delegation->tab_details_write))
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
		if (method_exists($object, 'fetch_thirdparty')) {
			$object->fetch_thirdparty();
		}
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
	    	
$userAccess = $object->restrictedProjectArea($user);

$head = project_prepare_head($object);
$current_head = 'details';
if (function_exists('complete_head_from_modules')) {
	$h = 0;
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'project');
}

llxHeader("", $langs->trans("ProjectDetails").' - '.$object->ref);
print dol_get_fiche_head($head, $current_head, $langs->trans("Project"), -1, ($object->public?'projectpub':'project'));

$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';
$morehtmlref = '<div class="refidno">';
$morehtmlref .= $object->title;
if (! empty($object->thirdparty) && ! empty($object->thirdparty->id)) {
	$morehtmlref .= '<br>'.$langs->trans('ThirdParty').' : '.$object->thirdparty->getNomUrl(1, 'project');
}
$morehtmlref .= '</div>';
dol_banner_tab($object, 'ref', $linkback, 0, 'ref', 'ref', $morehtmlref);
print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';

print load_fiche_titre($langs->trans("project_details"), '', 'project');

include '../tpl/details.default.tpl.php';

print dol_get_fiche_end();
print '</div>';
llxFooter();
$db->close();

?>
