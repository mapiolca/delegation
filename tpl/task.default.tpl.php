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

    if ($projectstatic->id > 0 || $allprojectforuser > 0) {

        $formconfirm = '';

        // Print form confirm
        print $formconfirm;

        print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
        if ($optioncss != '') {
            print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
        }
        print '<input type="hidden" name="token" value="'.newToken().'">';
        print '<input type="hidden" name="id" value="'.$id.'">';
        print '<input type="hidden" name="projectid" value="'.$projectidforalltimes.'">';
        print '<input type="hidden" name="withproject" value="'.$withproject.'">';
        print '<input type="hidden" name="tab" value="'.$tab.'">';
        print '<input type="hidden" name="page_y" value="">';


        /*
         *  Content
         */


        print '<div class="div-table-responsive">';
        print '<table class="tagtable nobottomiftotal liste'.($moreforfilter ? " listwithfilterbefore" : "").'">'."\n";

        print "</table>";
        print '</div>';
        print "</form>";
    }


?>
<?php 

$task_data = new Task($db);
$task_lines->fetch_task($id);

$numTasks = sizeof($task_lines->tasks);

            print '<table class="border" width="100%">';
                print '<tbody>';
                    print '<tr>';
                        print '<td>';
                            print ''.$langs->trans("BudgetConsigne").'';

    if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '0') // All
    {
                            print ''.$langs->trans("Draft").' | ';
                            print ''.$langs->trans("Validate").' | ';
                            print ''.$langs->trans("Approved").' | ';
                            print ''.$langs->trans("ReceivedPartially").' | ';
                            print ''.$langs->trans("Received").' | '.$langs->trans("Closed").'';
    }
    if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '1') // All without Draft
    {
                            print ''.$langs->trans("Validate").' | ';
                            print ''.$langs->trans("Approved").' | ';
                            print ''.$langs->trans("ReceivedPartially").' | ';
                            print ''.$langs->trans("Received").'|'.$langs->trans("Closed").'';
    }
    if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '2') // Approved + Sent + Received + Received Partially
    {
                            print ''.$langs->trans("Approved").' | ';
                            print ''.$langs->trans("Ordered").' | ';
                            print ''.$langs->trans("ReceivedPartially").' | ';
                            print ''.$langs->trans("Received").' | '.$langs->trans("Closed").' | ';
    }
    if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '3') // Sent + Received + Received Partially
    {
                            print ''.$langs->trans("Ordered").' ';
                            print ''.$langs->trans("ReceivedPartially").' | ';
                            print ''.$langs->trans("Received").' | '.$langs->trans("Closed").'';
    }
    if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '4') // Received + Received Partially
    {
                            print ''.$langs->trans("ReceivedPartially").' | ';
                            print ''.$langs->trans("Received").' | '.$langs->trans("Closed").'';
    }
    if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '5') // Received
    {
                            print ''.$langs->trans("Received").' | '.$langs->trans("Closed").'';
    }
    if ($conf->global->LMDB_BUDGET_ORDER_STATUS_REFUSED == '1') 
    {
                            print ' | '.$langs->trans("Refused").' ';
    }
                        print '</td>';
                    print '</tr>';
                print '</tbody>';
            print '</table>';
    
    for ($i=0; $i < $numTasks ; $i++) { 

        $total_ht_poste = '0';
        $total_ht_total_poste = '0';
        $budget_previsionnel_poste = '0';
        $budget_previsionnel_total_post = '0';
        $restant_poste = '0';
        $restant_total_poste = '0';

        $task0 = $task_lines->tasks[$i];
        $poste = $task0->id;

        $task_lines->fetch_task_child($id, $poste);
        
        $numTasks_child = sizeof($task_lines->task_child);
        if ($numTasks_child) {
        
            print '<table id="table" class="noborder" width="100%">';
                print '<thead>';
                    print '<tr>';
                        print '<th colspan="2" rowspan="2">';
                            print ''.$langs->trans('Expenses').'';
                        print '</th>';
        
            $budget_previsionnel_total_poste = "0";

            for ($i1=0; $i1 < $numTasks_child ; $i1++) {
            $task1 = $task_lines->task_child[$i1];

                        print '<th colspan="2">';
                            print ''.$task1->label.'';
                        print '</th>';
            }
                        print'<th colspan="2">';
                            print ''.$langs->trans('Total').'';
                        print '</th>';
                    print '</tr>';
                    print '<tr>';
        
            for ($i2=0; $i2 < $numTasks_child ; $i2++) {
            $task2 = $task_lines->task_child[$i2];
                        print '<th colspan="1">';
                            print ''.$langs->trans('Forecast').'';
                        print '</th>';
                        print '<th colspan="1">';
                            print ''.$langs->trans('Real').'';
                        print '</th>';
            }
                        print '<th colspan="1">';
                            print ''.$langs->trans('Forecast').'';
                        print '</th>';
                        print '<th colspan="1">';
                            print ''.$langs->trans('Real').'';
                        print '</th>';
                    print '</tr>';
                print '</thead>';
                print '<tbody>';
                    print '<tr>';
                        print '<td rowspan="2">';
                            print ''.$task0->label.'';
                        print '</td>';
                        print '<td>';
                            print ''.$langs->trans('ExpensesOT').'';
                        print '</td>';

            for ($i3=0; $i3 < $numTasks_child ; $i3++) {

                $task3 = $task_lines->task_child[$i3];

                $task_data->fetch($task3->id);

                $task_lines->fetch_order_child($id, $task3->id);

                $numOrders = sizeof($task_lines->orders);
               
                $total_ht_poste = "0";

                for ($o=0; $o < $numOrders ; $o++) { 
                    $order = $task_lines->orders[$o];

                    $total_ht_poste += $order->total_ht;
                    $total_ht_total_poste += $order->total_ht;
                }

                $budget_previsionnel = $task_data->array_options['options_lmdb_budget'];
                        print'<td align="center">';
                            print ''.price($budget_previsionnel).'€';
                        print '</td>';
                        print '<td align="center">';
                            print ''.price($total_ht_poste).'€';
                        print '</td>';

                $budget_previsionnel_total_poste += $budget_previsionnel;
                $budget_previsionnel_total += $budget_previsionnel;

            }
                        print '<td align="center">';
                            print ''.price($budget_previsionnel_total_poste).'€';
                        print '</td>';
                        print '<td align="center">';
                            print ''.price($total_ht_total_poste).'€';
                        print '</td>';
                    print '</tr>';
                    print '<tr>';
                        print '<td>';
                            print ''.$langs->trans('RemainingOT').'';
                        print '</td>';

            for ($i4=0; $i4 < $numTasks_child ; $i4++) {
                $task4 = $task_lines->task_child[$i4];
        
                $total_ht_poste = '0';
                //$total_ht_total_poste = '0';
                $budget_previsionnel_poste = '0';
                //$budget_previsionnel_total_post = '0';
                $restant_poste = '0';
                //$restant_total_poste = '0';
                $task_data->fetch($task4->id);

                $task_lines->fetch_order_child($id, $task4->id);

                $numOrders1 = sizeof($task_lines->orders);
        
                for ($o1=0; $o1 < $numOrders1 ; $o1++) { 
                    $order1 = $task_lines->orders[$o1];

                    $total_ht_poste += $order1->total_ht;
                    $total_ht_total_poste += $order1->total_ht;
                    $total_ht += $order1->total_ht;
                }

                $budget_previsionnel_poste = $task_data->array_options['options_lmdb_budget'];

                $restant_poste = $budget_previsionnel_poste - $total_ht_poste ;

                $restant_poste_pourcent = (1 - ($total_ht_poste / $budget_previsionnel_poste)) * 100;

                $restant_total_poste += $restant_poste;

                $restant_total_poste_pourcent = (1 - ($total_ht_total_poste / $budget_previsionnel_total_poste)) * 100;

                $restant_total +=$restant_poste;
                $restant_total_pourcent  = (1 - ($total_ht / $budget_previsionnel_total)) * 100;

                        print'<td align="center" colspan="2">';
                            print ''.price($restant_poste).'€ | '.price($restant_poste_pourcent).'%';
                        print '</td>';
            }
                        print '<td align="center" colspan="2">';
                            print ''.price($restant_total_poste).'€ | '.price($restant_total_poste_pourcent).'%';
                        print '</td>';
                    print '</tr>';
                print '</tbody>';
            print '</table>';
        }
    }
            print '<table id="table" class="noborder" width="100%">';
                print '<tfoot>';
                    print '<tr>';
                        print '<td align="right" colspan="1">';
                            print ''.$langs->trans('TotalBudgetOT').'';
                        print '</td>';
                        print '<td align="left" colspan="1">';
                            print ''.price($budget_previsionnel_total).'€';
                        print '</td>';
                        print '<td align="right" colspan="1">';
                            print ''.$langs->trans('TotalEpensesOT').'';
                        print '</td>';
                        print '<td align="left" colspan="1">';
                            print ''.price($total_ht).'€';
                        print '</td>';
                        print '<td align="right" colspan="1">';
                            print ''.$langs->trans('TotalRemainingOT').'';
                        print '</td>';
                        print '<td align="left" colspan="1">';
                            print ''.price($restant_total).'€ | '.price($restant_total_pourcent).'%';
                        print '</td>';
                    print '</tr>';
                print '</tfoot>';
            print '</table>';


    for ($i0=0; $i0 < $numTasks; $i0++) { 
        $task0 = $task_lines->tasks[$i0];
        $poste = $task0->id;

        $task_lines->fetch_task_child($id, $poste);
        $numTasks_child = sizeof($task_lines->task_child);

        if (!$numTasks_child) {
            $t[] = '1';
        }
        
    }

    $nb = count($t);

    if ($nb == '1') {
            print '<div class="warning">'.$langs->trans("Le Poste/Tâche suivants n'est pas pris en compte car il ne contient pas de sous-poste : ").'';
        for ($i0=0; $i0 < $numTasks; $i0++) { 
            $task0 = $task_lines->tasks[$i0];
            $poste = $task0->id;

            $task_lines->fetch_task_child($id, $poste);
            $numTasks_child = sizeof($task_lines->task_child);

            if (!$numTasks_child) {
                print '<li>'.$task0->label.'</li>';
            }
        }
            print '</div>';
    }elseif($nb >> '1'){
            print '<div class="warning">'.$langs->trans("Les Postes/Tâches suivants ne sont pas pris en compte car ils ne contiennent pas de sous-poste : ").'';
        for ($i0=0; $i0 < $numTasks; $i0++) { 
            $task0 = $task_lines->tasks[$i0];
            $poste = $task0->id;

            $task_lines->fetch_task_child($id, $poste);
            $numTasks_child = sizeof($task_lines->task_child);

            if (!$numTasks_child) {
                print '<li>'.$task0->label.'</li>';
            }
        }
            print '</div>';
    }
        print '</div>';
?>