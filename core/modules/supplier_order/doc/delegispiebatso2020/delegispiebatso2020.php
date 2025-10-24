<?php 

 /* Copyright (C) 2020      Pierre Ardoin         <mapiolca@me.com>

*/


$object->fetch_projet();
$company = new Societe($this->db);
$rowid = $object->project->socid ;
$company->fetch($rowid);
$mou = $company->name.' - '.$company->address.' '.$company->zip.' '.$company->town ; 

$spie = new Societe($this->db);

$rowid = $details_line->fk_moe ;
$spie->fetch($rowid);
$spie_entity = $spie->entity ; 

$logospie=$conf->societe->multidir_output[$conf->entity]."/".$spie->id."/logos/".$spie->logo;

$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;


$Forme_Juridique_Societe = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);
$Forme_Juridique_SousTraitant = getFormeJuridiqueLabel($object->thirdparty->forme_juridique_code) ;

//SPIE Name
if (empty($details_line->fk_moe)) {

    $spie_name = '<span style="color:red"><b><u>Veuillez saisir SPIE BATIGNOLLES SUD OUEST dans Projet->Détails->Maître d\'Oeuvre du chantier.</u></b></span>';

}elseif ($spie->name!=( 'SPIE BATIGNOLLES SUD OUEST' OR 'SPIE BATIGNOLLES SUD-OUEST')) {

    $spie_name = '<span style="color:red"><b><u>Modèle Réservé à SPIE BATIGNOLLES SUD OUEST, veuillez saisir SPIE BATIGNOLLES SUD OUEST dans Projet->Détails->Maître d\'Oeuvre du chantier.</u></b></span>';
    
}else{

    $spie_name = $spie->name ;

    }

//Fournisseur Forme Juridique
if (empty($object->thirdparty->forme_juridique_code)) {
    $Forme_Juridique_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir la forme juridique du sous-traitant dans Tiers->Fiche</u></b></span>';
}else{

    $Forme_Juridique_SousTraitant = getFormeJuridiqueLabel($object->thirdparty->forme_juridique_code) ;

    }

//Fournisseur Capital
if (empty($object->thirdparty->capital)) {
    $Capital_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir le Capital Social du sous-traitant dans Tiers->Fiche</u></b></span>';
}else{

    $Capital_SousTraitant = $object->thirdparty->capital ;

    }

//Fournisseur Adresse
if (empty($object->thirdparty->zip)) {

    $Adresse_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir l\'adresse postale du sous-traitant dans Tiers->Fiche</u></b></span>';
}else{

    $Adresse_SousTraitant = $object->thirdparty->address ;

    }

//Fournisseur Code Postal
if (empty($object->thirdparty->zip)) {
    $ZIP_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir le Code Postal du sous-traitant dans Tiers->Fiche</u></b></span>';
}else{

    $ZIP_SousTraitant = $object->thirdparty->zip ;

    }

//Fournisseur Ville
if (empty($object->thirdparty->town)) {
    $Ville_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir la Ville du sous-traitant dans Tiers->Fiche</u></b></span>';
}else{

    $Ville_SousTraitant = $object->thirdparty->town ;

    }

//Fournisseur SIREN
if (empty($object->thirdparty->idprof1) AND empty($object->thirdparty->cif)) {
    $SIREN_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir le SIREN du sous-traitant dans Tiers->Fiche->SIREN/CIF</u></b></span>';
}else{

    $SIREN_SousTraitant = $object->thirdparty->idprof1.''.$object->thirdparty->cif ;

    }

//Fournisseur RCS
if (empty($object->thirdparty->idprof4)) {

    $RCS_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir la Ville du RCS du sous-traitant dans Tiers->Fiche->RCS (Tout en Majuscules)</u></b></span>';

}else{

    $RCS_SousTraitant = $object->thirdparty->idprof4 ;

    }

//Fournisseur Représentant
if (empty($object->thirdparty->array_options['options_lmdb_representant'])) {

    $Representant_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir le Représentant du sous-traitant dans Tiers->Fiche</u></b></span>';

}else{

    $Representant_SousTraitant = $object->thirdparty->array_options['options_lmdb_representant'] ;

    }

//Fournisseur Qualité Représentant
if (empty($object->thirdparty->array_options['options_lmdb_qualite_representant'])) {

    $Qualite_Representant_SousTraitant = '<span style="color:red"><b><u>Veuillez saisir la Qualité du Représentant du sous-traitant dans Tiers->Fiche</u></b></span>';

}else{

    $Qualite_Representant_SousTraitant = $object->thirdparty->array_options['options_lmdb_qualite_representant'] ;

    }

//Projet Opportunité
if (empty($object->project->opp_amount)) {

    $Opportunite_Projet = '<span style="color:red"><b><u>Veuillez saisir le Montant d\'opportinité du Projet->Fiche (Inscrire le montant TTC)</u></b></span>';

}else{

    $Opportunite_Projet = $object->project->opp_amount ;

    }

//Projet Opportunité
if (empty($object->array_options['options_lmdb_poste'])) {

    $Poste_Commande = '<span style="color:red"><b><u>Veuillez saisir le Poste concerné par cette commande dans Commande->Fiche </u></b></span>';

}else{

    $Poste_Commande = $object->array_options['options_lmdb_poste'] ;

    }

//Projet N agture des Travaux
if (empty($details_line->nature_travaux)) {

    $nature_travaux = '<span style="color:red"><b><u>Veuillez saisir la Nature des Travaux dans Projet->Détail->Nature des Travaux </u></b></span>';

}else{

    $nature_travaux = $details_line->nature_travaux ;

    }

//Projet Adresse du Chantier
if (empty($details_line->adresse_chantier)) {

    $Adresse_Chantier = '<span style="color:red"><b><u>Veuillez saisir l\'Adresse du Chantier. dans Projet->Détail->Adresse du Chantier. </u></b></span>';

}else{

    $Adresse_Chantier = $details_line->adresse_chantier ;

    }




$text = '
<br><br><br><br><br><br><br><br>
<h3><b><u>ENTRE LES SOUSSIGNEES</u></b></h3><br>

<h4><b>'.$spie_name.'</b></h4><br>
<p>Société par Actions Simplifiée au capital social de 1 043 900 € <br>
Ayant son siège social 5 impasse Henry le Chatelier CS  20105 33701 MERIGNAC CEDEX<br>
Inscrite au registre du commerce des sociétés de Bordeaux sous le numéro 343.177.440<br>
Représentée par : Jean Michel AUGE agissant en qualité de Directeur d’Activités Projets SO<br>
Ci-après dénommée « le Mandataire »

<div style="text-align:right">De première part,</div></p>

<h4><b>'.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).'</b></h4>
<p>'.$outputlangs->convToOutputCharset($Forme_Juridique_Societe).' au capital social de '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_CAPITAL).'<br>
Ayant son siège social '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_ADDRESS).' '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_ZIP).' '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_TOWN).'<br>
Inscrite au registre du commerce et des sociétés de '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_RCS).' sous le numéro '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SIREN).'<br>
Représentée par : '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_GERANT).' agissant en sa qualité de '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_QUALITE_GERANT).'<br>
Ci-après dénommée « le Cotraitant »

<div style="text-align:right">De seconde part,</div></p>

<h4><b>'.$outputlangs->convToOutputCharset($object->thirdparty->name).'</b></h4>
<p>'.$outputlangs->convToOutputCharset($Forme_Juridique_SousTraitant).' au capital social de '.$outputlangs->convToOutputCharset(price($Capital_SousTraitant)).' €<br>
Ayant son siège social '.$outputlangs->convToOutputCharset($Adresse_SousTraitant).' '.$outputlangs->convToOutputCharset($ZIP_SousTraitant).' '.$outputlangs->convToOutputCharset($Ville_SousTraitant).'<br>
Inscrite au registre du commerce et des sociétés de '.$outputlangs->convToOutputCharset($RCS_SousTraitant).' sous le numéro '.$outputlangs->convToOutputCharset($SIREN_SousTraitant).'<br>
Représentée par : '.$outputlangs->convToOutputCharset($Representant_SousTraitant).' agissant en sa qualité de '.$outputlangs->convToOutputCharset($Qualite_Representant_SousTraitant).'.<br>
Ci-après dénommée « le Fournisseur »

<div style="text-align:right">De troisième part,</div></p>

<h4><b><u>PREALABLEMENT A LA DELEGATION DE PAIEMENT, IL EST EXPOSE CE QUI SUIT :</u></b></h4><br>

<p>Le Maître d’ouvrage, L’ANNEXE, a confié la réalisation des travaux relatifs à '.$nature_travaux.' '.$Adresse_Chantier.', à un GMEC dont SBSO est Mandataire. Les prestations effectuées par les membres du groupement (GMEC) sont réglées suivant situations mensuelles, payées entre les mains du Mandataire SBSO, qui répartit ensuite les fonds entre ses cotraitants.</p>

<p>La société '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).', membre du GMEC précité, cotraitant de SBSO, est en charge au sein de ce groupement des travaux de '.$details_line->libelle_lot.' pour un montant de '.price($Opportunite_Projet).' € TTC.</p>

<p>Cette dernière, afin de satisfaire à ses obligations contractuelles, a confié à l’entreprise '.$outputlangs->convToOutputCharset($object->thirdparty->name).' par commande en date du '.dol_print_date($object->date,"day",false,$outputlangs).' , la fourniture de '.$outputlangs->convToOutputCharset($Poste_Commande).'. pour un montant de '.price($object->total_ttc).' € TTC.</p>

<p>Sans préjudice des obligations réciproques liant le Cotraitant et son Fournisseur, ce dernier souhaite recevoir toutes assurances quant au règlement de ses prestations/livraisons et a demandé à être payé par le Mandataire SBSO pour le compte du Cotraitant.</p><br>
';

$text2 = '
<br><br>

<h3><b><u>CECI ETANT EXPOSE, IL A ETE CONVENU ET ARRETE CE QUI SUIT :</u></b></h3><br>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.  Pour la réalisation du chantier rappelé en préambule, le Cotraitant (déléguant) donne l’ordre irrévocable au Mandataire (délégué), de payer le Fournisseur (délégataire) pour le compte de celui-ci, pour un montant maximum de : '.price($object->total_ttc).' € TTC, dans le cadre d’une délégation au sens de l’article 1339 du Code Civil.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>2.</b>  Dès lors que le Mandataire accepte de prendre un tel engagement en considération de la personne du Cotraitant, la présente convention ne peut être cédée par ce dernier sans l’autorisation du Mandataire.</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>3.</b>  Il est expressément précisé que la présente convention n’a pas pour effet d’instituer un lien contractuel entre le Mandataire et le Fournisseur, mais seulement d’instituer un simple paiement pour compte.</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>4.</b>  Seules les prestations propres à l’affaire et objet du contrat visé en préambule entrent dans le champ de la présente convention.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>5.</b>  Les factures relatives aux prestations rappelées ci-dessus sont établies au nom du Cotraitant et adressées par le Fournisseur à ce dernier.<br>
A réception, le Cotraitant appose la mention « Bon à Payer » sur chacune des situations vérifiées, les signe et y appose son tampon commercial, puis les transmet, par voie de pli recommandé avec accusé de réception ou contre récépissé, au Mandataire avec copie au Fournisseur dans un délai de 15 jours maximum à compter de leur réception.<br>
Tout retard ou difficulté dans leur acheminement sera inopposable au Mandataire.<br>
A défaut de contestation expresse dans les 15 jours de la réception des dites factures, celles-ci seront considérées comme irrévocablement acceptées par leur destinataire.<br>
Le Mandataire est alors autorisé à payer le Fournisseur dans un délai de 60 jours suivant la date d’émission de ladite facture. Le Cotraitant autorise le Mandataire à déduire de ses situations les sommes que ce dernier aura réglées au Fournisseur pour le compte du Cotraitant.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>6.</b>  Le Mandataire s’interdit de payer pour compte au Fournisseur toute facture qui ne serait pas validée par le Cotraitant.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>7.</b>  Les dispositions qui précèdent n’ont pas pour effet de décharger le Cotraitant et le Fournisseur des responsabilités qui leur incombent au titre de leur contrat/commande respectif. 
A ce titre, le Cotraitant demeure responsable de l’ensemble de ses prestations au titre du marché de travaux qui le lit au maître d’ouvrage.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>8.</b>  Le Cotraitant déclare qu’au jour de la conclusion de la présente convention, il n’a consenti aucune cession de créance (notamment type loi DAILLY), délégation, ni aucun gage ou nantissement portant sur les sommes objet de la présente convention.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>9.</b>  Le Cotraitant s’engage à ne procéder à aucun nantissement ou cession de créance (notamment type loi DAILLY), délégation, gage ou nantissement, relativement aux sommes objet des présentes.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>10.</b>   En cas de contestation portant sur la validité, l’interprétation et/ou l’application de la présente convention, compétence est attribuée au Tribunal de Commerce de Bordeaux.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>11.</b>  Pour application des présentes les parties font élection de domicile en leur siège respectif.</p>

<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>12.</b>  La présente convention prendra fin lorsque le montant total des sommes versées par le Mandataire auront atteint le plafond mentionné au point 1, ou au plus tard à la réception des travaux, objet de l’opération visée en préambule.</p>
';

$text3 = '

<br><br><br>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>13. ETHIQUE :</b> les parties déclarent avoir une parfaite connaissance de la législation française et internationale visant à réprimer les atteintes à la probité et garantissent qu’il se conformeront à l’ensemble de ces dispositions et notamment, sans que l’énoncé des textes soit exhaustif :<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- En France :
De la loi 2016-1691 du 9 décembre 2016 relative à la transparence, à la lutte contre la corruption et à la modernisation de la vie économique (« loi Sapin 2 ») et des textes du code pénal réprimant la corruption.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- A l’international :
Les législations nationales et les dispositions adoptées par l’OCDE et la Convention des Nations Unies contre la corruption.</p><br>

<p>
Les parties déclarent avoir pris connaissance de la charte éthique et du code de conduite du Groupe Spie Batignolles pris en application de l’article 17 de la loi n°2016-1691 du 9 décembre 2016 relative à la transparence, à la lutte contre la corruption et à la modernisation de la vie économique (documents consultables à l’adresse : https://www.spiebatignolles.fr/politique-ethique/).
</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>14.  RGPD :</b> si des informations relatives à une personne physique identifiée ou identifiable (ci-après désignées par « les Données Personnelles ») sont transmises entre les parties, il est expressément convenu qu’elles seront chacune responsable du traitement au sens de la loi n°78-17 du 6 janvier 1978 modifiée, relative à l’informatique, aux fichiers et aux libertés (loi « Informatique et Libertés »), au règlement (UE) 2016/679 du parlement européen et du conseil du 27 avril 2016 relatif à la protection des personnes physiques à l’égard du traitement des données à caractère personnel et à la libre circulation de ces données.</p>
<p><p>Chaque partie garantit l’autre que conformément à la loi Informatique et Libertés, les Données Personnelles :<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-   ont été collectées et traitées loyalement et licitement ;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-   ont été collectées et traitées pour des finalités déterminées et légitimes ;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-   sont adéquates, pertinentes et non excessives par rapport aux finalités pour lesquelles elles ont été collectées.</p>
<p>Chaque partie déclare et garantit que les traitements qu’elle effectue sur les Données Personnelles ont fait l’objet des formalités requises auprès de la Commission Nationale de l’Informatique et des Libertés (CNIL) et que ces formalités sont compatibles avec le présent contrat.</p>

<p>Fait à '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_TOWN).', le '.dol_print_date(dol_now(),"day",false,$outputlangs,true).' (en trois exemplaires originaux)<p>

<table>
    <tr>
        <td>
            <div style="text-align:left;">
                <b>POUR LE MANDATAIRE SBSO</b>
            </div>
        </td>
        <td>
            <div style="text-align:right">
                « lu et approuvé » + cachet + signature
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div style="text-align:left">Monsieur Jean Michel AUGE</div> 
        </td>
    </tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr>
        <td>
            <div style="text-align:left;">
                <b>POUR LE COTRAITANT '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).'</b>
            </div>
        </td>
        <td>
            <div style="text-align:right">
                « lu et approuvé » + cachet + signature
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div style="text-align:left">'.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_GERANT).'</div>
        </td>
    </tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr><br></tr>
    <tr>
        <td>
            <div style="text-align:left; padding-top:120px">
                <b>POUR LE FOURNISSEUR '.$outputlangs->convToOutputCharset($object->thirdparty->name).'</b>
            </div>
        </td>
        <td>
            <div style="text-align:right; display: inline-block">« lu et approuvé » + cachet + signature</div>
        </td>
    </tr>
    <tr>
        <td>
            <div style="text-align:left">'.$outputlangs->convToOutputCharset($Representant_SousTraitant).'</div>
        </td>
    </tr
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

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Ceci repr&eacute;sente un document qui nous garantit votre engagement &agrave; payer les facture de la société '.$outputlangs->convToOutputCharset($object->thirdparty->name).' à leur date d\'échéance. </span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">En aucun cas ce document ne représente un débit de votre compte avant l\'échéance accordée, mais engage seulement nos trois sociétés à respecter les engagements décrits dans le document ci-joint.</span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Merci de bien vouloir retourner ce document <strong><u>sign&eacute; et tamponn&eacute;</u></strong> par courrier, à chacune des parties (ou l\'ensemble à '.$outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM).' qui fera la redirection à '.$outputlangs->convToOutputCharset($object->thirdparty->name).').</span></span></span></p>

<p style="margin-left:0cm; margin-right:0cm">&nbsp;</p>

<p style="margin-left:0cm; margin-right:0cm"><span style="font-size:11pt"><span style="font-family:Calibri,sans-serif"><span style="color:#000000">Nous restons à votre disposition pour tout renseignement supplémentaire.</span></span></span></p>
</div>

';

?>