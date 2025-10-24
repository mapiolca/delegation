<?php

/*
 * Copyright (C) 2020      Pierre Ardoin          <mapiolca@me.com>
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

/**	    \file       htdocs/delegation/tpl/rating.default.tpl.php
 *		\ingroup    delegation
 *		\brief      Delegation module default view
 */

echo ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');


?>

<?php echo $formconfirm ? $formconfirm : ''; 

    $linkback = '<a href="'.DOL_URL_ROOT.'/product/list.php?type='.$object->type.'">'.$langs->trans("BackToList").'</a>';
    $object->next_prev_filter=" fk_product_type = ".$object->type;

    dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');



    print '<div class="fichecenter">';
        print '<div class="fichehalfleft">';
        print '<div class="underbanner clearboth"></div>';
            print '<table class="border tableforfield" width="100%">';

                // Type
            if (! empty($conf->produit->enabled) && ! empty($conf->service->enabled))
            {
                // TODO change for compatibility with edit in place
                $typeformat='select;0:'.$langs->trans("Product").',1:'.$langs->trans("Service");
                print '<tr>';
                    print '<td class="titlefield">'.$form->editfieldkey("Type",'fk_product_type',$object->type,$object,$user->rights->produit->creer||$user->rights->service->creer,$typeformat).'</td>';
                    print '<td colspan="2">';
                        print $form->editfieldval("Type",'fk_product_type',$object->type,$object,$user->rights->produit->creer||$user->rights->service->creer,$typeformat);
                    print '</td>';
                print '</tr>';
            }

                // Description
                print '<tr>';
                    print '<td class="tdtop">'.$langs->trans("Description").'</td>';
                    print '<td colspan="2">'.(dol_textishtml($object->description)?$object->description:dol_nl2br($object->description,1,true)).'</td>';
                print '</tr>';

            print '</table>';
        print '</div>';

        print '<div class="fichehalfright">';
        print '<div class="underbanner clearboth"></div>';
            print '<table class="border tableforfield" width="100%">';
                print '<tr>';
                    print '<td>';
                        print $langs->trans('ProductRating');
                    print '</td>';
                    print '<td>';
                        print $rating->product->display();
                        print $htmllogobar; $htmllogobar='';

                    $pageyes = "".dol_buildpath("/delegation/ajax/post.php",2)."?action=add_rating&confirm=yes&";
            
                    print '<script type="text/javascript">';

                    print '
                        jQuery(document).ready(function () {
                            $(function () {
                                $("#dialog-rating").dialog({
                                    autoOpen: false,
                                    open: function () {
                                        $(this).parent().find("button.ui-button:eq(1)").focus();
                                    },
                                    resizable: false,
                                    height: "250",
                                    width: "330",
                                    modal: true,
                                    closeOnEscape: false,
                                    buttons: {
                                        "'.$langs->trans("SubmitRating").'": function () {
                                            var options = $("#dialog-rating form").serialize();
                                            var pageyes = "'.$pageyes.'";
                                            var urljump = pageyes + options;
                                            //alert(urljump);
                                            if (pageyes.length > 0) {
                                                location.href = urljump;
                                            }
                                            $(this).dialog("close");
                                        },
                                        "'.$langs->trans("Cancel").'": function () {
                                            $(this).dialog("close");
                                        }
                                    }
                                });

                                $(".rating-add").click(function () {
                                    $("#dialog-rating").dialog({title: $(this).attr("title")});
                                    $("#dialog-rating input#fk_element").val($(this).attr("fk_element"));
                                    $("#dialog-rating input#elementtype").val($(this).attr("elementtype"));
                                    $("#dialog-rating input#elementrated").val($(this).attr("elementrated"));
                                    $("#dialog-rating").dialog("open");
                                    return false; // prevent default action of links
                                });
                            });
                        });
                    </script>';
                    get_rating_form();
                    print '</td>';
                    
                print '</tr>';
                print '<tr>';
                    print '<td>';
                    print '</td>';
                    print '<td>';
                    print '</td>';
                print '</tr>';
            print '</table>';
        print '</div>';
    print '</div>';
    print '<div class="underbanner clearboth"></div>';


    dol_fiche_end();

    $resql = $rating->product->collect_rate();

    $num = $rating->db->num_rows($resql);   

    print_barre_liste($langs->trans("LMDB_RatingList"), $page, $_SERVER["PHP_SELF"], "&amp;id=$object->id", $sortfield, $sortorder, '', 0, $num, '');


    $e = 0;
    print '<div class="div-table-responsive">';
        print '<table class="noborder" width="100%">';
            print '<thead>';
                print '<tr class="liste_titre">';
                    print '<th class="liste_titre">'.$langs->trans('ProductRating').'</th>';
                    print '<th class="liste_titre">'.$langs->trans('LMDB_Comment').'</th>';
                    print '<th class="liste_titre">'.$langs->trans('User').'</th>';
                    print '<th class="liste_titre">'.$langs->trans('Company').'</th>';
                    print '<th class="liste_titre">'.$langs->trans('LMDB_Datec').'</th>';
                    print '<th class="liste_titre" width="50">&nbsp;</th>';
                print '</tr>';
            print '</thead>';
            print '<tbody>';
        while ($e < $num) {
            $rate = $rating->db->fetch_object($resql);
            $moy  += $rate->rate;

            if ($rate->rate < 0) {
                $negative = '-negative';
            } else {
                $negative = '';
            }

            if ($action == 'editline' && $lineid == $rate->rowid){
                
                print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="POST">';
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
                print '<input type="hidden" name="action" value="updateline" />';
                print '<input type="hidden" name="id" value="'.$object->id.'" />';
                print '<input type="hidden" name="lineid" value="'.$rate->rowid.'" />';

            }

            print '<tr>';
                print '<td>';

                if ($action == 'editline' && $lineid == $rate->rowid ){
                    print '<div id="edit-rat">';
                    for ($i = 0; $i < 6; $i++) {
                        if ($i == $rate->rate) {
                                $checked = 'checked';
                            }else{
                                $checked = '';
                            }
                        switch (true) {

                            case $i == 0:
                                print '<input id="edit_rat'.$i.'" class="edit-rating-star-neutral" style="display:none;" type="radio" value="'.$i.'" name="edit_rating" '.$checked.'/>';
                                print '<label for="edit_rat'.$i.'" class="edit-rating-star-neutral">'.$i;print '</label>';
                                break;
                            case $i < 0:
                                print '<input id="edit_rat'.$i.'" class="edit-rating-star-negative" style="display:none;" type="radio" value="'.$i.'" name="edit_rating" '.$checked.'/>';
                                print '<label for="edit_rat'.$i.'" class="edit-rating-star-negative">'.$i;print '</label>';
                                break;
                            case $i > 0:
                                print '<input id="edit_rat'.$i.'" class="edit-rating-star-positive" style="display:none;" type="radio" value="'.$i.'" name="edit_rating" '.$checked.'/>';
                                print '<label for="edit_rat'.$i.'" class="edit-rating-star-positive">'.$i;print '</label>';
                                break;
                        }
                        
                        
                    }
                    print '</div>';

                    print '<input type="hidden" id="fk_element" name="fk_element" value="'.$rate->fk_element.'" />';
                    print '<input type="hidden" id="elementtype" name="elementtype" value="'.$rate->elementtype.'" />';
                    print '<input type="hidden" id="elementrated" name="elementrated" value="'.$rate->elementrated.'" />';

                }else{
                    print '<div class="star-ratings-sprite">';
                        print '<div style="width:'.abs(20 * $rate->rate).'%" class="star-ratings-sprite-rating'.$negative.'"></div>';
                    print '</div>';
                }
                    
                print '</td>';
                if ($action == 'editline' && $lineid == $rate->rowid){
                    print '<td><textarea class = "minwidth300" name="comment" class="comment">';
                        print $rate->comment;
                    print '</textarea></td>';
                    
                }else{
                    print ' <td>'.$rate->comment.'</td>';
                }
                
                print '<td class="maxwidth100">'.$rate->firstname.' '.$rate->lastname.'</td>';
                print '<td class="maxwidth100">'.$rate->name_entity.'</td>';
                print '<td class="maxwidth100">'.dol_print_date($rate->datec).'</td>';

                if ($action == 'editline' && $lineid == $rate->rowid){
                    print '<td align="right">';
                    print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'"/>&nbsp;<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"';
                    print '</td>';

                }else{
                    print '<td align="right">';
                    if ($canAddLines && $rate->user == $user->rowid) {    
                        print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$rate->rowid.'">'.img_edit().'
                        </a>';
                    }
                    if ($canDeleteLines && $rate->user == $user->rowid) {
                        print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deleteline&amp;lineid='.$rate->rowid.'">'.img_delete().'
                        </a>';
                    }
                    print '</td>';
                }
            print '</tr>';

            if ($action == 'editline' && $lineid == $rate->rowid){

                print '</form>';
                
            }

            $e++;
        }
            print '</tbody>';
        print '</table>';
    print '</div>';
    
?>



<?php // End of page
llxFooter();
$db->close(); ?>

