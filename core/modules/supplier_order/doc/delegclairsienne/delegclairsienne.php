<?php

	$object->fetch_projet();
	$company = new Societe($this->db);
	$rowid = $object->project->socid ;
	$company->fetch($rowid);

	$Forme_Juridique_Societe = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);

	$Forme_Juridique_SousTraitant = getFormeJuridiqueLabel($object->thirdparty->forme_juridique_code) ;

$refop = '<div style="font-size:6.5px">'.$outputlangs->convToOutputCharset($object->array_options['options_lib_ch_client']).'</div>';


$refmarche = '<div style="font-size:6.5px">'.$outputlangs->convToOutputCharset($details_line->ref_chantier).' du '.dol_print_date($object->array_options['options_date_marche'],"day",false,$outputlangs,true).' LOT N°'.$outputlangs->convToOutputCharset($details_line->n_lot).' '.$outputlangs->convToOutputCharset($details_line->libelle_lot).'</div>';

$entreprin = '<div style="font-size:6.5px">'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</div>';

$soustraitant = '<div style="font-size:6.5px">'.$outputlangs->convToOutputCharset($object->thirdparty->name).'</div>';

$entreprin2 = '<div style="font-size:9px; line-height:15px;"> 
La société <b>'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</b>, forme juridique <b>'.$Forme_Juridique_Societe.'</b>, en qualité d’entrepreneur principal, ayant son siège social à <b>'.$conf->global->MAIN_INFO_SOCIETE_TOWN.'</b>, et 
enregistrée au registre du commerce de <b>'.$conf->global->MAIN_INFO_RCS.'</b>, sous le numéro, <b>'.$conf->global->MAIN_INFO_SIREN.'</b>.
<br/>
Représentée par <b>'.$conf->global->MAIN_INFO_GERANT.'</b> en sa qualité de <b>'.$conf->global->MAIN_INFO_QUALITE_GERANT.'</b>, (<i>Joindre en annexe un justificatif prouvant l’habilitation à engager la société.</i>)</div>';

$fournisseur = '<div style="font-size:9px; line-height:15px;"> 

La société <b>'.$outputlangs->convToOutputCharset($object->thirdparty->name).'</b>, forme juridique, <b>'.$Forme_Juridique_SousTraitant.'</b>, en qualité d’entreprise sous-traitante, ayant son siège social à <b>'.$outputlangs->convToOutputCharset($object->thirdparty->town).'</b>, et enregistrée au registre du commerce de <b>'.$outputlangs->convToOutputCharset($object->thirdparty->idprof4).'</b>, sous le numéro, <b>'.$outputlangs->convToOutputCharset($object->thirdparty->idprof1).'</b>. 
<br/>
Représentée par <b>'.$object->thirdparty->array_options['options_lmdb_representant'].'</b> en sa qualité de <b>'.$object->thirdparty->array_options['options_lmdb_qualite_representant'].'</b> (<i>Joindre en annexe un justificatif prouvant </i>
<i>l’habilitation à engager la société.</i>)</div>';


$client = '<div style="font-size:9px; line-height:12.5px;"> 
La société CLAIRSIENNE, Société Anonyme d’HLM, maître de l’ouvrage, ayant son siège social à <b> BORDEAUX (33), 223, avenue 
Émile Counord </b>, et enregistrée au registre du commerce de Bordeaux, sous le numéro <b> 458 205 382 </b>
Représentée par '.$company->array_options['options_lmdb_representant'].' en sa qualité '.$company->array_options['options_lmdb_qualite_representant'].'.
</div>';


$delegation = '<div style="font-size:9px; line-height:15px;">

L’entrepreneur principal a été chargé par le maître de l’ouvrage de la réalisation du lot ci-dessus désigné, conformément au <br/>marché référencé. <br/>L’entrepreneur principal, de son côté, a confié au sous-traitant l’exécution d’une une partie des travaux concernant ledit lot, <br/>pour un montant global de '.price($object->total_ttc).'€ TTC, suivant un contrat de sous-traitance en date du '.dol_print_date($object->date_commande,"day",false,$outputlangs,true).'.

</div>

';

?>