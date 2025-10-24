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
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';


/**
 *	Class to generate the supplier orders with the Délégation INPOSE model
 */
class pdf_DC42017 extends ModelePDFSuppliersOrders
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
		$this->name = "DC4 2017";
		$this->description = $langs->trans('SuppliersDC4ModelLMDB');

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
				$file = $dir . "/" . $objectref . " - Declaration de Sous-Traitant - DC4.pdf";
				if (! empty($conf->global->SUPPLIER_REF_IN_NAME)) $file = $dir . "/" . $objectref . " - Declaration de Sous-Traitant - DC4.pdf";
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
/*					
					if (! empty($tplidx)) $pdf->useTemplate($tplidx);
					$this->_pagehead($pdf, $object, 1, $outputlangs);
					$pdf->SetFont('','', $default_font_size - 1);
					$pdf->MultiCell(0, 3, '');		// Set interline to 3
					$pdf->SetTextColor(0,0,0);


					$this->_courrier($pdf, $object, 1, $outputlangs);

					
					$this->_pagefoot($pdf,$object,$outputlangs,1);
*/

			// Page 1
				$pdf->AddPage();
				
                $pagecount = $pdf->setSourceFile(DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/doc/DC4-2017/DC4-2017.pdf');
                $tplidx = $pdf->importPage(1);
                
				
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);


				include DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/doc/DC4-2017/DC4-2017.php';

				$logo=$conf->mycompany->dir_output.'/logos/'.$this->emetteur->logo;

				$height=pdf_getHeightForLogo($logo);
				$pdf->Image($logo, 10, 10, "", 10);

				$this->_pagefoot($pdf,$object,$outputlangs,1);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu
					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(190,4, 50, 125, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Client)),0,1);

					$pdf->writeHTMLCell(190,4, 50, 165, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Representant)),0,1);	


					$pdf->writeHTMLCell(190,4, 50, 215, $outputlangs->convToOutputCharset($Travaux),0,1);


				if ($dc4_line->dc4_object_declaration == '1') {
					
					$pdf->writeHTMLCell(190,4, 20.3, 248.5, dol_htmlentitiesbr("X"),0,1);

				} elseif ($dc4_line->dc4_object_declaration == '2') {
					
					$pdf->writeHTMLCell(190,4, 20.3, 256.5, dol_htmlentitiesbr("X"),0,1);

				}elseif ($dc4_line->dc4_object_declaration == '3') {
					
					$pdf->writeHTMLCell(190,4, 20.3, 264.5, dol_htmlentitiesbr("X"),0,1);

					$pdf->writeHTMLCell(190,4, 160, 264, dol_print_date($dc4_line->dc4_date_initiale,"day",false,$outputlangs),0,1);

				}
				
					


			// Page 2
				$pdf->AddPage();

				$tplidx = $pdf->importPage(2);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu
					
					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					
					$pdf->writeHTMLCell(190,4, 25, 69, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Societe)),0,1);

					$pdf->writeHTMLCell(190,4, 100, 69, dol_htmlentitiesbr($outputlangs->convToOutputCharset($SIREN_Societe)),0,1);

					$pdf->writeHTMLCell(190,4, 25, 96, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Forme_Juridique_SousTraitant)),0,1);

					$pdf->writeHTMLCell(190,4, 25, 166, dol_htmlentitiesbr($outputlangs->convToOutputCharset($SousTraitant)),0,1);
					
					$pdf->writeHTMLCell(190,4, 100, 166, dol_htmlentitiesbr($outputlangs->convToOutputCharset($SIREN_SousTraitant)),0,1);

					$pdf->writeHTMLCell(190,4, 25, 194, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Forme_Juridique_SousTraitant)),0,1);

					$pdf->writeHTMLCell(190,4, 50, 218, dol_htmlentitiesbr($outputlangs->convToOutputCharset($Representant_SousTraitant)),0,1);

					if ($object->thirdparty->typent_id == '3') {
					
					$pdf->writeHTMLCell(190,4, 79.5, 240, dol_htmlentitiesbr("X"),0,1);

				} elseif ($object->thirdparty->typent_id == '4') {
					
					$pdf->writeHTMLCell(190,4, 79.5, 240, dol_htmlentitiesbr("X"),0,1);

				}elseif ($object->thirdparty->typent_id == '') {
					
					$pdf->writeHTMLCell(190,4, 79.5, 242, dol_htmlentitiesbr("Définissez le Type du tiers sur la fiche du Sous-Traitant"),0,1);

				}else {

					$pdf->writeHTMLCell(190,4, 117.3, 240, dol_htmlentitiesbr("X"),0,1);

				}

				if ($details_line->marche_defense == '1') {
					
					$pdf->writeHTMLCell(190,4, 80.5, 272, dol_htmlentitiesbr("X"),0,1);

				} elseif ($details_line->marche_defense == '2') {
					
					$pdf->writeHTMLCell(190,4, 110, 272, dol_htmlentitiesbr("X"),0,1);

				}


			// Page 3
				$pdf->AddPage();

				$tplidx = $pdf->importPage(3);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				//Nature des Travaux
				//$pdf->setXY(10,10); // fixe les positions x et y courantes
				//$pdf->SetFont('','',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				//$pdf->writeHTMLCell(190,4, 10, 10, dol_htmlentitiesbr($text3),0,1);

				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(190,4, 50, 60, $outputlangs->convToOutputCharset($Travaux),0,1);


				if ($details_line->marche_defense == '1') {
					
					$pdf->writeHTMLCell(190,4, 50, 75, dol_htmlentitiesbr($details_line->adresse_chantier),0,1);

				}

					$total_tva == $object->total_tva ;

					if ($total_tva == 0) {
					
						$pdf->writeHTMLCell(190,4, 60, 156.5, dol_htmlentitiesbr(price($object->total_ht)),0,1);

					}else{
					
						$pdf->writeHTMLCell(190,4, 60, 116, dol_htmlentitiesbr(price($object->total_tva)),0,1);
						$pdf->writeHTMLCell(190,4, 60, 122, dol_htmlentitiesbr(price($object->total_ht)),0,1);
						$pdf->writeHTMLCell(190,4, 60, 128, dol_htmlentitiesbr(price($object->total_ttc)),0,1);

					}


					$pdf->writeHTMLCell(190,4, 60, 186.5, dol_htmlentitiesbr("Aucune."),0,1);

					if ($dc4_line->paiement_direct == '1') {
					
						$pdf->writeHTMLCell(190,4, 80.5, 214.7, dol_htmlentitiesbr("X"),0,1);

					} elseif ($dc4_line->paiement_direct == '2') {
					
						$pdf->writeHTMLCell(190,4, 110, 214.7, dol_htmlentitiesbr("X"),0,1);

					}

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

					if ($dc4_line->avance == '1') {
					
						$pdf->writeHTMLCell(190,4, 119.4, 267, dol_htmlentitiesbr("X"),0,1);

					} elseif ($dc4_line->avance == '2') {
					
						$pdf->writeHTMLCell(190,4, 139, 267, dol_htmlentitiesbr("X"),0,1);

					}


			// Page 4
				$pdf->AddPage();

				$tplidx = $pdf->importPage(4);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$Adresse_Internet = 'Par courriel : '.$object->thirdparty->email.' / Par internet : '.$object->thirdparty->url.'';

					$pdf->writeHTMLCell(190,4, 60, 143.1, $outputlangs->convToOutputCharset($Adresse_Internet),0,1);

					$pdf->writeHTMLCell(190,4, 38.5, 213, dol_htmlentitiesbr("X"),0,1);


			// Page 5
				$pdf->AddPage();

				$tplidx = $pdf->importPage(5);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();


				//Contenu

					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					if ($dc4_line->dc4_object_declaration == '2') {
						
						$pdf->writeHTMLCell(190,4, 48.5, 61.5, dol_htmlentitiesbr("X"),0,1);

						if ($dc4_line->dc4_hypothese == '1') {
							
							$pdf->writeHTMLCell(190,4, 30.8, 91.7, dol_htmlentitiesbr("X"),0,1);

						} elseif ($dc4_line->dc4_hypothese == '2') {
							
							$pdf->writeHTMLCell(190,4, 30.8, 101.7, dol_htmlentitiesbr("X"),0,1);

						}

					}elseif ($dc4_line->dc4_object_declaration == '3') {
						
						$pdf->writeHTMLCell(190,4, 48.5, 116.1, dol_htmlentitiesbr("X"),0,1);

						if ($dc4_line->dc4_hypothese == '3') {
							
							$pdf->writeHTMLCell(190,4, 26.2, 132.8, dol_htmlentitiesbr("X"),0,1);

						} elseif ($dc4_line->dc4_hypothese == '4') {
							
							$pdf->writeHTMLCell(190,4, 26.2, 149.7, dol_htmlentitiesbr("X"),0,1);

						}

					}


			// Page 6
				$pdf->AddPage();

				$tplidx = $pdf->importPage(6);
                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				$pdf->Image($logo, 10, 10, "", 10);
				
				// Pied de page
					$this->_pagefoot($pdf, $object, $outputlangs);
					if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();

				//Contenu

					$pdf->SetFont('','B',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)

					$pdf->writeHTMLCell(30,4, 18, 73.5, $outputlangs->convToOutputCharset($object->thirdparty->town),0,1);
					$pdf->writeHTMLCell(190,4, 53, 73.5, $outputlangs->convToOutputCharset("......................"),0,1);

					$pdf->writeHTMLCell(30,4, 108, 73.5, $outputlangs->convToOutputCharset($conf->global->MAIN_INFO_SOCIETE_TOWN),0,1);
					$pdf->writeHTMLCell(190,4, 140, 73.5, $outputlangs->convToOutputCharset("......................"),0,1);


					$pdf->writeHTMLCell(30,4, 18, 135.1, $outputlangs->convToOutputCharset($company->town),0,1);
					$pdf->writeHTMLCell(190,4, 51, 135.1, $outputlangs->convToOutputCharset("......................"),0,1);


			// Page 7
				$pdf->AddPage();

				$tplidx = $pdf->importPage(7);
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


	/**
	 *  Show top header of page.
	 *
	 *  @param	PDF			$pdf     		Object PDF
	 *  @param  Object		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @return	void
	 */


	function _courrier(&$pdf, $object, $showaddress, $outputlangs)

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


		include DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/doc/DC4-2017/DC4-2017.php';


		//Corps de la lettre
				$pdf->setXY(10,100); // fixe les positions x et y courantes
				$pdf->SetFont('','',10); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(190,4, 10, 90, dol_htmlentitiesbr($courrier_delegation),0,1);

	}


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

