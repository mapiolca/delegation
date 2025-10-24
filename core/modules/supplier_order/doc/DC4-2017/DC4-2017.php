<?php 

 /* Copyright (C) 2018      Pierre Ardoin         <pierre.ardoin@gmail.com>

*/
	$object->fetch_projet();

	if ($object->element == 'order_supplier') {
	
		$company = new Societe($this->db);
		$rowid = $object->project->socid ;
		$company->fetch($rowid);
		$mou = $company->name.' - '.$company->address.' '.$company->zip.' '.$company->town ;

		$Client = '

		'.$company->name.'<br>
		'.$company->address.'<br>
		'.$company->zip.' '.$company->town.' ';


		$Representant = ''.$company->array_options['options_lmdb_representant'].', '.$company->array_options['options_lmdb_qualite_representant'].'';

		$Societe = '

		'.$conf->global->MAIN_INFO_SOCIETE_NOM.'<br>
		'.$conf->global->MAIN_INFO_SOCIETE_ADDRESS.'<br>
		'.$conf->global->MAIN_INFO_SOCIETE_ZIP.' '.$conf->global->MAIN_INFO_SOCIETE_TOWN.'';

		$SIREN_Societe = '

		N° SIREN/CIF : '.$conf->global->MAIN_INFO_SIREN.'<br>
		N° TVA Intracommunautaire : '.$conf->global->MAIN_INFO_TVAINTRA.'<br>
		Code APE : '.$conf->global->MAIN_INFO_APE.'';

		$Forme_Juridique_Societe = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);

		$SousTraitant = '

		'.$object->thirdparty->name.'<br>
		'.$object->thirdparty->address.'<br>
		'.$object->thirdparty->zip.' '.$object->thirdparty->town.'';

		$SIREN_SousTraitant = '

		N° SIREN/CIF : '.$object->thirdparty->idprof1.''.$outputlangs->convToOutputCharset($object->thirdparty->cif).'<br>
		N° TVA Intracommunautaire : '.$object->thirdparty->tva_intra.'<br>
		Code APE : '.$object->thirdparty->idprof3.'';

		$Forme_Juridique_SousTraitant = getFormeJuridiqueLabel($object->thirdparty->forme_juridique_code) ;

		$Representant_SousTraitant = ''.$object->thirdparty->array_options['options_lmdb_representant'].', '.$object->thirdparty->array_options['options_lmdb_qualite_representant'].'';



	}

	if ($object->element == 'commande') {
		
		$company = new Societe($this->db);
		$rowid = $object->project->socid ;
		$company->fetch($rowid);
		$mou = $company->name.' - '.$company->address.' '.$company->zip.' '.$company->town ;

		$Client = '

		'.$company->name.'<br>
		'.$company->address.'<br>
		'.$company->zip.' '.$company->town.' ';


		$Representant_SousTraitant = ''.$company->array_options['options_lmdb_representant'].', '.$company->array_options['options_lmdb_qualite_representant'].'';

		$SousTraitant = '

		'.$conf->global->MAIN_INFO_SOCIETE_NOM.'<br>
		'.$conf->global->MAIN_INFO_SOCIETE_ADDRESS.'<br>
		'.$conf->global->MAIN_INFO_SOCIETE_ZIP.' '.$conf->global->MAIN_INFO_SOCIETE_TOWN.'<br>
		Téléphone : '.$conf->global->MAIN_INFO_SOCIETE_TEL.'<br>
		Courriel : '.$conf->global->MAIN_INFO_SOCIETE_MAIL;

		$SIREN_SousTraitant = '

		N° SIREN/CIF : '.$conf->global->MAIN_INFO_SIREN.'<br>
		N° TVA Intracommunautaire : '.$conf->global->MAIN_INFO_TVAINTRA.'<br>
		Code APE : '.$conf->global->MAIN_INFO_APE.'';

		$Forme_Juridique_SousTraitant = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);

		$Societe = '

		'.$object->thirdparty->name.'<br>
		'.$object->thirdparty->address.'<br>
		'.$object->thirdparty->zip.' '.$object->thirdparty->town.'<br>
		Téléphone : '.$object->thirdparty->phone.'<br>
		Courriel : '.$object->thirdparty->email;


		$SIREN_Societe= '

		N° SIREN/CIF : '.$object->thirdparty->idprof1.''.$outputlangs->convToOutputCharset($object->thirdparty->cif).'<br>
		N° TVA Intracommunautaire : '.$object->thirdparty->tva_intra.'<br>
		Code APE : '.$object->thirdparty->idprof3.'';

		$Forme_Juridique_Societe = getFormeJuridiqueLabel($object->thirdparty->forme_juridique_code) ;

		if (!is_null($company->array_options['options_lmdb_representant'])) {
			$Representant = ''.$company->array_options['options_lmdb_representant'].', '.$company->array_options['options_lmdb_qualite_representant'].'';
		}

		


	}

	if ($details_line->nature_travaux == '') {
		$Travaux = '<p style = "color:red" >'.$langs->trans('non_renseigne').'</p>';
	}else{
		$Travaux = 'Travaux relatifs à '.$details_line->nature_travaux ;
	}

	

?>