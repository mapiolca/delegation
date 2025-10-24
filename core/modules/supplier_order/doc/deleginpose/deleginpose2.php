<?php 

 /* Copyright (C) 2018-2019      Pierre Ardoin         <mapiolca@me.com>

*/


$object->fetch_projet();
$company = new Societe($this->db);
$rowid = $object->project->socid ;
$company->fetch($rowid);
$mou = $company->name.' - '.$company->address.' '.$company->zip.' '.$company->town ;

if ($dc4_line->dc4_object_declaration == 3) {
    $dc4_modif = '<tr><td style="text-align:center">'.$outputlangs->transnoentities("dc4_acte_modificatif").' - '.dol_print_date(dol_now(),"day",false,$outputlangs).'</td></tr>';
}


if ($company->idprof1 == "") {$company_idprof1 = $outputlangs->transnoentities("non_renseigne");}else{$company_idprof1 = $company->idprof1;}

$text = '

<table>
    <tr>
        <td style="text-align:center; vertical-align:middle; font-size:14x;">CONVENTION DE DELEGATION PARFAITE DE PAIEMENT <br/>LOT '.$details_line->libelle_lot.'
        </td>
    </tr>'.$dc4_modif.'
    <tr>
    <td style="font-size:8px;text-align:center;">ci-après la « Délégation »</td>
    </tr>
</table>
<p><br><br><br></p>
<p><br><br><br></p>
<p><br><br><br></p>
<p>
<br>
<br>
<br>
<br>
<b>Entre : </b></p>
<p><b>La Société '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).',</b> <br/>immatriculée  SIREN/CIF '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SIREN).', <br/>ayant ses locaux '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_ADDRESS).' '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_ZIP).' '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_TOWN).', <br/>représentée par '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_GERANT).', agissant en sa qualité de '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_QUALITE_GERANT).', dument habilité </p>
<p>ci-après désignée par &#34;<b>le Délégant</b>&#34; </p>
<p><b>et </b></p>
<p><b>La Société '.$company->name.',</b><br/>immatriculée SIREN/CIF '.$company_idprof1.', <br/>ayant son siège social '.$company->address.' '.$company->zip.' '.$company->town.', <br/>représentée par '.$company->array_options['options_lmdb_representant'].', agissant en sa qualité de '.$company->array_options['options_lmdb_qualite_representant'].', dument habilité </p>
<p>ci-après désignée par &#34;<b>le Délégué</b>&#34; </p>
<p><b>et </b></p>
<p><b>La Société '.$outputlangs->convToOutputCharset($object->thirdparty->name).',</b> <br/>immatriculée  SIREN/CIF '.$object->thirdparty->idprof1.''.$outputlangs->convToOutputCharset($object->thirdparty->cif).', <br/>ayant son siège social '.$outputlangs->convToOutputCharset($object->thirdparty->address).' '.$outputlangs->convToOutputCharset($object->thirdparty->zip).' '.$outputlangs->convToOutputCharset($object->thirdparty->town).', <br/>représentée par '.$outputlangs->convToOutputCharset($object->thirdparty->array_options['options_lmdb_representant']).', agissant en sa qualité de '.$outputlangs->convToOutputCharset($object->thirdparty->array_options['options_lmdb_qualite_representant']).', dument habilité </p>
<p>ci-après désignée par &#34;<b>le Délégataire</b>&#34; </p>
<p>Le Délégant, le Délégué et le Délégataire étant ci-après collectivement dénommés les « Parties ». </p>

<p><b>Il a été préalablement exposé ce qui suit : </b></p>

<p>Le Délégant a passé commande au Délégataire, pour un montant total de  '.price($object->total_ht).' euros HT de divers matériels et  accessoires  afin  d’exécuter  des  travaux  d’équipement  du  chantier  référencé '.$details_line->ref_chantier.' '.$object->project->title.'  (ci-après  la « Commande »). </p>
<p>Cette commande fait suite à un contrat conclu entre le délégant et le délégué pour la réalisation de '.$details_line->nature_travaux.' du chantier '.$object->project->title.' situé au '.$details_line->adresse_chantier.' et comme décrit dans le(s) lot(s) '.$details_line->n_lot.', '.$details_line->libelle_lot.'. (ci-après le &#34;Contrat&#34;) </p>
<p>Le Délégataire souhaite recevoir toutes assurances quant au règlement de ses fournitures et a demandé d’être <br/>payé directement par le Délégué pour le compte du Délégant, pour un montant de '.price($object->total_ht).' euros HT. </p>
<p"><b>Ceci exposé, les Parties ont convenu ce qui suit : </b></p>


';


$text2 = '

<p>
<br>
<br>
<br>
<br><b>ARTICLE 1 </b></p>
<p>Par la présente Délégation, le Délégué consent à payer le Délégataire pour le compte du Délégant : <br>
- la somme de '.price($object->total_ht).' euros HT,<br>
- à échéance de '.$outputlangs->transnoentities("PaymentConditionShort".$object->cond_reglement_code).' à compter de la date d’émission de la facture. </p>
<p>Le Délégant autorise en conséquence le Délégué à déduire du montant de ses propres situations les sommes que le Délégué aura réglées au Délégataire pour son compte en application des présentes, étant entendu que tout paiement ne pourra être effectué par le Délégué au Délégataire que dans la mesure où des sommes seraient dues par le Délégué au Délégant. </p>
<p><b>Les parties ont convenues ce qui suit : </b></p>
<p><b>ARTICLE 2 </b></p>
<p><br>Afin d’assurer au délégataire le paiement des sommes en principal, intérêts, frais et accessoires qui lui sont dues, ainsi que mentionnées dans l’exposé préalable, le délégant délègue au délégataire, dans les conditions prévues par les articles 1275 et suivants du Code Civil, son débiteur, le délégué, lequel intervenant aux présentes, déclare accepter la présente délégation et se reconnaît en conséquence désormais tenu personnellement et directement envers le délégataire.
<br><br>Ainsi, le délégué s’engage à effectuer tous ses paiements directement auprés du délégataire, ceci à dû concurrence de la somme de : '.price($object->total_ttc).' euros TTC suivant les modalités ci-dessous énoncées.
<br><br>Tout versement ainsi effectué libèrera le délégué, à due concurrence à l’égard du délégant.
<br><br>La présente délégation entraîne novation aux obligations contractées par le délégant envers le délégataire au titre du contrat de fourniture ci-dessus mentionné sous l’exposé préalable; le délégant étant libéré de toutes ses obligations envers le délégataire ; le délégué y consentant expressément, de manière ferme et irrévocable. 
<br><br>A ce titre, le délégué ne pourra opposer aucune exception, quelque qu’elle soit, tirée du contrat d’affaire et/ou de ses relations avec le délégant, ni en cas de modification de la situation juridique ou financière du délégant (procédure collective ou autre), a quelque titre que ce soit.
<br><br>En conséquence, le délégué s’engage de manière formelle et irrévocable, à payer le délégataire, sans qu’il ne puisse invoquer la moindre exception liée à l’exécution défectueuse, inexécution ou retard d’exécution des prestations et/ou travaux qu’il a confié au délégant à l’exclusion de tout retard de livraison ou non conformité du produit avec les prescriptions contractuelles.
<br><br>Le délégant reste responsable de la garde des approvisionnements qui restent sous son entière responsabilité après les avoir lui-même réceptionnés. En cas de détérioration ou vol…. le nouvel approvisionnement sera déduit de son marché initial.
<br><br>Dès lors, le délégué s’engage à régler le délégataire par virement bancaire à 45 jours fin de mois par rapport à la date de livraison des marchandises (et correspondant à la date d’émission de la facture) dans les locaux du délégant, étant entendu que la dite livraison devra être conforme à la commande passée par le délégant au profit du délégataire. 
<br><br>Le délégant déclare qu’il n’a consenti aucune cession de créance, délégation ni aucun gage concernant les sommes faisant l’objet de la présente délégation.
<br><br>Le délégué déclare n’avoir reçu à ce jour aucune notification de délégation ou de cession de créance ou de signification de gage concernant les sommes objet de la présente délégation. </p>

';

if ($dc4_line->dc4_object_declaration == 3) {
    $dc4_modif_date = dol_print_date(dol_now(),"day",false,$outputlangs);
}else{
    $dc4_modif_date = dol_print_date($object->date,"day",false,$outputlangs);
}

$text3 = '

<p>
<br>
<br>
<br><b>ARTICLE 3 </b></p>
<p>La présente Délégation est régie par le droit français. <br/>Tout litige relatif à l\'exécution et/ou l’interprétation de la présente Délégation sera de la compétence exclusive du <br/>Tribunal de Commerce de '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_RCS).'. </p>
<p><b>ARTICLE 4 <br/></b>La présente Délégation est applicable à compter de sa date de complète signature par les Parties. </p></b>

<p>A '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_TOWN).', le '.$dc4_modif_date.' en trois exemplaires originaux, un pour chacune des Parties. </p>

<table>
    <tr>
        <td><b>Le Délégué</b></td>
        <td>M./Mme '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_GERANT).'</td>
        <td></td>
    </tr>
    <br/>
    <tr>
        <td>Société '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).'</td>
        <td>Date : ....................</td>
        <td>Signature :</td>
    </tr>
    <tr><p></p></tr>
    <tr><p></p></tr>
    <tr><p></p></tr>
    <tr>
        <td><b>Le Délégant</b></td>
        <td>M./Mme '.$company->array_options['options_lmdb_representant'].'</td>
        <td></td>
    </tr>
    <br/>
    <tr>
        <td>Société '.$company->name.'</td>
        <td>Date : ....................</td>
        <td>Signature :</td>
    </tr>
    <tr><p></p></tr>
    <tr><p></p></tr>
    <tr><p></p></tr>
    <tr><p></p></tr>
    <tr>
        <td><b>Le Délégataire</b></td>
        <td>M./Mme '.$outputlangs->convToOutputCharset($object->thirdparty->array_options['options_lmdb_representant']).'</td>
        <td></td>
    </tr>
    <br/><br/>
    <tr>
        <td>Société '.$outputlangs->convToOutputCharset($object->thirdparty->name).'</td>
        <td>Date : ....................</td>
        <td>Signature :</td>
    </tr>

</table>
';

$courrier_delegation = '

<p></br></p>
<p></br></p>
<p></br></p>
<p></br></p>
<p></br></p>
<span style="color:#000000; font-family:Calibri,sans-serif; font-size:11pt">Cher Monsieur, ch&egrave;re Madame,</span>
<div style="margin-left:40px">

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Veuillez trouver ci-joint notre proposition de délégation de paiement concernant votre chantier '.$details_line->ref_chantier.' '.$object->project->title.'. </span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Ceci repr&eacute;sente un document qui nous garantit votre engagement &agrave; payer les facture de la société '.$outputlangs->convToOutputCharset($object->thirdparty->name).' à leur date d\'échéance, dans ce cas à <b>'.$outputlangs->transnoentities("PaymentConditionShort".$object->cond_reglement_code).'</b> après la date de facturation. </span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">En aucun cas ce document ne représente un débit de votre compte avant l\'échéance accordée, mais engage seulement nos trois sociétés à respecter les engagements décrits dans le document ci-joint.</span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Merci de bien vouloir retourner ce document <strong><u>sign&eacute; et tamponn&eacute;</u></strong> par courrier, à chacune des parties (ou l\'ensemble à '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).' qui fera la redirection à '.$outputlangs->convToOutputCharset($object->thirdparty->name).').</span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm">&nbsp;</p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Nous restons à votre disposition pour tout renseignement supplémentaire.</span></span></span></p>
</div>

';

?>