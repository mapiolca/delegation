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

/**     \file       htdocs/delegation/tpl/delegation.default.tpl.php
 *      \ingroup    delegation
 *      \brief      Delegation module default view
 */


llxHeader();

echo ($message ? dol_htmloutput_mesg($message, '', ($error ? 'error' : 'ok'), 0) : '');

dol_fiche_head($head, $current_head, $langs->trans('Proposal'), -1, 'propal');

$object->fetch_thirdparty();
?>
  
<?php echo $formconfirm ? $formconfirm : ''; ?>

<table class="border" width="100%">
    <tr>
        <td width="20%"><?php echo $langs->trans('Ref'); ?></td>
        <td><?php echo $object->ref; ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('RefCustomer'); ?></td>
        <td><?php echo $object->ref_client; ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('Company'); ?></td>
        <td><?php echo $soc->getNomUrl(1,'compta'); ?></td>
    </tr>

    <tr>
        <td><?php echo $langs->trans('Date'); ?></td>
        <td><?php echo dol_print_date($object->date,'daytext'); ?></td>
    </tr>    

     <tr>
        <td><?php echo $langs->trans('Status'); ?></td>
        <td align="left" colspan="3"><?php echo $object->getLibStatut(); ?></td>
    </tr>

    <?php if ($conf->projet->enabled){ ?>
      <tr>
        <td><?php echo $langs->trans('Project'); ?></td>
        <td><?php if ($project->id > 0){ echo $project->getNomUrl(1); } ?></td>
    </tr>
   <?php } ?>
</table>
<br />

<table class="border" width="100%">
    <tr>
        <td>
            <?php echo $langs->trans('DC1_Consigne_entete'); ?>
        </td>
    </tr>
</table>

<table id="tablelines" class="noborder" width="100%">
<?php if ($numLines > 0){ ?>
    <tr class="liste_titre nodrag nodrop">
        <td><?php echo $langs->trans('Label'); ?></td>
		<td><?php echo $langs->trans('Value'); ?></td>
		<td width="">&nbsp;</td>
	</tr>
    <?php 
    for($i = 0; $i < $numLines; $i++){
        $line = $dc1->lines[$i]; 

        if ($action == 'editline' && $lineid == $line->rowid){ ?>

        <form action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>" method="POST">
        <input type="hidden" name="token" value="<?php  echo $_SESSION['newtoken']; ?>" />
        <input type="hidden" name="action" value="updateline" />
        <input type="hidden" name="id" value="<?php echo $object->id; ?>" />
        <input type="hidden" name="lineid" value="<?php echo $line->rowid; ?>"/>
        <input type="hidden" name="field" value="<?php echo $field ?>"/>

         <?php } ?>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo $langs->trans('id_acheteur'); ?>

                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_A_Tooltip") ; ?></div>" class="paddingright classfortooltip valigntextbottom">
                
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "id_acheteur"){ ?>
                    <input type="text" size="8" id="id_acheteur" name="id_acheteur" value="<?php echo $line->id_acheteur; ?>" />
                <?php }else{ 
                echo $object->thirdparty->name.'<br>' ;
                echo $object->thirdparty->address.'<br>' ;
                echo $object->thirdparty->zip.'&nbsp;'.$object->thirdparty->town ;

                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "id_acheteur"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <?php /*<a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=id_acheteur'; ?>">
                            <?php echo img_edit(); ?>
                        */ ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr> 

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo $langs->trans('objet_consultation'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_B_Tooltip") ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "objet_consultation" ){ ?>
                    <textarea type="text" size="8" id="objet_consultation" name="objet_consultation" ><?php echo $line->objet_consultation; ?></textarea>
                <?php }else{ 
                echo $line->objet_consultation ; 
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "objet_consultation"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=objet_consultation'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('ref_consultation'); ?>       
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "ref_consultation" ){ ?>
                    <input type="text" size="8" id="ref_consultation" name="ref_consultation" value="<?php echo $line->ref_consultation; ?>" />
                <?php }else{ 
                echo $line->ref_consultation ; 
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "ref_consultation"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=ref_consultation'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "".$langs->trans('objet_candidature'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_C_Tooltip") ?></div>" class="paddingright classfortooltip valigntextbottom">   
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "objet_candidature" ){ ?>
                    
                    <select id="objet_candidature" name="objet_candidature" value="<?php echo $line->objet_candidature; ?>">
                        <option value="1" <?php if ($line->objet_candidature == 1) { echo "selected" ; } ?>><?php echo $langs->trans('marche_public'); ?></option> 
                        <option value="2" <?php if ($line->objet_candidature == 2) { echo "selected" ; } ?>><?php echo $langs->trans('lots_separe'); ?></option>
                        <option value="3" <?php if ($line->objet_candidature == 3) { echo "selected" ; } ?>><?php echo $langs->trans('tous_les_lots'); ?></option>
                    </select>

                <?php }else{ 

                if ($line->objet_candidature == 1) {
                   echo $langs->trans('marche_public') ; 
                }elseif ($line->objet_candidature == 2) {
                    echo $langs->trans('lots_separe') ; 
                }elseif ($line->objet_candidature == 3) {
                    echo $langs->trans('tous_les_lots') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "objet_candidature"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=objet_candidature'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('n_lots'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_n_lots_Tooltip") ?></div>" class="paddingright classfortooltip valigntextbottom">       
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "n_lots" ){ ?>
                    <textarea type="text" size="8" id="n_lots" name="n_lots"><?php echo $line->n_lots; ?></textarea>
                <?php }else{ 
                echo $line->n_lots ; 
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "n_lots"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=n_lots'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('designation_lot') ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_C_Designation_Tooltip") ?>En cas de non numérotation</div>" class="paddingright classfortooltip valigntextbottom">   
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "designation_lot" ){ ?>
                    <textarea type="text" size="8" id="designation_lot" name="designation_lot"><?php echo $line->designation_lot; ?></textarea>
                <?php }else{ 
                echo $line->designation_lot ; 
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "designation_lot"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=designation_lot'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo $langs->trans('candidat_statut'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_D_Tooltip") ?></div>" class="paddingright classfortooltip valigntextbottom">       
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "candidat_statut" ){ ?>

                    <select id="candidat_statut" name="candidat_statut" value="<?php echo $line->candidat_statut; ?>">
                        <option value="1" <?php if ($line->candidat_statut == 1) { echo "selected" ; } ?>><?php echo $langs->trans('candidat_statut1'); ?></option> 
                        <option value="2" <?php if ($line->candidat_statut == 2) { echo "selected" ; } ?>><?php echo $langs->trans('candidat_statut2'); ?></option>
                        <option value="3" <?php if ($line->candidat_statut == 3) { echo "selected" ; } ?>><?php echo $langs->trans('candidat_statut3'); ?></option>
                        <option value="4" <?php if ($line->candidat_statut == 4) { echo "selected" ; } ?>><?php echo $langs->trans('candidat_statut4'); ?></option>
                    </select>

                <?php }else{ 

                if ($line->candidat_statut == 1) {
                   echo $langs->trans('candidat_statut1') ; 
                }elseif ($line->candidat_statut == 2) {
                    echo $langs->trans('candidat_statut2') ; 
                }elseif ($line->candidat_statut == 3) {
                    echo $langs->trans('candidat_statut3') ; 
                }elseif ($line->candidat_statut == 4) {
                    echo $langs->trans('candidat_statut4') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "candidat_statut"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=candidat_statut'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "".$langs->trans('id_membre'); ?>       
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "id_membre" ){ ?>
                    <?php /* <input type="text" size="8" id="id_membre" name="id_membre" value="<?php echo $line->id_membre; ?>" /> */ ?>
                <?php }else{ 
                /* echo $line->id_membre ; */ echo "<--Non pris en charge pour le moment-->";
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "id_membre"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                    <?php /* <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=id_membre'; ?>"><?php echo img_edit(); ?></a>*/?>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "".$langs->trans('DC1_F'); ?>
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('F_engagement'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_F1_Tooltip"); ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "F_engagement" ){ ?>
                    <select id="F_engagement" name="F_engagement" value="<?php echo $line->F_engagement; ?>">
                        <option value="1" <?php if ($line->F_engagement == 1) { echo "selected" ; } ?>><?php echo $langs->trans('F_engagement1'); ?></option> 
                        <option value="2" <?php if ($line->F_engagement == 2) { echo "selected" ; } ?>><?php echo $langs->trans('F_engagement2'); ?></option>
                    </select>

                <?php }else{ 

                if ($line->F_engagement == 1) {
                   echo $langs->trans('F_engagement1') ; 
                }elseif ($line->F_engagement == 2) {
                    echo $langs->trans('F_engagement2') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>

            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "F_engagement"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=F_engagement'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>
                <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('F_documents'); ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans('DC1_F2_Tooltip'); ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>          
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('adresse_internet'); ?>      
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "adresse_internet" ){ ?>
                    <textarea type="text" size="8" id="adresse_internet" name="adresse_internet" ><?php echo $line->adresse_internet; ?></textarea>
                <?php }else{ 
                echo $line->adresse_internet ; 
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "adresse_internet"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=adresse_internet'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>        
                <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;".$langs->trans('renseignement_adresse'); ?>       
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "renseignement_adresse" ){ ?>
                    <textarea type="text" size="8" id="renseignement_adresse" name="renseignement_adresse" ><?php echo $line->renseignement_adresse; ?></textarea>
                <?php }else{ 
                echo $line->renseignement_adresse ; 
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "renseignement_adresse"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=renseignement_adresse'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td>           
                    <?php echo "&nbsp;&nbsp;&nbsp;".$langs->trans('DC1_F3'); ?>

                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;>Le candidat individuel, ou les membres du groupement, produisent, aux fins de vérification de l’aptitude à exercer l’activité professionnelle, de la capacité économique et financière et des capacités techniques et professionnelles : <i> (Cocher la case correspondante.)<i><ul><li>Le Formulaire DC2</li><li>les documents établissant ses capacités, tels que demandés dans les documents de la consultation(*).</li><li>Tout</li><li>Aucun</li></div>" class="paddingright classfortooltip valigntextbottom">     
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "dc2" ){ ?>
                    <select id="dc2" name="dc2" value="<?php echo $line->dc2; ?>">
                        <option value="1" <?php if ($line->dc2 == 1) { echo "selected" ; } ?>><?php echo $langs->trans('dc21'); ?></option> 
                        <option value="2" <?php if ($line->dc2 == 2) { echo "selected" ; } ?>><?php echo $langs->trans('dc22'); ?></option>
                        <option value="3" <?php if ($line->dc2 == 3) { echo "selected" ; } ?>><?php echo $langs->trans('dc23'); ?></option> 
                        <option value="4" <?php if ($line->dc2 == 4) { echo "selected" ; } ?>><?php echo $langs->trans('dc24'); ?></option>
                    </select>

                <?php }else{ 

                if ($line->dc2 == 1) {
                   echo $langs->trans('dc21') ; 
                }elseif ($line->dc2 == 2) {
                    echo $langs->trans('dc22') ; 
                }elseif ($line->dc2 == 3) {
                    echo $langs->trans('dc23') ; 
                }elseif ($line->dc2 == 4) {
                    echo $langs->trans('dc24') ; 
                }else{
                    echo $langs->trans('non_renseigne') ; 
                }
                
                } ?>
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "dc2"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                        <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=dc2'; ?>">
                            <?php echo img_edit(); ?>
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr> 

        <tr class="<?php echo ($i%2==0 ? 'impair' : 'pair'); ?>">
            <td> 
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "mandataire"){ ?>           
                    <?php echo $langs->trans('mandataire'); ?>
                <?php }else{ 
                    echo $langs->trans('mandataire'); 
                } ?>
                <img src="/theme/md/img/info.png" alt="" title="<div class=&quot;centpercent&quot;><?php echo $langs->trans("DC1_G_Tooltip"); ?></div>" class="paddingright classfortooltip valigntextbottom">
            </td>

            <td>
                <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "mandataire" ){ ?>
                    <?php /* <input type="text" size="8" id="mandataire" name="mandataire" value="<?php echo $line->mandataire; ?>" /> */ ?>
                <?php }else{ 
                /* echo $line->mandataire ; */ echo "<--Non pris en charge pour le moment-->";
                } ?>

                
            </td>

            <?php if ($action == 'editline' && $lineid == $line->rowid && $field == "mandataire"){ ?>
                <td align="right">
                    <input type="submit" class="button" name="save" value="<?php echo $langs->trans("Save"); ?>" />&nbsp;<input type="submit" class="button" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
                </td>
            <?php }else{ ?>
                <td align="right">
                    <?php if ($canAddLines) { ?>       
                    <?php /* <a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'&amp;field=mandataire'; ?>"><?php echo img_edit(); ?></a>*/?>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>




    <?php } ?>
	</form>
<?php } ?>

</table>
<table class="border" width="100%">
    <tr>
        <td>
            <?php echo $langs->trans('dc1'); ?>

        </td>
    </tr>
</table>

</div>

<br />

<?php dol_fiche_end(); ?>

<?php llxFooter(''); ?>

