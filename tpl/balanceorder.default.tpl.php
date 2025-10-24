<?php
/* Copyright (C) 2020      Pierre Ardoin        <mapiolca@me.com>
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

/**     \file       htdocs/delegation/tpl/balanceorder.default.tpl.php
 *      \ingroup    delegation
 *      \brief      Balance Order module default view
 */

if (! empty($conf->propal->enabled))        require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (! empty($conf->facture->enabled))       require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
if (! empty($conf->facture->enabled))       require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture-rec.class.php';
if (! empty($conf->commande->enabled))      require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
if (! empty($conf->supplier_proposal->enabled)) require_once DOL_DOCUMENT_ROOT.'/supplier_proposal/class/supplier_proposal.class.php';
if (! empty($conf->fournisseur->enabled))   require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
if (! empty($conf->fournisseur->enabled))   require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
if (! empty($conf->contrat->enabled))       require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
if (! empty($conf->ficheinter->enabled))    require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
if (! empty($conf->expedition->enabled))    require_once DOL_DOCUMENT_ROOT.'/expedition/class/expedition.class.php';
if (! empty($conf->deplacement->enabled))   require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
if (! empty($conf->expensereport->enabled)) require_once DOL_DOCUMENT_ROOT.'/expensereport/class/expensereport.class.php';
if (! empty($conf->agenda->enabled))        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
if (! empty($conf->don->enabled))           require_once DOL_DOCUMENT_ROOT.'/don/class/don.class.php';
if (! empty($conf->loan->enabled))          require_once DOL_DOCUMENT_ROOT.'/loan/class/loan.class.php';
if (! empty($conf->loan->enabled))          require_once DOL_DOCUMENT_ROOT.'/loan/class/loanschedule.class.php';
if (! empty($conf->stock->enabled))         require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
if (! empty($conf->tax->enabled))           require_once DOL_DOCUMENT_ROOT.'/compta/sociales/class/chargesociales.class.php';
if (! empty($conf->banque->enabled))        require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/paymentvarious.class.php';
if (! empty($conf->salaries->enabled))      require_once DOL_DOCUMENT_ROOT.'/salaries/class/paymentsalary.class.php';
if (! empty($conf->categorie->enabled))     require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

// Load translation files required by the page
$langs->loadLangs(array('projects', 'companies', 'suppliers', 'compta'));
if (! empty($conf->facture->enabled))       $langs->load("bills");
if (! empty($conf->commande->enabled))      $langs->load("orders");
if (! empty($conf->propal->enabled))        $langs->load("propal");
if (! empty($conf->ficheinter->enabled))    $langs->load("interventions");
if (! empty($conf->deplacement->enabled))   $langs->load("trips");
if (! empty($conf->expensereport->enabled)) $langs->load("trips");
if (! empty($conf->don->enabled))           $langs->load("donations");
if (! empty($conf->loan->enabled))          $langs->load("loan");
if (! empty($conf->salaries->enabled))      $langs->load("salaries");
$datesrfc=GETPOST('datesrfc');
$dateerfc=GETPOST('dateerfc');
$dates=dol_mktime(0, 0, 0, GETPOST('datesmonth'), GETPOST('datesday'), GETPOST('datesyear'));
$datee=dol_mktime(23, 59, 59, GETPOST('dateemonth'), GETPOST('dateeday'), GETPOST('dateeyear'));
if (empty($dates) && ! empty($datesrfc)) $dates=dol_stringtotime($datesrfc);
if (empty($datee) && ! empty($dateerfc)) $datee=dol_stringtotime($dateerfc);
if (! isset($_POST['datesrfc']) && ! isset($_POST['datesday']) && ! empty($conf->global->PROJECT_LINKED_ELEMENT_DEFAULT_FILTER_YEAR))
{
    $new=dol_now();
    $tmp=dol_getdate($new);
    //$datee=$now
    //$dates=dol_time_plus_duree($datee, -1, 'y');
    $dates=dol_get_first_day($tmp['year'], 1);
}
if ($id == '' && $ref == '')
{
    setEventMessage($langs->trans('ErrorBadParameters'), 'errors');
    header('Location: list.php');
    exit();
}

$mine = $_REQUEST['mode']=='mine' ? 1 : 0;
//if (! $user->rights->projet->all->lire) $mine=1;  // Special for projects

/*
 * Referers types 
 */

$listofreferent = array(
'propal'=>array(
    'name'=>"Proposals",
    'title'=>"ListProposalsAssociatedProject",
    'class'=>'Propal',
    'table'=>'propal',
    'datefieldname'=>'datep',
    'urlnew'=>DOL_URL_ROOT.'/comm/propal/card.php?action=create&origin=project&originid='.$id.'&socid='.$socid,
    'lang'=>'propal',
    'buttonnew'=>'AddProp',
    'testnew'=>$user->rights->propal->creer,
    'test'=>$conf->propal->enabled && $user->rights->propale->lire),
'order'=>array(
    'name'=>"CustomersOrders",
    'title'=>"ListOrdersAssociatedProject",
    'class'=>'Commande',
    'table'=>'commande',
    'datefieldname'=>'date_commande',
    'urlnew'=>DOL_URL_ROOT.'/commande/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'orders',
    'buttonnew'=>'CreateOrder',
    'testnew'=>$user->rights->commande->creer,
    'test'=>$conf->commande->enabled && $user->rights->commande->lire),
'invoice'=>array(
    'name'=>"CustomersInvoices",
    'title'=>"ListInvoicesAssociatedProject",
    'class'=>'Facture',
    'margin'=>'add',
    'table'=>'facture',
    'datefieldname'=>'datef',
    'urlnew'=>DOL_URL_ROOT.'/compta/facture/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'bills',
    'buttonnew'=>'CreateBill',
    'testnew'=>$user->rights->facture->creer,
    'test'=>$conf->facture->enabled && $user->rights->facture->lire),
'invoice_predefined'=>array(
    'name'=>"PredefinedInvoices",
    'title'=>"ListPredefinedInvoicesAssociatedProject",
    'class'=>'FactureRec',
    'table'=>'facture_rec',
    'datefieldname'=>'datec',
    'urlnew'=>DOL_URL_ROOT.'/compta/facture/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'bills',
    'buttonnew'=>'CreateBill',
    'testnew'=>$user->rights->facture->creer,
    'test'=>$conf->facture->enabled && $user->rights->facture->lire),
'proposal_supplier'=>array(
    'name'=>"SuppliersProposals",
    'title'=>"ListSupplierProposalsAssociatedProject",
    'class'=>'SupplierProposal',
    'table'=>'supplier_proposal',
    'datefieldname'=>'date_valid',
    'urlnew'=>DOL_URL_ROOT.'/supplier_proposal/card.php?action=create&projectid='.$id, // No socid parameter here, the socid is often the customer and we create a supplier object
    'lang'=>'supplier_proposal',
    'buttonnew'=>'AddSupplierProposal',
    'testnew'=>$user->rights->supplier_proposal->creer,
    'test'=>$conf->supplier_proposal->enabled && $user->rights->supplier_proposal->lire),
'order_supplier'=>array(
    'name'=>"SuppliersOrders",
    'title'=>"ListSupplierOrdersAssociatedProject",
    'class'=>'CommandeFournisseur',
    'table'=>'commande_fournisseur',
    'datefieldname'=>'date_commande',
    'urlnew'=>DOL_URL_ROOT.'/fourn/commande/card.php?action=create&projectid='.$id, // No socid parameter here, the socid is often the customer and we create a supplier object
    'lang'=>'suppliers',
    'buttonnew'=>'AddSupplierOrder',
    'testnew'=>$user->rights->fournisseur->commande->creer,
    'test'=>$conf->supplier_order->enabled && $user->rights->fournisseur->commande->lire),
'invoice_supplier'=>array(
    'name'=>"BillsSuppliers",
    'title'=>"ListSupplierInvoicesAssociatedProject",
    'class'=>'FactureFournisseur',
    'margin'=>'minus',
    'table'=>'facture_fourn',
    'datefieldname'=>'datef',
    'urlnew'=>DOL_URL_ROOT.'/fourn/facture/card.php?action=create&projectid='.$id, // No socid parameter here, the socid is often the customer and we create a supplier object
    'lang'=>'suppliers',
    'buttonnew'=>'AddSupplierInvoice',
    'testnew'=>$user->rights->fournisseur->facture->creer,
    'test'=>$conf->supplier_invoice->enabled && $user->rights->fournisseur->facture->lire),
'contract'=>array(
    'name'=>"Contracts",
    'title'=>"ListContractAssociatedProject",
    'class'=>'Contrat',
    'table'=>'contrat',
    'datefieldname'=>'date_contrat',
    'urlnew'=>DOL_URL_ROOT.'/contrat/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'contracts',
    'buttonnew'=>'AddContract',
    'testnew'=>$user->rights->contrat->creer,
    'test'=>$conf->contrat->enabled && $user->rights->contrat->lire),
'intervention'=>array(
    'name'=>"Interventions",
    'title'=>"ListFichinterAssociatedProject",
    'class'=>'Fichinter',
    'table'=>'fichinter',
    'datefieldname'=>'date_valid',
    'disableamount'=>0,
    'margin'=>'minus',
    'urlnew'=>DOL_URL_ROOT.'/fichinter/card.php?action=create&origin=project&originid='.$id.'&socid='.$socid,
    'lang'=>'interventions',
    'buttonnew'=>'AddIntervention',
    'testnew'=>$user->rights->ficheinter->creer,
    'test'=>$conf->ficheinter->enabled && $user->rights->ficheinter->lire),
'shipping'=>array(
    'name'=>"Shippings",
    'title'=>"ListShippingAssociatedProject",
    'class'=>'Expedition',
    'table'=>'expedition',
    'datefieldname'=>'date_valid',
    'urlnew'=>DOL_URL_ROOT.'/expedition/card.php?action=create&origin=project&originid='.$id.'&socid='.$socid,
    'lang'=>'sendings',
    'buttonnew'=>'CreateShipment',
    'testnew'=>0,
    'test'=>$conf->expedition->enabled && $user->rights->expedition->lire),
'trip'=>array(
    'name'=>"TripsAndExpenses",
    'title'=>"ListExpenseReportsAssociatedProject",
    'class'=>'Deplacement',
    'table'=>'deplacement',
    'datefieldname'=>'dated',
    'margin'=>'minus',
    'disableamount'=>1,
    'urlnew'=>DOL_URL_ROOT.'/deplacement/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'trips',
    'buttonnew'=>'AddTrip',
    'testnew'=>$user->rights->deplacement->creer,
    'test'=>$conf->deplacement->enabled && $user->rights->deplacement->lire),
'expensereport'=>array(
    'name'=>"ExpenseReports",
    'title'=>"ListExpenseReportsAssociatedProject",
    'class'=>'ExpenseReportLine',
    'table'=>'expensereport_det',
    'datefieldname'=>'date',
    'margin'=>'minus',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/expensereport/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'trips',
    'buttonnew'=>'AddTrip',
    'testnew'=>$user->rights->expensereport->creer,
    'test'=>$conf->expensereport->enabled && $user->rights->expensereport->lire),
'donation'=>array(
    'name'=>"Donation",
    'title'=>"ListDonationsAssociatedProject",
    'class'=>'Don',
    'margin'=>'add',
    'table'=>'don',
    'datefieldname'=>'datedon',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/don/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'donations',
    'buttonnew'=>'AddDonation',
    'testnew'=>$user->rights->don->creer,
    'test'=>$conf->don->enabled && $user->rights->don->lire),
'loan'=>array(
    'name'=>"Loan",
    'title'=>"ListLoanAssociatedProject",
    'class'=>'Loan',
    'margin'=>'add',
    'table'=>'loan',
    'datefieldname'=>'datestart',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/loan/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'loan',
    'buttonnew'=>'AddLoan',
    'testnew'=>$user->rights->loan->write,
    'test'=>$conf->loan->enabled && $user->rights->loan->read),
'chargesociales'=>array(
    'name'=>"SocialContribution",
    'title'=>"ListSocialContributionAssociatedProject",
    'class'=>'ChargeSociales',
    'margin'=>'minus',
    'table'=>'chargesociales',
    'datefieldname'=>'date_ech',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/compta/sociales/card.php?action=create&projectid='.$id,
    'lang'=>'compta',
    'buttonnew'=>'AddSocialContribution',
    'testnew'=>$user->rights->tax->charges->lire,
    'test'=>$conf->tax->enabled && $user->rights->tax->charges->lire),
'project_task'=>array(
    'name'=>"TaskTimeSpent",
    'title'=>"ListTaskTimeUserProject",
    'class'=>'Task',
    'margin'=>'minus',
    'table'=>'projet_task',
    'datefieldname'=>'task_date',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/projet/tasks/time.php?withproject=1&action=createtime&projectid='.$id,
    'buttonnew'=>'AddTimeSpent',
    'testnew'=>$user->rights->projet->creer,
    'test'=>($conf->projet->enabled && $user->rights->projet->lire && empty($conf->global->PROJECT_HIDE_TASKS))),
'stock_mouvement'=>array(
    'name'=>"MouvementStockAssociated",
    'title'=>"ListMouvementStockProject",
    'class'=>'MouvementStock',
    'margin'=>'minus',
    'table'=>'stock_mouvement',
    'datefieldname'=>'datem',
    'disableamount'=>0,
    'test'=>($conf->stock->enabled && $user->rights->stock->mouvement->lire && !empty($conf->global->STOCK_MOVEMENT_INTO_PROJECT_OVERVIEW))),
'salaries'=>array(
    'name'=>"Salaries",
    'title'=>"ListSalariesAssociatedProject",
    'class'=>'PaymentSalary',
    'table'=>'payment_salary',
    'datefieldname'=>'datev',
    'margin'=>'minus',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/salaries/card.php?action=create&projectid='.$id,
    'lang'=>'salaries',
    'buttonnew'=>'AddSalaryPayment',
    'testnew'=>$user->rights->salaries->write,
    'test'=>$conf->salaries->enabled && $user->rights->salaries->read),
'variouspayment'=>array(
    'name'=>"VariousPayments",
    'title'=>"ListVariousPaymentsAssociatedProject",
    'class'=>'PaymentVarious',
    'table'=>'payment_various',
    'datefieldname'=>'datev',
    'margin'=>'minus',
    'disableamount'=>0,
    'urlnew'=>DOL_URL_ROOT.'/compta/bank/various_payment/card.php?action=create&projectid='.$id,
    'lang'=>'banks',
    'buttonnew'=>'AddVariousPayment',
    'testnew'=>$user->rights->banque->modifier,
    'test'=>$conf->banque->enabled && $user->rights->banque->lire && empty($conf->global->BANK_USE_OLD_VARIOUS_PAYMENT)),
/* No need for this, available on dedicated tab "Agenda/Events"
'agenda'=>array(
    'name'=>"Agenda",
    'title'=>"ListActionsAssociatedProject",
    'class'=>'ActionComm',
    'table'=>'actioncomm',
    'datefieldname'=>'datep',
    'disableamount'=>1,
    'urlnew'=>DOL_URL_ROOT.'/comm/action/card.php?action=create&projectid='.$id.'&socid='.$socid,
    'lang'=>'agenda',
    'buttonnew'=>'AddEvent',
    'testnew'=>$user->rights->agenda->myactions->create,
    'test'=>$conf->agenda->enabled && $user->rights->agenda->myactions->read),
*/
);

// Show balance for whole project Order

$langs->loadLangs(array("suppliers", "bills", "orders", "proposals", "margins"));

if (!empty($conf->stock->enabled)) $langs->load('stocks');

print load_fiche_titre($langs->trans("ProfitsOrder"), '', 'title_accountancy');

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td align="left" width="200">'.$langs->trans("Element").'</td>';
print '<td align="right" width="100">'.$langs->trans("Number").'</td>';
print '<td align="right" width="100">'.$langs->trans("AmountHT").'</td>';
print '<td align="right" width="100">'.$langs->trans("AmountTTC").'</td>';
print '</tr>';

foreach ($listofreferent as $key => $value)
{
    $name=$langs->trans($value['name']);
    $title=$value['title'];
    $classname=$value['class'];
    $tablename=$value['table'];
    $datefieldname=$value['datefieldname'];
    $qualified=$value['test'];
    $margin = $value['margin'];
    $project_field = $value['project_field'];
    if ($tablename == "commande" || $tablename == "commande_fournisseur")       // If this element must be included into profit calculation ($margin is 'minus' or 'plus' or 'auto')
    {
        $element = new $classname($db);

        $elementarray = $object->get_element_list($key, $tablename, $datefieldname, $dates, $datee, !empty($project_field)?$project_field:'fk_projet');

        if (count($elementarray)>0 && is_array($elementarray))
        {
            $total_ht_order = 0;
            $total_ttc_order = 0;

            $num=count($elementarray);
            for ($i = 0; $i < $num; $i++)
            {
                $tmp=explode('_',$elementarray[$i]);
                $idofelement=$tmp[0];
                $idofelementuser=$tmp[1];

                $element->fetch($idofelement);
                if ($idofelementuser) $elementuser->fetch($idofelementuser);

                // Define if record must be used for total or not
                $qualifiedfortotal=true;

                if ($tablename != 'expensereport_det' && method_exists($element, 'fetch_thirdparty')) $element->fetch_thirdparty();

                // Define $total_ht_by_line
                if ($tablename == 'don' || $tablename == 'chargesociales' || $tablename == 'payment_various' || $tablename == 'payment_salary') $total_ht_by_line_order=$element->amount;
                elseif ($tablename == 'fichinter') $total_ht_by_line_order=$element->getAmount();
                elseif ($tablename == 'stock_mouvement') $total_ht_by_line_order=$element->price*abs($element->qty);
                elseif ($tablename == 'projet_task')
                {
                    if ($idofelementuser)
                    {
                        $tmp = $element->getSumOfAmount($elementuser, $dates, $datee);
                        $total_ht_by_line_order = price2num($tmp['amount'],'MT');
                    }
                    else
                    {
                        $tmp = $element->getSumOfAmount('', $dates, $datee);
                        $total_ht_by_line_order = price2num($tmp['amount'],'MT');
                    }
                }
                else $total_ht_by_line_order=$element->total_ht;

                // Define $total_ttc_by_line
                if ($tablename == 'don' || $tablename == 'chargesociales' || $tablename == 'payment_various' || $tablename == 'payment_salary') $total_ttc_by_line_order=$element->amount;
                elseif ($tablename == 'fichinter') $total_ttc_by_line_order=$element->getAmount();
                elseif ($tablename == 'stock_mouvement') $total_ttc_by_line_order=$element->price*abs($element->qty);
                elseif ($tablename == 'projet_task')
                {
                    $defaultvat = get_default_tva($mysoc, $mysoc);
                    $total_ttc_by_line_order = price2num($total_ht_by_line_order * (1 + ($defaultvat / 100)),'MT');
                }
                else $total_ttc_by_line_order=$element->total_ttc;

                // Change sign of $total_ht_by_line and $total_ttc_by_line for some cases
                if ($tablename == 'payment_various')
                {
                    if ($element->sens == 1)
                    {
                        $total_ht_by_line_order = -$total_ht_by_line_order;
                        $total_ttc_by_line_order = -$total_ttc_by_line_order;
                    }
                }

                if ($tablename == 'commande')
                {
                        $total_ht_by_line_order = -$total_ht_by_line_order;
                        $total_ttc_by_line_order = -$total_ttc_by_line_order;
                }

                // Add total if we have to
                if ($qualifiedfortotal)
                {
                    $total_ht_order = $total_ht_order + $total_ht_by_line_order;
                    $total_ttc_order = $total_ttc_order + $total_ttc_by_line_order;
                }
            }

            // Each element with at least one line is output
            $qualifiedforfinalprofit=true;
            if ($key == 'intervention' && empty($conf->global->PROJECT_INCLUDE_INTERVENTION_AMOUNT_IN_PROFIT)) $qualifiedforfinalprofit=false;
            //var_dump($key);

            // Calculate margin
            if ($qualifiedforfinalprofit)
            {
                if ($margin != "add")
                {
                    $total_ht_order = -$total_ht_order;
                    $total_ttc_order = -$total_ttc_order;
                }

                $balance_order_ht += $total_ht_order;
                $balance_order_ttc += $total_ttc_order;
            }

            print '<tr class="oddeven">';
            // Module
            print '<td align="left">'.$name.'</td>';
            // Nb
            print '<td align="right">'.$i.'</td>';
            // Amount HT
            print '<td align="right">';
            if (! $qualifiedforfinalprofit) print '<span class="opacitymedium">'.$form->textwithpicto($langs->trans("NA"), $langs->trans("AmountOfInteventionNotIncludedByDefault")).'</span>';
            else print price($total_ht_order);
            print '</td>';
            // Amount TTC
            print '<td align="right">';
            if (! $qualifiedforfinalprofit) print '<span class="opacitymedium">'.$form->textwithpicto($langs->trans("NA"), $langs->trans("AmountOfInteventionNotIncludedByDefault")).'</span>';
            else print price($total_ttc_order);
            print '</td>';
            print '</tr>';
        }
    }
}
// and the final balance
print '<tr class="liste_total">';
print '<td align="right" colspan=2 >'.$langs->trans("Profit").'</td>';
print '<td align="right" >'.price(price2num($balance_order_ht, 'MT')).'</td>';
print '<td align="right" >'.price(price2num($balance_order_ttc, 'MT')).'</td>';
print '</tr>';

print "</table>";

?>