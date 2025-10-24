<?php
/* Copyright (C) 2004-2014 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerke@telenet.be>
 * Copyright (C) 2010-2014 Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2015       Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2017      Ferran Marcet         <fmarcet@2byte.es>
 * Copyright (C) 2018      Pierre Ardoin         <pierre.ardoin@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/core/modules/supplier_order/doc/pdf_DC42017modules.php
 *	\ingroup    fournisseur
 *	\brief      File of class to generate suppliers orders from Délégation INPOSE model
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_order/modules_commandefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/delegation/class/delegation.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/delegation/class/dc4.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/delegation/class/detailprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

/**
 *	Class to generate the supplier orders with the Délégation INPOSE model
 */
class pdf_csstbtps2014 extends ModelePDFSuppliersOrders
{
    var $db;
    var $name;
    var $description;
    var $type;

    var $phpmin = array(4,3,0); // Minimum version of PHP required by module
    var $version = 'dolibarr';

    var $page_largeur;
    var $page_hauteur;
    var $format;
	var $marge_gauche;
	var	$marge_droite;
	var	$marge_haute;
	var	$marge_basse;

	var $emetteur;	// Objet societe qui emet


	/**
	 *	Constructor
	 *
	 *  @param	DoliDB		$db      	Database handler
	 */
	function __construct($db)
	{
		global $conf,$langs,$mysoc;

		$langs->load("main");
		$langs->load("bills");
		$langs->load("btp@btp");
		$langs->load("delegation@delegation");

		$this->db = $db;
		$this->name = "Contrat de Sous-Traitance BTP Simplifié 2014";
		$this->description = $langs->trans('CSTBTP2014ModelLMBD');

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->MAIN_PDF_MARGIN_LEFT)?$conf->global->MAIN_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->MAIN_PDF_MARGIN_RIGHT)?$conf->global->MAIN_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->MAIN_PDF_MARGIN_TOP)?$conf->global->MAIN_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->MAIN_PDF_MARGIN_BOTTOM)?$conf->global->MAIN_PDF_MARGIN_BOTTOM:10;

		$this->emetteur=$mysoc;
	}


    /**
     *  Function to build pdf onto disk
     *
     *  @param		CommandeFournisseur	$object				Id of object to generate
     *  @param		Translate			$outputlangs		Lang output object
     *  @param		string				$srctemplatepath	Full path of source filename for generator using a template file
     *  @param		int					$hidedetails		Do not show line details
     *  @param		int					$hidedesc			Do not show desc
     *  @param		int					$hideref			Do not show ref
     *  @return		int										1=OK, 0=KO
     */
	function write_file($object,$outputlangs='',$srctemplatepath='',$hidedetails=0,$hidedesc=0,$hideref=0)
	{
		global $user,$langs,$conf,$hookmanager,$mysoc;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output='ISO-8859-1';

		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");
		$outputlangs->load("bills");
		$outputlangs->load("products");
		$outputlangs->load("orders");

		if ($conf->fournisseur->dir_output.'/commande')
		{
			$object->fetch_thirdparty();

			$deja_regle = 0;
			$amount_credit_notes_included = 0;
			$amount_deposits_included = 0;
			//$amount_credit_notes_included = $object->getSumCreditNotesUsed();
            //$amount_deposits_included = $object->getSumDepositsUsed();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->fournisseur->commande->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$objectrefsupplier = dol_sanitizeFileName($object->ref_supplier);
				$dir = $conf->fournisseur->commande->dir_output . '/'. $objectref;
				$file = $dir . "/" . $objectref . " - Contrat de Sous-Traitance BTP Simplifié 2014.pdf";
				if (! empty($conf->global->SUPPLIER_REF_IN_NAME)) $file = $dir . "/" . $objectref . " - Contrat de Sous-Traitance BTP Simplifié 2014.pdf";
			}

			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
					return 0;
				}

			}

			if (file_exists($dir))
			{
				// Add pdfgeneration hook
				if (! is_object($hookmanager))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager=new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('beforePDFCreation',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks

				$nblignes = count($object->lines);

                $pdf=pdf_getInstance($this->format);
                $default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
                $heightforinfotot = 50;	// Height reserved to output the info and total part
		        $heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:5);	// Height reserved to output the free text on last page
	            $heightforfooter = $this->marge_basse + 8;	// Height reserved to output the footer (value include bottom margin)
                $pdf->SetAutoPageBreak(1,0);

                if (class_exists('TCPDF'))
                {
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false);
                }
                $pdf->SetFont(pdf_getPDFFont($outputlangs));
                // Set path to the background PDF File

				$pdf->Open();
				$pagenb=0;
				$pdf->SetDrawColor(128,128,128);

				$pdf->SetTitle($outputlangs->convToOutputCharset($object->ref));
				$pdf->SetSubject($outputlangs->transnoentities("Order"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("Order")." ".$outputlangs->convToOutputCharset($object->thirdparty->name));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right

				// Courrier
//					$pdf->AddPage();
					
					$pagenb++;
					
					$this->page_largeur = 210;
					$this->page_hauteur = 197;
					
					$pdf->setPageOrientation('P', 1);


			$dc4_lines = array();
				if ($conf->delegation->enabled)
				{
					dol_include_once("/delegation/class/dc4.class.php");

					$id = GETPOST('id', 'int');
					$dc4 = new DC4($this->db);

					if ($id > 0)
					{
						$dc4_result = $object->fetch($id);

						if ($dc4_result > 0)
						{
							$dc4->fetch();
							$dc4_lines = $dc4->lines;
							foreach ($dc4_lines as $dc4_line)
							{
								$index++;
									$dc4_line->rowid;
									$dc4_line->fk_object;
									$dc4_line->fk_element;
									$dc4_line->dc4_object_declaration;
									$dc4_line->dc4_date_initiale ;
									$dc4_line->dc4_hypothese ;
									$dc4_line->avance ;
									$dc4_line->dc4_documents_fournis ;
									$dc4_line->paiement_direct ;
									$dc4_line->libelle_poste_cctp ;
									$dc4_line->sps_travaux ;
									$dc4_line->sps_date_remise;
									$dc4_line->cissct ;
									$dc4_line->DIUO ;
									$dc4_line->responsabilite ;
							}
							
							if ($object->element != 'order_supplier')// || $object->type != 5)
							{
								$error = true;
								$message = $langs->trans('NotASupplierOrder');
							}

						}
						else
						{
							$error = true;
							$message = $langs->trans('ObjectNotFound');
						}
					}
					else
					{
						$error = true;
						$message = $langs->trans('ObjectNotFound');
					}
					
				}



			$details_lines = array();
				if ($conf->delegation->enabled)
				{
					dol_include_once("/delegation/class/detailprojet.class.php");

					$projectid = $object->fetch_projet();

					$details = new Details($this->db);

					if ($projectid > 0)
					{
						$result = $object->project->id;

						if ($result > 0)
						{
							$details->fetch();
							$details_lines = $details->lines;
							foreach ($details_lines as $details_line)
							{
								$index++;
								$details_line->type_mou ;
								$details_line->ref_chantier ;
								$details_line->adresse_chantier ;
								$details_line->nature_travaux ;
								$details_line->fk_moe ;
								$details_line->n_lot ;
								$details_line->libelle_lot ;
								$details_line->marche_defense ;
								$details_line->rg_sstt ;
							}
							
							if ($object->element != 'project')// || $object->type != 5)
							{
								$error = true;
								$message = $langs->trans('NotASupplierOrder');
							}

						}
						else
						{
							$error = true;
							$message = $langs->trans('ObjectNotFound');
						}
					}
					else
					{
						$error = true;
						$message = $langs->trans('ObjectNotFound');
					}
				}

			// Page 1
				$pdf->AddPage();
				
                $pagecount = $pdf->setSourceFile(DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/doc/contrat-sous-traitance-btp-simplifie-2014/contrat-sous-traitance-btp-simplifie-2014.pdf');
                $tplidx = $pdf->importPage(1);
                
				
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);


				//include DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/doc/contrat-sous-traitance-btp-simplifie-2014/contrat-sous-traitance-btp-simplifie-2014.php';

				$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;

				$height=pdf_getHeightForLogo($logo);
				$pdf->Image($logo, 10, 280, "", 10);

				$this->_pagefoot($pdf,$object,$outputlangs,1);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu
					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(190,4, 71, 30.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_NOM)),0,1);

					$Forme_Juridique_Societe = getFormeJuridiqueLabel($conf->global->MAIN_INFO_SOCIETE_FORME_JURIDIQUE);

					$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 71, 35, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Forme_Juridique_Societe)),0,1);
					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 140, 35, dol_htmlentitiesbr($outputlangs->convToOutputCharset($conf->global->MAIN_INFO_CAPITAL)),0,1);

					$Adresse = $conf->global->MAIN_INFO_SOCIETE_ADDRESS.' '.$conf->global->MAIN_INFO_SOCIETE_ZIP.' '.$conf->global->MAIN_INFO_SOCIETE_TOWN ;
					$pdf->writeHTMLCell(190,4, 71, 41, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Adresse)),0,1);

					$pdf->writeHTMLCell(190,4, 71, 45.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SIRET)),0,1);

					$pdf->writeHTMLCell(190,4, 71, 57.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($conf->global->MAIN_INFO_GERANT)),0,1);

					$pdf->writeHTMLCell(190,4, 71, 62.6, dol_htmlentitiesbr($outputlangs->convToOutputCharset($conf->global->MAIN_INFO_QUALITE_GERANT)),0,1);

					$pdf->writeHTMLCell(190,4, 71, 78, dol_htmlentitiesbr($outputlangs->convToOutputCharset($object->thirdparty->name)),0,1);

					$Forme_Juridique_SousTraitant = getFormeJuridiqueLabel($object->thirdparty->forme_juridique_code) ;

					$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 71, 84, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Forme_Juridique_SousTraitant)),0,1);
					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 140, 83, dol_htmlentitiesbr($outputlangs->convToOutputCharset(price($object->thirdparty->capital))),0,1);

					$Adresse_sst = ''.$object->thirdparty->address.' '.$object->thirdparty->zip.' '.$object->thirdparty->town.'';
					$pdf->writeHTMLCell(190,4, 71, 88.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Adresse_sst)),0,1);

					$SIRETCIFsst = $object->thirdparty->idprof2.''.$outputlangs->convToOutputCharset($object->thirdparty->cif) ;

					$pdf->writeHTMLCell(190,4, 71, 94, dol_htmlentitiesbr($outputlangs->convToOutputCharset($SIRETCIFsst)),0,1);

					$pdf->writeHTMLCell(190,4, 71, 105.2, dol_htmlentitiesbr($outputlangs->convToOutputCharset($object->thirdparty->array_options['options_lmdb_representant'])),0,1);
					$pdf->writeHTMLCell(190,4, 71, 110.6, dol_htmlentitiesbr($outputlangs->convToOutputCharset($object->thirdparty->array_options['options_lmdb_qualite_representant'])),0,1);

					$object->fetch_projet();

					$Travaux = 'Travaux relatifs à '.$details_line->nature_travaux;
					$pdf->writeHTMLCell(190,4, 18, 142, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Travaux)),0,1);


					$company = new Societe($this->db);
					$rowid = $object->project->socid ;
					$company->fetch($rowid);
					$mou = $company->name.' - '.$company->address.' '.$company->zip.' '.$company->town ;

					$pdf->writeHTMLCell(190,4, 47, 162.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($mou)),0,1);

					$rowid = $details_line->fk_moe ;
					$company->fetch($rowid);
					$moe = $company->name.' - '.$company->address.' '.$company->zip.' '.$company->town ;

					$pdf->writeHTMLCell(190,4, 47, 167.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($moe)),0,1);
					$pdf->writeHTMLCell(190,4, 47, 172.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($details_line->libelle_lot)),0,1);
					$pdf->writeHTMLCell(190,4, 47, 177.5, dol_htmlentitiesbr($outputlangs->convToOutputCharset($details_line->adresse_chantier)),0,1);

					$lpaf1 = '- Extrait K-Bis <br>- Attestation Mutuelle <br>- Attestation Congés Payés<br>- Attestation d’assurance de l’année en cours<br>- Attestation d’Impôts<br>';
					$lpaf2 = '- Attestation URSSAF<br>- Relevé d’Identité  Bancaire<br>- Commande et Devis<br>- Déclaration de Sous-traitance - DC4 (le cas échéant)<br> ';

					$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 20, 208, dol_htmlentitiesbr($outputlangs->convToOutputCharset($lpaf1)),0,1);
					$pdf->writeHTMLCell(190,4, 70, 208, dol_htmlentitiesbr($outputlangs->convToOutputCharset($lpaf2)),0,1);

					$lptf = '-Sans Objet';
					$pdf->writeHTMLCell(190,4, 20, 228, dol_htmlentitiesbr($outputlangs->convToOutputCharset($lptf)),0,1);

					$lpgf = '-Sans Objet';
					$pdf->writeHTMLCell(190,4, 20, 262, dol_htmlentitiesbr($outputlangs->convToOutputCharset($lptf)),0,1);			


			// Page 2
				$pdf->AddPage();

				$tplidx = $pdf->importPage(2);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 280, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu
					
					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)


					if ($dc4_line->sps_travaux == '1') {
					
					$pdf->writeHTMLCell(190,4, 104.5, 44, dol_htmlentitiesbr("X"),0,1);
					$pdf->writeHTMLCell(190,4, 79, 52.5, dol_print_date($dc4_line->sps_date_remise,"day",false,$outputlangs,true),0,1);

				} elseif ($dc4_line->sps_travaux == '2') {
					
					$pdf->writeHTMLCell(190,4, 125, 44, dol_htmlentitiesbr("X"),0,1);

				} else {

					$pdf->writeHTMLCell(190,4, 125, 44, dol_htmlentitiesbr("X"),0,1);

				}



					if ($dc4_line->DIUO == '1') {
					
					$pdf->writeHTMLCell(190,4, 105.5, 60.7, dol_htmlentitiesbr("X"),0,1);

				} elseif ($dc4_line->DIUO == '2') {
					
					$pdf->writeHTMLCell(190,4, 125.8, 60.7, dol_htmlentitiesbr("X"),0,1);

				} else {

					$pdf->writeHTMLCell(190,4, 125.8, 60.7, dol_htmlentitiesbr("X"),0,1);

				}


					if ($dc4_line->cissct == '1') {
					
					$pdf->writeHTMLCell(190,4, 64.2, 65.2, dol_htmlentitiesbr("X"),0,1);

				} elseif ($dc4_line->cissct == '2') {
					
					$pdf->writeHTMLCell(190,4, 85, 65.2, dol_htmlentitiesbr("X"),0,1);

				} else {

					$pdf->writeHTMLCell(190,4, 85, 65.2, dol_htmlentitiesbr("X"),0,1);

				}


				$pdf->SetFont('','B',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(190,4, 125.8, 86.5, dol_htmlentitiesbr("Suivant CCAP Marché consultable sur demande."),0,1);
				$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(190,4, 123.8, 96.5, dol_htmlentitiesbr("0,00 (non spécifié)"),0,1);

				$pdf->writeHTMLCell(190,4, 83, 119.5, dol_htmlentitiesbr(price($object->total_ht)),0,1);

				$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(190,4, 142.5, 142.5, dol_htmlentitiesbr("X"),0,1);


				if ($details_line->type_mou == '1') {
					
					$pdf->writeHTMLCell(190,1, 14.2, 207.5, dol_htmlentitiesbr("X"),0,1);

				} elseif ($details_line->type_mou == '2') {
					
					$pdf->writeHTMLCell(190,1, 14.2, 253.3, dol_htmlentitiesbr("X"),0,1);

				}

					$pdf->writeHTMLCell(190,1, 17.4, 263, dol_htmlentitiesbr("X"),0,1);

					$pdf->writeHTMLCell(190,1, 25, 272, dol_htmlentitiesbr(" Entre le 15 et le 20 du mois. Toute situation reçue au-delà de ce délai sera reportée au mois suivant."),0,1);


			// Page 3
				$pdf->AddPage();

				$tplidx = $pdf->importPage(3);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 280, "", 10);
				//Nature des Travaux
				//$pdf->setXY(10,10); // fixe les positions x et y courantes
				//$pdf->SetFont('','',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				//$pdf->writeHTMLCell(190,4, 10, 10, dol_htmlentitiesbr($text3),0,1);

				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)


					$pdf->writeHTMLCell(190,4, 25, 20, dol_htmlentitiesbr("Situation d'avancement mensuel ou Solde suivant la durée des travaux à effectuer"),0,1);

					$lib_condition_paiement=$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code)!=('PaymentCondition'.$object->cond_reglement_code)?$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code):$outputlangs->convToOutputCharset($object->cond_reglement);

					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4,70, 35, dol_htmlentitiesbr($lib_condition_paiement),0,1);


					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 150, 59, dol_htmlentitiesbr("7"),0,1);


					if ($object->array_options['options_delailivraison'] == "1") {
						
						$pdf->writeHTMLCell(190,4, 150, 79, dol_htmlentitiesbr($object->array_options['options_delailivraison'].' semaine'),0,1);
					
					} else {
						
						$pdf->writeHTMLCell(190,4, 150, 79, dol_htmlentitiesbr($object->array_options['options_delailivraison'].' semaines'),0,1);

					}
					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 25, 90, dol_htmlentitiesbr('-Retard de Validation des plans,<br>-Retard justifié par le transporteur,<br>-Retard justifié par un fournisseur.'),0,1);


					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 17.8, 108.1, dol_htmlentitiesbr("X"),0,1);	

					$pdf->writeHTMLCell(190,4, 50, 155, dol_htmlentitiesbr("Maximum 30% du Montant du Marché Initial"),0,1);

					$pdf->writeHTMLCell(190,4, 165, 172.5, dol_htmlentitiesbr("30 jours"),0,1);		

					if ($dc4_line->responsabilite == "2") {
						
						$pdf->writeHTMLCell(190,4, 115, 181, dol_htmlentitiesbr("X"),0,1);
					
					} else {
						
						$pdf->writeHTMLCell(190,4, 130.5, 181, dol_htmlentitiesbr("X"),0,1);

					}


					if ($details_line->rg_sstt >> 1) {
						
						$pdf->writeHTMLCell(190,4, 92, 198, dol_htmlentitiesbr(price($details_line->rg_sstt.'')),0,1);
						$pdf->writeHTMLCell(190,4, 17.3, 198, dol_htmlentitiesbr("X"),0,1);
						$pdf->writeHTMLCell(190,4, 25.5, 222, dol_htmlentitiesbr("X"),0,1);
					
					} elseif ($details_line->rg_sstt == 0 OR $details_line->rg_sstt == '0,00') {
						
						$pdf->writeHTMLCell(190,4, 17.3, 193.5, dol_htmlentitiesbr("X"),0,1);

					} else {
						
						$pdf->writeHTMLCell(190,4, 17.3, 198, dol_htmlentitiesbr("X"),0,1);
						$pdf->writeHTMLCell(190,4, 25.5, 222, dol_htmlentitiesbr("X"),0,1);
						$pdf->writeHTMLCell(190,4, 92, 198, dol_htmlentitiesbr($details_line->rg_sstt / 100 .''),0,1);

					}


					$pdf->writeHTMLCell(190,4, 60, 245, dol_htmlentitiesbr("4 (Voir page 8/13)"),0,1);
					$pdf->writeHTMLCell(190,4, 114, 254, dol_htmlentitiesbr("X"),0,1);
					$pdf->writeHTMLCell(190,4, 94.5, 259.5, dol_htmlentitiesbr("4 (Voir page 8/13)"),0,1);
					$pdf->writeHTMLCell(190,4, 60, 263.7, dol_htmlentitiesbr("10"),0,1);

/*
				//Banque

					require_once DOL_DOCUMENT_ROOT . '/societe/class/companybankaccount.class.php';
					$object->thirdparty->bac = new CompanyBankAccount($this->db);
					$object->thirdparty->bac->fetch(0,$object->thirdparty->id);

					//$pdf->writeHTMLCell(60,10, 30, 245, $outputlangs->convToOutputCharset($object->thirdparty->bac->code_banque),0,1);
					//$pdf->writeHTMLCell(60,10, 59, 245, $outputlangs->convToOutputCharset($object->thirdparty->bac->code_guichet),0,1);
					//$pdf->writeHTMLCell(60,10, 65, 245, $outputlangs->convToOutputCharset($object->thirdparty->bac->number),0,1);
					//$pdf->writeHTMLCell(60,10, 92, 245, $outputlangs->convToOutputCharset($object->thirdparty->bac->cle_rib),0,1);
					
					$IBAN = 'IBAN : '.$object->thirdparty->bac->iban.'<br>BIC/SWIFT : '.$object->thirdparty->bac->bic.'';

					$pdf->writeHTMLCell(100,10, 70, 257, $outputlangs->convToOutputCharset($IBAN),0,1);

					$pdf->writeHTMLCell(60,10, 70, 252, $outputlangs->convToOutputCharset($object->thirdparty->bac->bank),0,1);

					if ($object->array_options['options_ss_avance'] == '1') {
					
						$pdf->writeHTMLCell(190,4, 119.4, 267, dol_htmlentitiesbr("X"),0,1);

					} elseif ($object->array_options['options_ss_avance'] == '2') {
					
						$pdf->writeHTMLCell(190,4, 139, 267, dol_htmlentitiesbr("X"),0,1);

					}
*/

			// Page 4
				$pdf->AddPage();

				$tplidx = $pdf->importPage(4);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 280, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(190,4, 17.7, 76.1, dol_htmlentitiesbr("X"),0,1);
					$pdf->writeHTMLCell(190,4, 17.7, 98.2, dol_htmlentitiesbr("X"),0,1);

					$pdf->writeHTMLCell(190,4, 17.7, 141.2, dol_htmlentitiesbr("X"),0,1);
					$pdf->writeHTMLCell(190,4, 110, 141.2, dol_htmlentitiesbr($conf->global->MAIN_INFO_RCS),0,1);

					$pdf->writeHTMLCell(190,4, 20, 160, dol_htmlentitiesbr("Sans Objet"),0,1);

					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 20, 200, dol_htmlentitiesbr($outputlangs->convToOutputCharset($lpaf1)),0,1);
					$pdf->writeHTMLCell(190,4, 90, 200, dol_htmlentitiesbr($outputlangs->convToOutputCharset($lpaf2)),0,1);

					$pdf->SetFont('','B',8); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 32, 233, dol_htmlentitiesbr($outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_TOWN)),0,1);

					$pdf->writeHTMLCell(150,1, 160, 233,  dol_print_date($object->date_commande,"day",false,$outputlangs,true),0,1);

					



			// Page 5
				$pdf->AddPage();

				$tplidx = $pdf->importPage(5);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 6
				$pdf->AddPage();

				$tplidx = $pdf->importPage(6);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 7
				$pdf->AddPage();

				$tplidx = $pdf->importPage(7);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 8
				$pdf->AddPage();

				$tplidx = $pdf->importPage(8);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 9
				$pdf->AddPage();

				$tplidx = $pdf->importPage(9);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 10
				$pdf->AddPage();

				$tplidx = $pdf->importPage(10);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 11
				$pdf->AddPage();

				$tplidx = $pdf->importPage(11);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 12
				$pdf->AddPage();

				$tplidx = $pdf->importPage(12);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


			// Page 7
				$pdf->AddPage();

				$tplidx = $pdf->importPage(13);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();
				
			// Fermture Formulaire
				$pdf->Close();

				$pdf->Output($file,'F');


				// Add pdfgeneration hook
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('afterPDFCreation',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks

				if (! empty($conf->global->MAIN_UMASK))
				@chmod($file, octdec($conf->global->MAIN_UMASK));

				return 1;   // Pas d'erreur

			}
			else
			{
				$this->error=$langs->trans("ErrorCanNotCreateDir",$dir);
				return 0;
			}
		}
		else
		{
			$this->error=$langs->trans("ErrorConstantNotDefined","SUPPLIER_OUTPUTDIR");
			return 0;
		}
	}


/**
	 *  Show top header of page.
	 *
	 *  @param	PDF			$pdf     		Object PDF
	 *  @param  CommandeFournisseur		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @return	void
	 */
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs)
	{
		global $langs,$conf,$mysoc;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("orders");
		$outputlangs->load("companies");
		$outputlangs->load("sendings");
		$outputlangs->load("delegation@delegation");

		$object->fetch_projet();

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		// Do not add the BACKGROUND as this is for suppliers
		//pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		//Affiche le filigrane brouillon - Print Draft Watermark
		/*if($object->statut==0 && (! empty($conf->global->COMMANDE_DRAFT_WATERMARK)) )
		{
            pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->COMMANDE_DRAFT_WATERMARK);
		}*/
		//Print content

		$pdf->SetTextColor(0,0,60);
		$pdf->SetFont('','B',$default_font_size + 3);

		$posx=$this->page_largeur-$this->marge_droite-100;
		$posy=$this->marge_haute;

		$pdf->SetXY($this->marge_gauche,$posy);

		// Logo
		$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;
		if ($this->emetteur->logo)
		{
			if (is_readable($logo))
			{
			    $height=pdf_getHeightForLogo($logo);
			    $pdf->Image($logo, $this->marge_gauche, $posy, 0, $height);	// width=0 (auto)
			}
			else
			{
				$pdf->SetTextColor(200,0,0);
				$pdf->SetFont('','B', $default_font_size - 2);
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorLogoFileNotFound",$logo), 0, 'L');
				$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorGoToModuleSetup"), 0, 'L');
			}
		}
		else
		{
			$text=$this->emetteur->name;
			$pdf->MultiCell(100, 4, $outputlangs->convToOutputCharset($text), 0, 'L');
		}

		$pdf->SetFont('', 'B', $default_font_size + 3);
		$pdf->SetXY($posx,$posy);
		$pdf->SetTextColor(0,0,60);
		$title=$outputlangs->transnoentities("Deleg")." ".$outputlangs->convToOutputCharset($object->ref);
		$pdf->MultiCell(100, 3, $title, '', 'R');
		$posy+=1;


		$pdf->SetFont('','', $default_font_size -1);



		/*if (! empty($object->date_commande))
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("OrderDate")." : " . dol_print_date($object->date_commande,"day",false,$outputlangs,true), '', 'R');
		}
		else
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(255,0,0);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("OrderToProcess"), '', 'R');
		}*/

		$pdf->SetTextColor(0,0,60);
		$usehourmin='day';
		/*if (!empty($conf->global->SUPPLIER_ORDER_USE_HOUR_FOR_DELIVERY_DATE)) $usehourmin='dayhour';
		if (! empty($object->date_livraison))
		{
			$posy+=4;
			$pdf->SetXY($posx-90,$posy);
			$pdf->MultiCell(190, 3, $outputlangs->transnoentities("DateDeliveryPlanned")." : " . dol_print_date($object->date_livraison,$usehourmin,false,$outputlangs,true), '', 'R');
		}*/

		/*if ($object->thirdparty->code_fournisseur)
		{
			$posy+=4;
			$pdf->SetXY($posx,$posy);
			$pdf->SetTextColor(0,0,60);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("SupplierCode")." : " . $outputlangs->transnoentities($object->thirdparty->code_fournisseur), '', 'R');
		}*/

		$posy+=1;
		$pdf->SetTextColor(0,0,60);

		/*
		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size);
		*/

		if ($showaddress)
		{
			// Sender properties
			$carac_emetteur = pdf_build_address($outputlangs, $this->emetteur, $object->thirdparty);

			// Show sender
			$posy=42;
			$posx=$this->marge_gauche;
			if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->page_largeur-$this->marge_droite-80;
			$hautcadre=40;

			// Show sender frame
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posx,$posy-5);
			$pdf->MultiCell(66,5, $outputlangs->transnoentities("BillFrom").":", 0, 'L');
			$pdf->SetXY($posx,$posy);
			$pdf->SetFillColor(230,230,230);
			$pdf->MultiCell(82, $hautcadre, "", 0, 'R', 1);
			$pdf->SetTextColor(0,0,60);

			// Show sender name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell(80, 4, $outputlangs->convToOutputCharset($this->emetteur->name), 0, 'L');
			$posy=$pdf->getY();

			// Show sender information
			$pdf->SetXY($posx+2,$posy);
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->MultiCell(80, 4, $carac_emetteur, 0, 'L');



			// If BILLING contact defined on order, we use it
			$usecontact=false;
			$arrayidcontact=$object->getIdContact('external','BILLING');
			if (count($arrayidcontact) > 0)
			{
				$usecontact=true;
				$result=$object->fetch_contact($arrayidcontact[0]);
			}

			//Recipient name
			// On peut utiliser le nom de la societe du contact
			if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) {
				$thirdparty = $object->contact;
			} else {
				$thirdparty = $object->thirdparty;
			}

			$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);

			$carac_client=pdf_build_address($outputlangs,$this->emetteur,$object->thirdparty,($usecontact?$object->contact:''),$usecontact,'target',$object);

			// Show recipient
			$widthrecbox=100;
			if ($this->page_largeur < 210) $widthrecbox=84;	// To work with US executive format
			$posy=42;
			$posx=$this->page_largeur-$this->marge_droite-$widthrecbox;
			if (! empty($conf->global->MAIN_INVERT_SENDER_RECIPIENT)) $posx=$this->marge_gauche;
/*
			// Show recipient frame
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($posx+2,$posy-5);
			$pdf->MultiCell($widthrecbox, 5, $outputlangs->transnoentities("BillTo").":",0,'L');
			$pdf->Rect($posx, $posy, $widthrecbox, $hautcadre);

			// Show recipient name
			$pdf->SetXY($posx+2,$posy+3);
			$pdf->SetFont('','B', $default_font_size);
			$pdf->MultiCell($widthrecbox, 4, $carac_client_name, 0, 'L');

			$posy = $pdf->getY();

			// Show recipient information
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->SetXY($posx+2,$posy);
			$pdf->MultiCell($widthrecbox, 4, $carac_client, 0, 'L');
*/
		}
	}


	/*
	 *  Show top header of page.
	 *
	 *  @param	PDF			$pdf     		Object PDF
	 *  @param  Object		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @return	void
	 */


	/*function _courrier(&$pdf, $object, $showaddress, $outputlangs)

	{

		global $conf,$langs,$mysoc;

		$outputlangs->load("main");
		$outputlangs->load("bills");
		$outputlangs->load("propal");
		$outputlangs->load("companies");
		$outputlangs->load("btp@btp");
		$outputlangs->load("delegation@delegation");

		$default_font_size = pdf_getPDFFontSize($outputlangs);

			// If BILLING contact defined on invoice, we use it
			$usecontact=false;
			$arrayidcontact=$object->getIdContact('internal','BILLING');
			if (count($arrayidcontact) > 0)
			{
				$usecontact=true;
				$result=$object->fetch_contact($arrayidcontact[0]);
			}

			//Recipient name
			// On peut utiliser le nom de la societe du contact
			if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) {
				$thirdparty = $object->contact;
			} else {
				$thirdparty = $mysoc;
			}

			$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);

			$carac_client=pdf_build_address($outputlangs,$this->emetteur,$mysoc,((!empty($object->contact))?$object->contact:null),$usecontact,'target',$object);


		include DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/doc/contrat-sous-traitance-btp-simplifie-2014/contrat-sous-traitance-btp-simplifie-2014.php';


		//Corps de la lettre
				$pdf->setXY(10,100); // fixe les positions x et y courantes
				$pdf->SetFont('','',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(190,4, 10, 90, dol_htmlentitiesbr($courrier_delegation),0,1);

	}*/


		/**
	 *   	Show footer of page. Need this->emetteur object
     *
	 *   	@param	PDF			$pdf     			PDF
	 * 		@param	CommandeFournisseur		$object				Object to show
	 *      @param	Translate	$outputlangs		Object lang for output
	 *      @param	int			$hidefreetext		1=Hide free text
	 *      @return	int								Return height of bottom margin including footer text
	 */
	function _pagefoot(&$pdf, $object, $outputlangs, $hidefreetext=0)
	{
		global $conf;
		$showdetails=$conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS;
		return pdf_pagefoot($pdf,$outputlangs,'',$this->emetteur,$this->marge_basse,$this->marge_gauche,$this->page_hauteur,$object,$showdetails,$hidefreetext);
	}

}

