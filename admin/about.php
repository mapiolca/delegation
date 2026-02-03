<?php
/**
 * Page À propos pour le module Delegation.
 */

// EN: Attempt to load Dolibarr bootstrap from common locations.
// FR: Tente de charger l'amorçage Dolibarr depuis les emplacements courants.
$res = 0;
if (!$res && file_exists(__DIR__.'/../main.inc.php')) {
        $res = require_once __DIR__.'/../main.inc.php';
}
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
        $res = require_once __DIR__.'/../../main.inc.php';
}
if (!$res && file_exists(__DIR__.'/../../../main.inc.php')) {
        $res = require_once __DIR__.'/../../../main.inc.php';
}
if (!$res) {
        die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
dol_include_once('/delegation/core/modules/modDelegation.class.php');
require_once DOL_DOCUMENT_ROOT.'/custom/delegation/class/lmdb.class.php';


// EN: Load admin and module translations for the about page.
// FR: Charge les traductions d'administration et du module pour la page À propos.
$langs->loadLangs(array('admin', 'delegation@delegation'));

// EN: Only Dolibarr administrators can display the about page.
// FR: Seuls les administrateurs Dolibarr peuvent afficher la page À propos.
if (empty($user->admin)) {
        accessforbidden();
}

if(!$user->admin or empty($conf->delegation->enabled))
	accessforbidden();

global $mysoc;

$langs->loadLangs(array("admin", "delegation@delegation"));

$moduleDescriptor = new modDelegation($db);
$title = $langs->trans('DelegationAbout');
$helpurl = '';

$delegationAdminLibPath = dol_buildpath('/delegation/lib/admin.lib.php', 0);
if ((function_exists('dol_is_file') && dol_is_file($delegationAdminLibPath)) || (! function_exists('dol_is_file') && is_file($delegationAdminLibPath))) {
	require_once $delegationAdminLibPath;
}

llxHeader('', $title, $helpurl);

$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans($title), $linkback, 'info');
$head = lmdb_prepare_head();
// EN: Render the admin tabs with the bookcal pictogram to stay consistent with the setup header.
// FR: Affiche les onglets d'administration avec le pictogramme bookcal pour rester cohérent avec la configuration.
print dol_get_fiche_head($head, 'About', $title, -1, '');

print '<div class="underbanner opacitymedium">'.$langs->trans('DelegationAboutPage').'</div>';
print '<br>';

print '<div class="fichecenter">';

// EN: Present core module information in a dedicated summary table.
// FR: Présente les informations principales du module dans un tableau récapitulatif.
print '<div class="fichehalfleft">';
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><th colspan="2">'.$langs->trans('DelegationAboutGeneral').'</th></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutVersion').'</td><td>'.dol_escape_htmltag($moduleDescriptor->version).'</td></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutFamily').'</td><td>'.dol_escape_htmltag($moduleDescriptor->family).'</td></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutDescription').'</td><td>'.dol_escape_htmltag($langs->trans($moduleDescriptor->description)).'</td></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutMaintainer').'</td><td>'.dol_escape_htmltag($moduleDescriptor->editor_name).'</td></tr>';
print '</table>';
print '</div>';
print '</div>';

// EN: List documentation and support resources with direct links.
// FR: Liste les ressources de documentation et de support avec des liens directs.
print '<div class="fichehalfright">';
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><th colspan="2">'.$langs->trans('DelegationAboutResources').'</th></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutDocumentation').'</td><td><a href="'.dol_buildpath('/Delegation/README.md', 1).'" target="_blank" rel="noopener">'.$langs->trans('DelegationAboutDocumentationLink').'</a></td></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutSupport').'</td><td>'.dol_escape_htmltag($langs->trans('DelegationAboutSupportValue')).'</td></tr>';
print '<tr class="oddeven"><td class="titlefield">'.$langs->trans('DelegationAboutContact').'</td><td><a href="https://'.$moduleDescriptor->editor_url.'" target="_blank" rel="noopener">'.dol_escape_htmltag($moduleDescriptor->editor_url).'</a></td></tr>';
print '</table>';
print '</div>';
print '</div>';

print '</div>';

print dol_get_fiche_end();

llxFooter();
$db->close();
