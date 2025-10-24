<?php

/* Copyright (C) 2019-2020      Pierre Ardoin        <mapiolca@me.com>
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

/**     \file       htdocs/delegation/tpl/projet_fiche.default.tpl.php
 *      \ingroup    delegation
 *      \brief      Delegation module default view
 */



dol_fiche_head($head, $current_head, $langs->trans("Project"), -1, ($object->public?'projectpub':'project'));

// Project card

$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

$morehtmlref='<div class="refidno">';
// Title
$morehtmlref.=$object->title;
// Thirdparty
if ($object->thirdparty->id > 0)
{
    $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1, 'project');
}
$morehtmlref.='</div>';

dol_banner_tab($object, 'ref', $linkback, 0, 'ref', 'ref', $morehtmlref);


print '<div class="fichecenter">';
    print '<div class="fichehalfleft">';
        print '<div class="underbanner clearboth"></div>';
            print '<table class="border tableforfield" width="100%">';

// Visibility
            print '<tr><td class="titlefield">'.$langs->trans("Visibility").'</td><td>';
            if ($object->public) print $langs->trans('SharedProject');
            else print $langs->trans('PrivateProject');
            print '</td></tr>';

        if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
        {

// Opportunity status
            print '<tr><td>'.$langs->trans("OpportunityStatus").'</td><td>';
            $code = dol_getIdFromCode($db, $object->opp_status, 'c_lead_status', 'rowid', 'code');
            if ($code) print $langs->trans("OppStatus".$code);
            print '</td></tr>';

// Opportunity percent
            print '<tr><td>'.$langs->trans("OpportunityProbability").'</td><td>';
            if (strcmp($object->opp_percent, '')) print price($object->opp_percent, '', $langs, 1, 0).' %';
            print '</td></tr>';

// Opportunity Amount
            print '<tr><td>'.$langs->trans("OpportunityAmount").'</td><td>';
            if (strcmp($object->opp_amount, '')) print price($object->opp_amount, '', $langs, 1, 0, 0, $conf->currency);
            print '</td></tr>';
        }

// Date start - end
            print '<tr><td>'.$langs->trans("DateStart").' - '.$langs->trans("DateEnd").'</td><td>';
            $start = dol_print_date($object->date_start, 'day');
            print ($start?$start:'?');
            $end = dol_print_date($object->date_end, 'day');
            print ' - ';
            print ($end?$end:'?');
            if ($object->hasDelay()) print img_warning("Late");
            print '</td></tr>';

// Budget
            print '<tr><td>'.$langs->trans("Budget").'</td><td>';
            if (strcmp($object->budget_amount, '')) print price($object->budget_amount, '', $langs, 1, 0, 0, $conf->currency);
            print '</td></tr>';
        print '</table>';
    print '</div>';
    print '<div class="fichehalfright">';
        print '<div class="ficheaddleft">';
        print '<div class="underbanner clearboth"></div>';
            print '<table class="border tableforfield" width="100%">';

    // Description
                print '<tr>';
                    print '<td class="titlefield tdtop">'.$langs->trans("Description").'</td>';
                    print '<td>'.nl2br($object->description).'</td>';
                print '</tr>';

    // Bill time
            if (empty($conf->global->PROJECT_HIDE_TASKS) && ! empty($conf->global->PROJECT_BILL_TIME_SPENT))
            {
                print '<tr>';
                    print '<td class="valignmiddle">'.$langs->trans("BillTime").'</td>';
                    print '<td>'.yn($object->bill_time).'</td>';
                print '</tr>';
            }

    // Categories
            if($conf->categorie->enabled) {
                print '<tr>';
                    print '<td class="valignmiddle">'.$langs->trans("Categories").'</td>';
                    print '<td>'.$form->showCategories($object->id, 'project', 1).'</td>';
                print '</tr>';
            }
            print '</table>';
        print '</div>';
    print '</div>';
//print '</div>';
//print '<div class="underbanner clearboth"></div>';
?>

<?php //dol_fiche_end(); ?> 