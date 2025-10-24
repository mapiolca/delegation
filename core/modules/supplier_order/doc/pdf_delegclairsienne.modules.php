<?php
/* Copyright (C) 2004-2014 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerke@telenet.be>
 * Copyright (C) 2010-2014 Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2015       Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2017      Ferran Marcet         <fmarcet@2byte.es>
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
 *	\file       htdocs/core/modules/supplier_order/pdf/pdf_delegeiffage.modules.php
 *	\ingroup    fournisseur
 *	\brief      File of class to generate suppliers orders from Délégation Eiffage model
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
 *	Class to generate the supplier orders with the Délégation Eiffage model
 */
class pdf_delegclairsienne extends ModelePDFSuppliersOrders
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
		$this->name = "Délégation Clairsienne V13.06";
		$this->description = $langs->trans('Modèle de Délégation de Paiement Clairisenne V13.06');

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
		$outputlangs->load("delegation");


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
				$file = $dir . "/" . $objectref . " - Délégation de Paiement Clairsienne.pdf";
				if (! empty($conf->global->SUPPLIER_REF_IN_NAME)) $file = $dir . "/" . $objectref . " - Délégation de Paiement Clairsienne.pdf";
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


			// Page 1
				$pdf->AddPage();

				include DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/pdf/delegclairsienne/delegclairsienne.php';

				$pagecount = $pdf->setSourceFile(DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/pdf/delegclairsienne/delegclairsienne.pdf');
                $tplidx = $pdf->importPage(1);

                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

				
				$pdf->writeHTMLCell(190,4, 35, 44.5, dol_htmlentitiesbr($refop),0,1);

				//Ref Marché
				$pdf->writeHTMLCell(190,4, 35, 51.5, dol_htmlentitiesbr($refmarche),0,1);

				//Entrepreneur Principal
				$pdf->writeHTMLCell(190,4, 42, 58.25, dol_htmlentitiesbr($entreprin),0,1);

				//Sous-Traitant
				$pdf->writeHTMLCell(190,4, 33, 65.1, dol_htmlentitiesbr($soustraitant),0,1);

				//Nommé l'Entrepreneur Principal
				$pdf->writeHTMLCell(190,4, 20, 93.75, dol_htmlentitiesbr($entreprin2),0,1);

				//Nommé le sous-traitant
				$pdf->writeHTMLCell(180,4, 20, 132.5, dol_htmlentitiesbr($fournisseur),0,1);

				//Nommé le Maitre d'ouvrage
				$pdf->writeHTMLCell(180,4, 20, 177.75, dol_htmlentitiesbr($client),0,1);

				//Préalablement
				$pdf->writeHTMLCell(180,4, 14, 225, dol_htmlentitiesbr($delegation),0,1);

				if ($dc4_line->dc4_object_declaration == '1') {
									
					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 155, 266.1, dol_htmlentitiesbr("___/___/____"),0,1);
					$pdf->writeHTMLCell(190,4, 160, 270, dol_htmlentitiesbr("Veuillez renseigner le formulaire Objet de la Consultation dans l'onglet DC4 de cette commande."),0,1);

				} elseif ($dc4_line->dc4_object_declaration == '2') {
					//Ref des Opérations
					$pdf->setXY(10,10); // fixe les positions x et y courantes
					$pdf->SetFont('','',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 24.8, 266.6, dol_htmlentitiesbr("X"),0,1);

					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 155, 266.1, dol_print_date($dc4_line->dc4_date_initiale,"day",false,$outputlangs),0,1);

				}elseif ($dc4_line->dc4_object_declaration == '3') {
					
					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 155, 266.1, dol_htmlentitiesbr("___/___/____"),0,1);

				}else{
					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 160, 270, dol_htmlentitiesbr("Veuillez renseigner le formulaire Objet de la Consultation dans l'onglet DC4 de cette commande."),0,1);
				}

			// Page 2
				$pdf->AddPage();

				$pagecount = $pdf->setSourceFile(DOL_DOCUMENT_ROOT.'/custom/delegation/core/modules/supplier_order/pdf/delegclairsienne/delegclairsienne.pdf');
                $tplidx = $pdf->importPage(2);

                if (! empty($tplidx)) $pdf->useTemplate($tplidx);

                if ($dc4_line->dc4_object_declaration == '1') {
									
					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 137, 25, dol_htmlentitiesbr("___/___/____"),0,1);
					$pdf->writeHTMLCell(190,4, 60, 30, dol_htmlentitiesbr("Veuillez renseigner le formulaire Objet de la Consultation dans l'onglet DC4 de cette commande."),0,1);

				} elseif ($dc4_line->dc4_object_declaration == '2') {

					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 136, 25.2, dol_htmlentitiesbr("___/___/____"),0,1);
				}elseif ($dc4_line->dc4_object_declaration == '3') {

					$pdf->setXY(10,10); // fixe les positions x et y courantes
					$pdf->SetFont('','',6); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 24.8, 25.7, dol_htmlentitiesbr("X"),0,1);
					
					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 136, 25.2, dol_print_date($dc4_line->dc4_date_initiale,"day",false,$outputlangs),0,1);

				}else{
					$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
					$pdf->writeHTMLCell(190,4, 60, 30, dol_htmlentitiesbr("Veuillez renseigner le formulaire Objet de la Consultation dans l'onglet DC4 de cette commande."),0,1);
				}

				//Nature des Travaux
				$pdf->setXY(10,10); // fixe les positions x et y courantes
				$pdf->SetFont('','',9); // fixe la police, le type ( 'B' pour gras, 'I' pour italique, '' pour normal,...)
				$pdf->writeHTMLCell(190,4, 160, 186.8, dol_htmlentitiesbr("Fait à ".$conf->global->MAIN_INFO_SOCIETE_TOWN),0,1);
				$pdf->writeHTMLCell(190,4, 168, 193.2, dol_print_date(dol_now(),"day",false,$outputlangs),0,1);


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

}

