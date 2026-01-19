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
 * GNU General Public License for more dc4.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**	    \file       htdocs/delegation/tpl/dc4.default.tpl.php
 *		\ingroup    delegation
 *		\brief      Delegation module default view
 */
/**

/*
 *  View
 */

?>

<?php

    if ($object->element == 'order_supplier') {
        print load_fiche_titre($langs->trans("dc4"), '', '');

    if (empty($object->date_commande)) {
        print '<div class="warning">'.$langs->trans("dc4_errordatemessage").'</div>';
    }
        
    }elseif ($object->element == 'commande') {
        print load_fiche_titre($langs->trans("dc4_only"), '', ''); 
    }

?>

<div class="div-table-responsive">
<table id="tablelines" class="noborder centpercent">
<?php if ($numLines > 0){ ?>
    <tr class="liste_titre nodrag nodrop">
        <td><?php print $langs->trans('Label'); ?></td>
        <td width="350"><?php print $langs->trans('Value'); ?></td>
        <td width="">&nbsp;</td>
    </tr>
    <?php
    for($i = 0; $i < $numLines; $i++){
        $line_dc4 = $dc4->lines[$i];

        if ($action == 'editline' && $lineid == $line_dc4->rowid){ ?>

        <form name="dc4" action="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
            <input type="hidden" name="token" value="<?php  print newToken(); ?>" />
            <input type="hidden" name="action" value="updateline" />
            <input type="hidden" name="id" value="<?php print $object->id; ?>" />
            <input type="hidden" name="lineid" value="<?php print $line_dc4->rowid; ?>"/>
            <input type="hidden" name="field" value="<?php print $field ?>"/>

        <?php } ?>

            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_dc4_object_declaration'); ?>
                    <img src="/theme/md/img/info.png" alt="" title='<div class=&quot;centpercent&quot;><?php print $langs->trans("DC4_dc4_object_declaration_Tooltip"); ?>' class="paddingright classfortooltip valigntextbottom">    
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_object_declaration"){ ?>
                    <select id="dc4_object_declaration" name='dc4_object_declaration' value='<?php print $line->dc4_object_declaration; ?>'>
                        <option value="1" <?php if ($line_dc4->dc4_object_declaration == 1) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_object_declaration_1'); ?></option> 
                        <option value="2" <?php if ($line_dc4->dc4_object_declaration == 2) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_object_declaration_2'); ?></option>
                        <option value="3" <?php if ($line_dc4->dc4_object_declaration == 3) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_object_declaration_3'); ?></option>
                    </select>
                <?php }else if ($line_dc4->dc4_object_declaration == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->dc4_object_declaration == "1") {
                    print $langs->trans('DC4_dc4_object_declaration_1');?>
                <?php }else if ($line_dc4->dc4_object_declaration == "2") {
                    print $langs->trans('DC4_dc4_object_declaration_2'); ?>
                <?php }else if ($line_dc4->dc4_object_declaration == "3") {
                    print $langs->trans('DC4_dc4_object_declaration_3'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_object_declaration"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=dc4_object_declaration'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>
            <?php if ($line_dc4->dc4_object_declaration == 3) { ?>
               
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo $langs->trans('DC4_dc4_date_initiale'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_dc4_date_initiale_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom"> 
                </td>
               <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_date_initiale" ){ ?>
    
                        <?php  print $form->select_date($line_dc4->dc4_date_initiale,'dc4_date_initiale',0,0,0,"dc4"); ?>

                    <?php }elseif ($line_dc4->dc4_date_initiale == "1970-01-01") {
                        
                        echo $langs->trans('non_renseigne')." " ;
                    }else{ 
                       echo dol_print_date($line_dc4->dc4_date_initiale);
                    } ?>
                </td>
            <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_date_initiale"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=dc4_date_initiale'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
                <?php } ?>
            </tr>
        <?php }?>
        <?php if ($line_dc4->dc4_object_declaration == 2) { ?>
            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_dc4_hypothese_01'); ?>
                    <img src="/theme/md/img/info.png" alt="" title='<div class=&quot;centpercent&quot;><?php print $langs->trans("DC4_dc4_hypothese_Tooltip_01"); ?>' class="paddingright classfortooltip valigntextbottom">    
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_hypothese"){ ?>
                    <select id="dc4_hypothese" name='dc4_hypothese' value='<?php print $line->dc4_hypothese; ?>'>
                        <option value="1" <?php if ($line_dc4->dc4_hypothese == 1) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_hypothese_1'); ?></option> 
                        <option value="2" <?php if ($line_dc4->dc4_hypothese == 2) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_hypothese_2'); ?></option>
                    </select>
                <?php }else if ($line_dc4->dc4_hypothese == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->dc4_hypothese == "1") {
                    print $langs->trans('DC4_dc4_hypothese_1');?>
                <?php }else if ($line_dc4->dc4_hypothese == "2") {
                    print $langs->trans('DC4_dc4_hypothese_2'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_hypothese"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=dc4_hypothese'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>
        <?php }?>
        <?php if ($line_dc4->dc4_object_declaration == 3) { ?>
            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_dc4_hypothese_02'); ?>
                    <img src="/theme/md/img/info.png" alt="" title='<div class=&quot;centpercent&quot;><?php print $langs->trans("DC4_dc4_hypothese_Tooltip_02"); ?>' class="paddingright classfortooltip valigntextbottom">    
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_hypothese"){ ?>
                    <select id="dc4_hypothese" name='dc4_hypothese' value='<?php print $line->dc4_hypothese; ?>'>
                        <option value="3" <?php if ($line_dc4->dc4_hypothese == 3) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_hypothese_3'); ?></option> 
                        <option value="4" <?php if ($line_dc4->dc4_hypothese == 4) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_hypothese_4'); ?></option>
                    </select>
                <?php }else if ($line_dc4->dc4_hypothese == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->dc4_hypothese == "3") {
                    print $langs->trans('DC4_dc4_hypothese_3');?>
                <?php }else if ($line_dc4->dc4_hypothese == "4") {
                    print $langs->trans('DC4_dc4_hypothese_4'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_hypothese"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=dc4_hypothese'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>
        <?php }?>



            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_avance'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_avance_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "avance"){ ?>
                    <select id="avance" name='avance' value='<?php print $line->avance; ?>'>
                        <option value="1" <?php if ($line_dc4->avance == 1) { print "selected" ; } ?>><?php print $langs->trans('Yes'); ?></option> 
                        <option value="2" <?php if ($line_dc4->avance == 2) { print "selected" ; } ?>><?php print $langs->trans('No'); ?></option>
                    </select>
                <?php }else if ($line_dc4->avance == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->avance == "1") {
                    print $langs->trans('Yes');?>
                <?php }else if ($line_dc4->avance == "2") {
                    print $langs->trans('No'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "avance"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=avance'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>
            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_dc4_documents_fournis'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_dc4_documents_fournis_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_documents_fournis"){ ?>
                    <select id="dc4_documents_fournis" name='dc4_documents_fournis' value='<?php print $line->dc4_documents_fournis; ?>'>
                        <option value="1" <?php if ($line_dc4->dc4_documents_fournis == 1) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_documents_fournis_1'); ?></option> 
                        <option value="2" <?php if ($line_dc4->dc4_documents_fournis == 2) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_documents_fournis_2'); ?></option>
                        <option value="3" <?php if ($line_dc4->dc4_documents_fournis == 3) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_documents_fournis_3'); ?></option>
                        <option value="4" <?php if ($line_dc4->dc4_documents_fournis == 4) { print "selected" ; } ?>><?php print $langs->trans('DC4_dc4_documents_fournis_4'); ?></option>
                    </select>
                <?php }else if ($line_dc4->dc4_documents_fournis == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->dc4_documents_fournis == "1") {
                    print $langs->trans('DC4_dc4_documents_fournis_1');?>
                <?php }else if ($line_dc4->dc4_documents_fournis == "2") {
                    print $langs->trans('DC4_dc4_documents_fournis_2'); ?>
                <?php }else if ($line_dc4->dc4_documents_fournis == "3") {
                    print $langs->trans('DC4_dc4_documents_fournis_3');?>
                <?php }else if ($line_dc4->dc4_documents_fournis == "4") {
                    print $langs->trans('DC4_dc4_documents_fournis_4'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "dc4_documents_fournis"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=dc4_documents_fournis'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>
            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_paiement_direct'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_paiement_direct_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "paiement_direct"){ ?>
                    <select id="paiement_direct" name='paiement_direct' value='<?php print $line->paiement_direct; ?>'>
                        <option value="1" <?php if ($line_dc4->paiement_direct == 1) { print "selected" ; } ?>><?php print $langs->trans('Yes'); ?></option> 
                        <option value="2" <?php if ($line_dc4->paiement_direct == 2) { print "selected" ; } ?>><?php print $langs->trans('No'); ?></option>
                    </select>
                <?php }else if ($line_dc4->paiement_direct == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->paiement_direct == "1") {
                    print $langs->trans('Yes');?>
                <?php }else if ($line_dc4->paiement_direct == "2") {
                    print $langs->trans('No'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "paiement_direct"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=paiement_direct'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>

            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php print "".$langs->trans('DC4_libelle_poste_cctp'); ?>
                </td>
                <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "libelle_poste_cctp" ){ ?>
                        <textarea type="text" size="8" id="libelle_poste_cctp" name="libelle_poste_cctp" ><?php print $line_dc4->libelle_poste_cctp; ?></textarea>
                    <?php }elseif ($line_dc4->libelle_poste_cctp == "") {
                       print $langs->trans('non_renseigne');
                    }else{ 
                        print $line_dc4->libelle_poste_cctp ; 
                    } ?>
                </td>
                <td align="right">
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "libelle_poste_cctp"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                <?php }else{ 
                    if ($canAddLines) { ?>       
                        <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=libelle_poste_cctp'; ?>">
                            <?php print img_edit(); ?>
                        </a>
                    <?php } } ?>
                </td>
            </tr>

            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_sps_travaux'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_sps_travaux_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "sps_travaux"){ ?>
                    <select id="sps_travaux" name='sps_travaux' value='<?php print $line->sps_travaux; ?>'>
                        <option value="1" <?php if ($line_dc4->sps_travaux == 1) { print "selected" ; } ?>><?php print $langs->trans('Yes'); ?></option> 
                        <option value="2" <?php if ($line_dc4->sps_travaux == 2) { print "selected" ; } ?>><?php print $langs->trans('No'); ?></option>
                    </select>
                <?php }else if ($line_dc4->sps_travaux == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->sps_travaux == "1") {
                    print $langs->trans('Yes');?>
                <?php }else if ($line_dc4->sps_travaux == "2") {
                    print $langs->trans('No'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "sps_travaux"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=sps_travaux'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>
            <?php if ($line_dc4->sps_travaux == 1) { ?>
               
            <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td> 
                    <?php echo $langs->trans('DC4_sps_date_remise'); ?>
                    <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_sps_date_remise_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom"> 
                </td>
               <td>
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "sps_date_remise" ){ ?>
    
                        <?php  print $form->select_date($line_dc4->sps_date_remise,'sps_date_remise',0,0,0,"dc4"); ?>

                    <?php }elseif ($line_dc4->sps_date_remise == "1970-01-01") {
                        
                        echo $langs->trans('non_renseigne')." " ;
                    }else{ 
                       echo dol_print_date($line_dc4->sps_date_remise);
                    } ?>
                </td>
            <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "sps_date_remise"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;
                    <input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=sps_date_remise'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
                <?php } ?>
            </tr>
            <?php }?>

            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_cissct'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_cissct_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "cissct"){ ?>
                    <select id="cissct" name='cissct' value='<?php print $line->cissct; ?>'>
                        <option value="1" <?php if ($line_dc4->cissct == 1) { print "selected" ; } ?>><?php print $langs->trans('Yes'); ?></option> 
                        <option value="2" <?php if ($line_dc4->cissct == 2) { print "selected" ; } ?>><?php print $langs->trans('No'); ?></option>
                    </select>
                <?php }else if ($line_dc4->cissct == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->cissct == "1") {
                    print $langs->trans('Yes');?>
                <?php }else if ($line_dc4->cissct == "2") {
                    print $langs->trans('No'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "cissct"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=cissct'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>

            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_DIUO'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_DIUO_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "DIUO"){ ?>
                    <select id="DIUO" name='DIUO' value='<?php print $line->DIUO; ?>'>
                        <option value="1" <?php if ($line_dc4->DIUO == 1) { print "selected" ; } ?>><?php print $langs->trans('Yes'); ?></option> 
                        <option value="2" <?php if ($line_dc4->DIUO == 2) { print "selected" ; } ?>><?php print $langs->trans('No'); ?></option>
                    </select>
                <?php }else if ($line_dc4->DIUO == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->DIUO == "1") {
                    print $langs->trans('Yes');?>
                <?php }else if ($line_dc4->DIUO == "2") {
                    print $langs->trans('No'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "DIUO"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=DIUO'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>

            <tr class="<?php print ($i%2==0 ? 'impair' : 'pair'); ?>">
                <td>       
                    <?php print $langs->trans('DC4_responsabilite'); ?><img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC4_responsabilite_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
                </td>
                <td>
                <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "responsabilite"){ ?>
                    <select id="responsabilite" name='responsabilite' value='<?php print $line->responsabilite; ?>'>
                        <option value="1" <?php if ($line_dc4->responsabilite == 1) { print "selected" ; } ?>><?php print $langs->trans('Yes'); ?></option> 
                        <option value="2" <?php if ($line_dc4->responsabilite == 2) { print "selected" ; } ?>><?php print $langs->trans('No'); ?></option>
                    </select>
                <?php }else if ($line_dc4->responsabilite == NULL) {
                    print $langs->trans('non_renseigne');?>
                <?php }else if ($line_dc4->responsabilite == "1") {
                    print $langs->trans('Yes');?>
                <?php }else if ($line_dc4->responsabilite == "2") {
                    print $langs->trans('No'); ?>
                <?php }?>
                </td>
                <td align="right">
                    <?php if ($action == 'editline' && $lineid == $line_dc4->rowid && $field == "responsabilite"){ ?>
                        <input type="submit" class="button" name="save" value="<?php print $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php print $langs->trans("Cancel"); ?>" />
                    <?php }else{
                        if ($canAddLines) { ?>     
                            <a href="<?php print $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line_dc4->rowid.'&amp;field=responsabilite'; ?>"><?php print img_edit();?></a>
                <?php } } ?>
                </td>
            </tr>

<?php
/* Liste des Variables

`rowid`                         int(11) AUTO_INCREMENT,
`fk_object`                     int(11)  NOT NULL,
`fk_element`                    VARCHAR(255)  NOT NULL,
`dc4_object_declaration`        int(11) NULL,
`dc4_date_initiale`             DATE NOT NULL,   
`avance`                        int(11) NULL,
`dc4_documents_fournis`          int(11) NULL,
`paiement_direct`               int(11) NULL,
`libelle_poste_cctp`            VARCHAR(255)  NOT NULL,
`sps_travaux`                   int(11) NULL,
`sps_date_remise`               DATE NOT NULL,  
`cissct`                        int(11) NULL,
`DIUO`                          int(11) NULL,
`responsabilite`                int(11) NULL,

*/
?>

    <?php } ?>
    </form>
<?php } ?>
</table>
</div>
?>
