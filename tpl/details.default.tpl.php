<?php

/* Copyright (C) 2019-2020      Pierre Ardoin        <mapiolca@me.com>
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

/**	    \file       htdocs/delegation/tpl/details.default.tpl.php
 *		\ingroup    delegation
 *		\brief      Delegation module default view
 */

?>

<table id="tablelines" class="noborder" width="100%">
<?php if ($numLines > 0){ ?>
    <tr class="liste_titre nodrag nodrop">
        <td><?php print $langs->trans('Label'); ?></td>
        <td width="350"><?php print $langs->trans('Value'); ?></td>
        <td width="">&nbsp;</td>
    </tr>
    <?php
    for($i = 0; $i < $numLines; $i++){
        $line_details = $details->lines[$i];

        if ($action == 'editline' && $lineid == $line_details->rowid){ ?>

        <form name="details" action="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
        <input type="hidden" name="token" value="<?php  print $_SESSION['newtoken']; ?>" />
        <input type="hidden" name="action" value="updateline" />
        <input type="hidden" name="id" value="<?php print $object->id; ?>" />
        <input type="hidden" name="lineid" value="<?php print $line_details->rowid; ?>"/>
        <input type="hidden" name="field" value="<?php print $field ?>"/>

         <?php } ?>

        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>       
                <?php print $langs->trans('PROJECTDETAILS_type_mou').' '.img_picto($langs->trans('PROJECTDETAILS_type_mou_Tooltip'), 'info', 'style="cursor:help"'); ?>
            </td>
            <td>
            <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "type_mou"){ ?>
                <select id="type_mou" name='type_mou' value='<?php print $line->type_mou; ?>'>
                    <option value="1" <?php if ($line_details->type_mou == 1) { print "selected" ; } ?>><?php print $langs->trans('public'); ?></option> 
                    <option value="2" <?php if ($line_details->type_mou == 2) { print "selected" ; } ?>><?php print $langs->trans('private'); ?></option>
                </select>
            <?php }else if ($line_details->type_mou == "0") {
                print $langs->trans('non_renseigne');?>
            <?php }else if ($line_details->type_mou == "1") {
                print $langs->trans('public');?>
            <?php }else if ($line_details->type_mou == "2") {
                print $langs->trans('private'); ?>
            <?php }?>
            </td>
            <td align="right">
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "type_mou"){ ?>
                    <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                <?php }else{
                    if ($canAddLines) { ?>     
                        <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=type_mou'; ?>"><?php print img_edit();?></a>
                <?php }}?>
            </td>
        </tr>

        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>  
                <?php print $langs->trans('PROJECTDETAILS_refchantier').' '.img_picto($langs->trans('PROJECTDETAILS_refchantier_tooltip'), 'info', 'style="cursor:help"'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "ref_chantier" ){ ?>
                    <textarea type="text" size="8" id="ref_chantier" name="ref_chantier" ><?php print $line_details->ref_chantier; ?></textarea>
                <?php }elseif ($line_details->ref_chantier == "") {
                   print $langs->trans('non_renseigne');
                }else{ 
                    print $line_details->ref_chantier ;
                } ?>
            </td>
            <td align="right">
        <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "ref_chantier"){ ?>
            <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
        <?php }else{ 
            if ($canAddLines) { ?>       
                <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=ref_chantier'; ?>">
                    <?php print img_edit(); ?>
                </a>
            <?php }}?>
            </td>
        </tr>

        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php print "".$langs->trans('PROJECTDETAILS_adresse_chantier'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "adresse_chantier" ){ ?>
                    <textarea type="text" size="8" id="adresse_chantier" name="adresse_chantier" ><?php print $line_details->adresse_chantier; ?></textarea>
                <?php }elseif ($line_details->adresse_chantier == "") {
                   print $langs->trans('non_renseigne');
                }else{ 
                    print $line_details->adresse_chantier ; 
                } ?>
            </td>
            <td align="right">
            <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "adresse_chantier"){ ?>
                    <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
            <?php }else{ 
                if ($canAddLines) { ?>       
                    <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=adresse_chantier'; ?>">
                        <?php print img_edit(); ?>
                    </a>
                <?php } } ?>
            </td>
        </tr>

        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php print $langs->trans('PROJECTDETAILS_nature_travaux').' '.img_picto($langs->trans('PROJECTDETAILS_nature_travaux_tooltip'), 'info', 'style="cursor:help"'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "nature_travaux" ){ ?>
                    <textarea type="text" size="8" id="nature_travaux" name="nature_travaux" ><?php print $line_details->nature_travaux; ?></textarea>
                <?php }elseif ($line_details->nature_travaux == "") {
                   print $langs->trans('non_renseigne');
                }else{ 
                    print $line_details->nature_travaux ;
                } ?>
            </td>
            <td align="right">
        <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "nature_travaux"){ ?>
            <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
        <?php }else{ 
            if ($canAddLines) { ?>       
                <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=nature_travaux'; ?>">
                    <?php print img_edit(); ?>
                </a>
            <?php }}?>
            </td>
        </tr>
        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php print $langs->trans('PROJECTDETAILS_fk_moe').' '.img_picto($langs->trans('PROJECTDETAILS_fk_moe_Tooltip'), 'info', 'style="cursor:help"'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "fk_moe" ){ 
                    if (! empty($conf->global->PROJECT_FILTER_FOR_THIRDPARTY_LIST)) $filteronlist=$conf->global->PROJECT_FILTER_FOR_THIRDPARTY_LIST;
                    $text=$form->select_company(GETPOST('socid', 'int'), 'fk_moe', $filteronlist, 'SelectThirdParty', 1, 0, array(), 0, 'minwidth300');
                    if (empty($conf->global->PROJECT_CAN_ALWAYS_LINK_TO_ALL_SUPPLIERS) && empty($conf->dol_use_jmobile))
                    {
                        $texthelp=$langs->trans("IfNeedToUseOtherObjectKeepEmpty");
                        print $form->textwithtooltip($text.' '.img_help(), $texthelp, 1);
                    }
                    else print $text;
                    ?>
                <?php }elseif ($line_details->fk_moe == "") {
                    print $langs->trans('non_renseigne');
                }else{ 

                    $soc->fetch($line_details->fk_moe) ;
                    print $soc->getNomUrl(1) ;

                } ?>
            </td>
            <td align="right">
        <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "fk_moe"){ ?>
            <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
        <?php }else{ 
            if ($canAddLines) { ?>       
                <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=fk_moe'; ?>">
                    <?php print img_edit(); ?>
                </a>
            <?php }}?>
            </td>
        </tr>
        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php print "".$langs->trans('PROJECTDETAILS_n_lot'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "n_lot" ){ ?>
                    <input type="text" id="n_lot" name="n_lot" value="<?php print $line_details->n_lot; ?>">
                <?php }elseif ($line_details->n_lot == "") {
                   print $langs->trans('non_renseigne');
                }else{ 
                    print $line_details->n_lot ; 
                } ?>
            </td>
            <td align="right">
            <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "n_lot"){ ?>
                    <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
            <?php }else{ 
                if ($canAddLines) { ?>       
                    <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=n_lot'; ?>">
                        <?php print img_edit(); ?>
                    </a>
                <?php } } ?>
            </td>
        </tr>
        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php print "".$langs->trans('PROJECTDETAILS_libelle_lot'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "libelle_lot" ){ ?>
                    <input type="text" size="8" id="libelle_lot" name="libelle_lot" value="<?php print $line_details->libelle_lot; ?>">
                <?php }elseif ($line_details->libelle_lot == "") {
                   print $langs->trans('non_renseigne');
                }else{ 
                    print $line_details->libelle_lot ; 
                } ?>
            </td>
            <td align="right">
            <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "libelle_lot"){ ?>
                    <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
            <?php }else{ 
                if ($canAddLines) { ?>       
                    <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=libelle_lot'; ?>">
                        <?php print img_edit(); ?>
                    </a>
                <?php } } ?>
            </td>
        </tr>
        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>       
                <?php print $langs->trans('PROJECTDETAILS_marche_defense').' '.img_picto($langs->trans('PROJECTDETAILS_marche_defense_Tooltip'), 'info', 'style="cursor:help"'); ?>                
            </td>
            <td>
            <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "marche_defense"){ ?>
                <select id="marche_defense" name="marche_defense" value="<?php print $line->marche_defense; ?>">
                    <option value="1" <?php if ($line_details->marche_defense == 1) { print "selected" ; } ?>><?php print $langs->trans('yes'); ?></option> 
                    <option value="2" <?php if ($line_details->marche_defense == 2) { print "selected" ; } ?>><?php print $langs->trans('no'); ?></option>
                </select>
            <?php }else if ($line_details->marche_defense == "0") {
                print $langs->trans('non_renseigne');?>
            <?php }else if ($line_details->marche_defense == "1") {
                print $langs->trans('Yes');?>
            <?php }else if ($line_details->marche_defense == "2") {
                print $langs->trans('No'); ?>
            <?php }?>
            </td>
            <td align="right">
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "marche_defense"){ ?>
                    <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                <?php }else{
                    if ($canAddLines) { ?>     
                        <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=marche_defense'; ?>"><?php print img_edit();?></a>
                <?php }}?>
            </td>
        </tr>
        <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php print "".$langs->trans('PROJECTDETAILS_rg_sstt').' '.img_picto($langs->trans('PROJECTDETAILS_rg_sstt_Tooltip'), 'info', 'style="cursor:help"'); ?>
            </td>
            <td>
                <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "rg_sstt" ){ ?>
                    <input type="text" size="8" id="rg_sstt" name="rg_sstt" value="<?php print $line_details->rg_sstt; ?>">
                <?php }elseif ($line_details->rg_sstt <= 'O') {
                   print $langs->trans('none');
                }else{ 
                    print $line_details->rg_sstt ; 
                } ?>
            </td>
            <td align="right">
            <?php if ($action == 'editline' && $lineid == $line_details->rowid && $field == "rg_sstt"){ ?>
                    <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
            <?php }else{ 
                if ($canAddLines) { ?>       
                    <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_details->rowid.'&amp;field=rg_sstt'; ?>">
                        <?php print img_edit(); ?>
                    </a>
                <?php } } ?>
            </td>
        </tr>
    <?php } ?>
    </form>
<?php } ?>

</table>

<?php 

// End of page
llxFooter();

?>
