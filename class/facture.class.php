<?php
/* Copyright (C) 2002-2007  Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2004       Sebastien Di Cintio     <sdicintio@ressource-toi.org>
 * Copyright (C) 2004       Benoit Mortier          <benoit.mortier@opensides.be>
 * Copyright (C) 2005       Marc Barilley / Ocebo   <marc@ocebo.com>
 * Copyright (C) 2005-2014  Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2006       Andre Cianfarani        <acianfa@free.fr>
 * Copyright (C) 2007       Franky Van Liedekerke   <franky.van.liedekerke@telenet.be>
 * Copyright (C) 2010-2020  Juanjo Menent           <jmenent@2byte.es>
 * Copyright (C) 2012-2014  Christophe Battarel     <christophe.battarel@altairis.fr>
 * Copyright (C) 2012-2015  Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2012       Cédric Salvador         <csalvador@gpcsolutions.fr>
 * Copyright (C) 2012-2014  Raphaël Doursenaud      <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2013       Cedric Gross            <c.gross@kreiz-it.fr>
 * Copyright (C) 2013       Florian Henry           <florian.henry@open-concept.pro>
 * Copyright (C) 2016-2025  Ferran Marcet           <fmarcet@2byte.es>
 * Copyright (C) 2018-2024  Alexandre Spangaro      <alexandre@inovea-conseil.com>
 * Copyright (C) 2018       Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2022       Sylvain Legrand         <contact@infras.fr>
 * Copyright (C) 2023      	Gauthier VERDOL       	<gauthier.verdol@atm-consulting.fr>
 * Copyright (C) 2023		Nick Fragoulis
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France             <frederic.france@free.fr>
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/compta/facture/class/facture.class.php
 *	\ingroup    invoice
 *	\brief      File of class to manage invoices
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commoninvoice.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobjectline.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/client.class.php';
require_once DOL_DOCUMENT_ROOT.'/margin/lib/margins.lib.php';
require_once DOL_DOCUMENT_ROOT.'/multicurrency/class/multicurrency.class.php';

if (isModEnabled('accounting')) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formaccounting.class.php';
}
if (isModEnabled('accounting')) {
	require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
}

/**
 *	Class to manage invoices
 */
class Facture extends CommonInvoice
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'facture';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'facture';

	/**
	 * @var string    Name of subtable line
	 */
	public $table_element_line = 'facturedet';

	/**
	 * @var string Name of class line
	 */
	public $class_element_line = 'FactureLigne';

	/**
	 * @var string Fieldname with ID of parent key if this field has a parent
	 */
	public $fk_element = 'fk_facture';

	/**
	 * @var string String with name of icon for myobject.
	 */
	public $picto = 'bill';

	/**
	 * 0=Default, 1=View may be restricted to sales representative only if no permission to see all or to company of external user if external user
	 * @var integer
	 */
	public $restrictiononfksoc = 1;

	/**
	 * {@inheritdoc}
	 */
	protected $table_ref_field = 'ref';

	/**
	 * @var int ID
	 * @deprecated		Use $user_creation_id
	 */
	public $fk_user_author;

	/**
	 * @var int|null ID
	 * @deprecated		Use $user_validation_id
	 */
	public $fk_user_valid;

	/**
	 * @var int ID
	 * @deprecated		Use $user_modification_id
	 */
	public $fk_user_modif;


	public $datem;

	/**
	 * @var int	Date expected for delivery
	 */
	public $delivery_date; // Date expected of shipment (date of start of shipment, not the reception that occurs some days after)

	/**
	 * @var string customer ref
	 * @deprecated
	 * @see $ref_customer
	 */
	public $ref_client;

	/**
	 * @var string customer ref
	 */
	public $ref_customer;

	public $total_ht;
	public $total_tva;
	public $total_localtax1;
	public $total_localtax2;
	public $total_ttc;
	public $revenuestamp;

	public $resteapayer;

	/**
	 * 1 if invoice paid COMPLETELY, 0 otherwise (do not use it anymore, use statut and close_code)
	 */
	public $paye;

	//! key of module source when invoice generated from a dedicated module ('cashdesk', 'takepos', ...)
	public $module_source;
	//! key of pos source ('0', '1', ...)
	public $pos_source;
	//! id of template invoice when generated from a template invoice
	public $fk_fac_rec_source;
	//! id of source invoice if replacement invoice or credit note
	public $fk_facture_source;
	public $linked_objects = array();

	/**
	 * @var int ID Field to store bank id to use when payment mode is withdraw
	 */
	public $fk_bank;

	/**
	 * @var CommonInvoiceLine[]
	 */
	public $lines = array();

	/**
	 * @var FactureLigne
	 */
	public $line;
	public $extraparams = array();

	/**
	 * @var int ID facture rec
	 */
	public $fac_rec;

	public $date_pointoftax;


	/**
	 * @var int Situation cycle reference number
	 */
	public $situation_cycle_ref;

	/**
	 * @var int Situation counter inside the cycle
	 */
	public $situation_counter;

	/**
	 * @var int Final situation flag
	 */
	public $situation_final;

	/**
	 * @var array Table of previous situations
	 */
	public $tab_previous_situation_invoice = array();

	/**
	 * @var array Table of next situations
	 */
	public $tab_next_situation_invoice = array();

	/**
	 * @var static object oldcopy
	 */
	public $oldcopy;

	/**
	 * @var double percentage of retainage
	 */
	public $retained_warranty;

	/**
	 * @var int timestamp of date limit of retainage
	 */
	public $retained_warranty_date_limit;

	/**
	 * @var int Code in llx_c_paiement
	 */
	public $retained_warranty_fk_cond_reglement;

	/**
	 * @var int availability ID
	 */
	public $availability_id;

	public $date_closing;

	/**
	 * @var int
	 */
	public $source;

	/**
	 * @var float	Percent of discount ("remise" in French)
	 * @deprecated The discount percent is on line level now
	 */
	public $remise_percent;

	/**
	 * @var string payment url
	 */
	public $online_payment_url;


	/**
	 * 	Constructor
	 *
	 * 	@param	DoliDB		$db			Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;

		$this->ismultientitymanaged = 1;
		$this->isextrafieldmanaged = 1;
	}

		/**
	 *	Get object from database. Get also lines.
	 *
	 *	@param      int		$rowid       		Id of object to load
	 * 	@param		string	$ref				Reference of invoice
	 * 	@param		string	$ref_ext			External reference of invoice
	 * 	@param		int		$notused			Not used
	 *  @param		bool	$fetch_situation	Load also the previous and next situation invoice into $tab_previous_situation_invoice and $tab_next_situation_invoice
	 *	@return     int         				>0 if OK, <0 if KO, 0 if not found
	 */
	public function fetch($rowid, $ref = '', $ref_ext = '', $notused = 0, $fetch_situation = false)
	{
		if (empty($rowid) && empty($ref) && empty($ref_ext)) {
			return -1;
		}

		$sql = 'SELECT f.rowid, f.entity, f.ref, f.ref_client, f.ref_ext, f.type, f.subtype, f.fk_soc';
		$sql .= ', f.total_tva, f.localtax1, f.localtax2, f.total_ht, f.total_ttc, f.revenuestamp';
		$sql .= ', f.datef as df, f.date_pointoftax';
		$sql .= ', f.date_lim_reglement as dlr';
		$sql .= ', f.datec as datec';
		$sql .= ', f.date_valid as datev';
		$sql .= ', f.tms as datem';
		$sql .= ', f.note_private, f.note_public, f.fk_statut as status, f.paye, f.close_code, f.close_note, f.fk_user_author, f.fk_user_valid, f.fk_user_modif, f.model_pdf, f.last_main_doc';
		$sql .= ', f.fk_facture_source, f.fk_fac_rec_source';
		$sql .= ', f.fk_mode_reglement, f.fk_cond_reglement, f.fk_projet as fk_project, f.extraparams';
		$sql .= ', f.situation_cycle_ref, f.situation_counter, f.situation_final';
		$sql .= ', f.fk_account';
		$sql .= ", f.fk_multicurrency, f.multicurrency_code, f.multicurrency_tx, f.multicurrency_total_ht, f.multicurrency_total_tva, f.multicurrency_total_ttc";
		$sql .= ', p.code as mode_reglement_code, p.libelle as mode_reglement_libelle';
		$sql .= ', c.code as cond_reglement_code, c.libelle as cond_reglement_libelle, c.libelle_facture as cond_reglement_libelle_doc';
		$sql .= ', f.fk_incoterms, f.location_incoterms';
		$sql .= ', f.module_source, f.pos_source';
		$sql .= ", i.libelle as label_incoterms";
		$sql .= ", f.retained_warranty as retained_warranty, f.retained_warranty_date_limit as retained_warranty_date_limit, f.retained_warranty_fk_cond_reglement as retained_warranty_fk_cond_reglement";
		$sql .= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_payment_term as c ON f.fk_cond_reglement = c.rowid';
		$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as p ON f.fk_mode_reglement = p.id';
		$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON f.fk_incoterms = i.rowid';

		if ($rowid) {
			$sql .= " WHERE f.rowid = ".((int) $rowid);
		} else {
			$sql .= ' WHERE f.entity IN ('.getEntity('invoice').')'; // Don't use entity if you use rowid
			if ($ref) {
				$sql .= " AND f.ref = '".$this->db->escape($ref)."'";
			}
			if ($ref_ext) {
				$sql .= " AND f.ref_ext = '".$this->db->escape($ref_ext)."'";
			}
		}

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				$this->entity = $obj->entity;

				$this->ref					= $obj->ref;
				$this->ref_client			= $obj->ref_client;
				$this->ref_customer			= $obj->ref_client;
				$this->ref_ext				= $obj->ref_ext;
				$this->type					= $obj->type;
				$this->subtype				= $obj->subtype;
				$this->date					= $this->db->jdate($obj->df);
				$this->date_pointoftax		= $this->db->jdate($obj->date_pointoftax);
				$this->date_creation        = $this->db->jdate($obj->datec);
				$this->date_validation		= $this->db->jdate($obj->datev);
				$this->date_modification    = $this->db->jdate($obj->datem);
				$this->datem                = $this->db->jdate($obj->datem);
				$this->total_ht				= $obj->total_ht;
				$this->total_tva			= $obj->total_tva;
				$this->total_localtax1		= $obj->localtax1;
				$this->total_localtax2		= $obj->localtax2;
				$this->total_ttc			= $obj->total_ttc;
				$this->revenuestamp         = $obj->revenuestamp;
				$this->paye                 = $obj->paye;
				$this->close_code			= $obj->close_code;
				$this->close_note			= $obj->close_note;

				$this->socid = $obj->fk_soc;
				$this->thirdparty = null; // Clear if another value was already set by fetch_thirdparty

				$this->fk_project = $obj->fk_project;
				$this->project = null; // Clear if another value was already set by fetch_projet

				$this->statut = $obj->status;	// deprecated
				$this->status = $obj->status;

				$this->date_lim_reglement = $this->db->jdate($obj->dlr);
				$this->mode_reglement_id	= $obj->fk_mode_reglement;
				$this->mode_reglement_code	= $obj->mode_reglement_code;
				$this->mode_reglement		= $obj->mode_reglement_libelle;
				$this->cond_reglement_id	= $obj->fk_cond_reglement;
				$this->cond_reglement_code	= $obj->cond_reglement_code;
				$this->cond_reglement		= $obj->cond_reglement_libelle;
				$this->cond_reglement_doc = $obj->cond_reglement_libelle_doc;
				$this->fk_account = ($obj->fk_account > 0) ? $obj->fk_account : null;
				$this->fk_facture_source	= $obj->fk_facture_source;
				$this->fk_fac_rec_source	= $obj->fk_fac_rec_source;
				$this->note = $obj->note_private; // deprecated
				$this->note_private = $obj->note_private;
				$this->note_public			= $obj->note_public;
				$this->user_creation_id     = $obj->fk_user_author;
				$this->user_validation_id   = $obj->fk_user_valid;
				$this->user_modification_id = $obj->fk_user_modif;
				$this->fk_user_author       = $obj->fk_user_author;
				$this->fk_user_valid        = $obj->fk_user_valid;
				$this->fk_user_modif        = $obj->fk_user_modif;
				$this->model_pdf = $obj->model_pdf;
				$this->last_main_doc = $obj->last_main_doc;
				$this->situation_cycle_ref  = $obj->situation_cycle_ref;
				$this->situation_counter    = $obj->situation_counter;
				$this->situation_final      = $obj->situation_final;
				$this->retained_warranty    = $obj->retained_warranty;
				$this->retained_warranty_date_limit         = $this->db->jdate($obj->retained_warranty_date_limit);
				$this->retained_warranty_fk_cond_reglement  = $obj->retained_warranty_fk_cond_reglement;

				$this->extraparams = !empty($obj->extraparams) ? (array) json_decode($obj->extraparams, true) : array();

				//Incoterms
				$this->fk_incoterms         = $obj->fk_incoterms;
				$this->location_incoterms   = $obj->location_incoterms;
				$this->label_incoterms = $obj->label_incoterms;

				$this->module_source = $obj->module_source;
				$this->pos_source = $obj->pos_source;

				// Multicurrency
				$this->fk_multicurrency 		= $obj->fk_multicurrency;
				$this->multicurrency_code = $obj->multicurrency_code;
				$this->multicurrency_tx 		= $obj->multicurrency_tx;
				$this->multicurrency_total_ht = $obj->multicurrency_total_ht;
				$this->multicurrency_total_tva 	= $obj->multicurrency_total_tva;
				$this->multicurrency_total_ttc 	= $obj->multicurrency_total_ttc;


				// Retrieve all extrafield
				// fetch optionals attributes and labels
				$this->fetch_optionals();


				return 1;
			} else {
				$this->error = 'Invoice with id='.$rowid.' or ref='.$ref.' or ref_ext='.$ref_ext.' not found';

				dol_syslog(__METHOD__.$this->error, LOG_WARNING);
				return 0;
			}
		} else {
			$this->error = $this->db->lasterror();
			return -1;
		}
	}


	/**
	 *  Send reminders by emails for invoices validated that are due.
	 *  CAN BE A CRON TASK
	 *
	 *  @param	int			$nbdays				Delay before due date (or after if delay is negative)
	 *  @param	string		$paymentmode		'' or 'all' by default (no filter), or 'LIQ', 'CHQ', CB', ...
	 *  @param	int|string	$template			Name (or id) of email template (Must be a template of type 'facture_send')
	 *  @param	string		$datetouse			'duedate' (default) or 'invoicedate'
	 *  @param	string		$forcerecipient		Force email of recipient (for example to send the email to an accountant supervisor instead of the customer)
	 *  @return int         					0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	public function sendEmailsNotificationOnInvoiceDate($nbdays = 0, $paymentmode = 'all', $template = '', $datetouse = 'invoicedate', $forcerecipient = '')
	{
		global $conf, $langs, $user;

		$error = 0;
		$this->output = '';
		$this->error = '';
		$nbMailSend = 0;
		$errorsMsg = array();

		$langs->load("bills", "facture@delegation");

		if (!isModEnabled('invoice')) {	// Should not happen. If module disabled, cron job should not be visible.
			$this->output .= $langs->trans('ModuleNotEnabled', $langs->transnoentitiesnoconv("Facture"));
			return 0;
		}
		if (!in_array($datetouse, array('duedate', 'invoicedate'))) {
			$this->output .= 'Bad value for parameter datetouse. Must be "duedate" or "invoicedate"';
			return 0;
		}
		/*if (empty($conf->global->FACTURE_REMINDER_EMAIL)) {
			$langs->load("bills");
			$this->output .= $langs->trans('EventRemindersByEmailNotEnabled', $langs->transnoentitiesnoconv("Facture"));
			return 0;
		}
		*/

		require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
		require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
		$formmail = new FormMail($this->db);

		$now = dol_now();
		$tmpidate = dol_get_first_hour(dol_time_plus_duree($now, $nbdays, 'd'), 'gmt');

		$tmpinvoice = new Facture($this->db);

		dol_syslog(__METHOD__." start", LOG_INFO);

		// Select all action comm reminder
		$sql = "SELECT rowid as id FROM ".MAIN_DB_PREFIX."facture as f";

		if (!empty($paymentmode) && $paymentmode != 'all') {
			$sql .= ", ".MAIN_DB_PREFIX."c_paiement as cp";
		}
		$sql .= " WHERE f.paye = 0";	// Only unpaid
		$sql .= " AND f.fk_statut = ".self::STATUS_VALIDATED;	// Only validated status
		if ($datetouse == 'invoicedate') {
			$sql .= " AND f.datef = '".$this->db->idate($tmpidate, 'gmt')."'";
		} else {
			$sql .= " AND f.date_lim_reglement = '".$this->db->idate($tmpidate, 'gmt')."'";
		}
		$sql .= " AND f.entity IN (".getEntity('facture', 0).")";	// One batch process only one company (no sharing)
		if (!empty($paymentmode) && $paymentmode != 'all') {
			$sql .= " AND f.fk_mode_reglement = cp.id AND cp.code = '".$this->db->escape($paymentmode)."'";
		}

		// TODO Add a filter to check there is no payment started yet
		if ($datetouse == 'invoicedate') {
			$sql .= $this->db->order("datef", "ASC");
		} else {
			$sql .= $this->db->order("date_lim_reglement", "ASC");
		}

		$resql = $this->db->query($sql);

		$stmpidate = dol_print_date($tmpidate, 'day', 'gmt');
		if ($datetouse == 'invoicedate') {
			$this->output .= $langs->transnoentitiesnoconv("SearchValidatedInvoicesWithDate", $stmpidate);
		} else {
			$this->output .= $langs->transnoentitiesnoconv("SearchUnpaidInvoicesWithDueDate", $stmpidate);
		}
		if (!empty($paymentmode) && $paymentmode != 'all') {
			$this->output .= ' ('.$langs->transnoentitiesnoconv("PaymentMode").' '.$paymentmode.')';
		}
		$this->output .= '<br>';

		if ($resql) {
			while ($obj = $this->db->fetch_object($resql)) {
				if (!$error) {
					// Load event
					$res = $tmpinvoice->fetch($obj->id);

					if ($res > 0) {
						$tmpinvoice->fetch_thirdparty();
						$tmpinvoice->fetch_optionals();

						$active = $tmpinvoice->array_options['options_lmdb_envoi_auto'];
						if ($active==1) {
							
							$template = $tmpinvoice->array_options['options_lmdb_template'];
						
							$outputlangs = new Translate('', $conf);
							if ($tmpinvoice->thirdparty->default_lang) {
								$outputlangs->setDefaultLang($tmpinvoice->thirdparty->default_lang);
								$outputlangs->loadLangs(array("main", "bills", "facture@delegation"));
							} else {
								$outputlangs = $langs;
							}

							// Select email template according to language of recipient
							$arraymessage = $formmail->getEMailTemplate($this->db, 'facture_send', $user, $outputlangs, (is_numeric($template) ? $template : 0), 1, (is_numeric($template) ? '' : $template));
							if (is_numeric($arraymessage) && $arraymessage <= 0) {
								$langs->load("errors");
								$this->output .= $langs->trans('ErrorFailedToFindEmailTemplate', $template);
								return 0;
							}

							// PREPARE EMAIL
							$errormesg = '';

							// Make substitution in email content
							$substitutionarray = getCommonSubstitutionArray($outputlangs, 0, '', $tmpinvoice);

							complete_substitutions_array($substitutionarray, $outputlangs, $tmpinvoice);

							// Topic
							$sendTopic = make_substitutions(empty($arraymessage->topic) ? $outputlangs->transnoentitiesnoconv('InformationMessage') : $arraymessage->topic, $substitutionarray, $outputlangs, 1);

							// Content
							$content = $outputlangs->transnoentitiesnoconv($arraymessage->content);

							$sendContent = make_substitutions($content, $substitutionarray, $outputlangs, 1);

							// Recipient
							$to = array();
							if ($forcerecipient) {	// If a recipient was forced
								$to = array($forcerecipient);
							} else {
								$res = $tmpinvoice->fetch_thirdparty();
								$recipient = $tmpinvoice->thirdparty;
								if ($res > 0) {
									$tmparraycontact = $tmpinvoice->liste_contact(-1, 'external', 0, 'BILLING');
									if (is_array($tmparraycontact) && count($tmparraycontact) > 0) {
										foreach ($tmparraycontact as $data_email) {
											if (!empty($data_email['email'])) {
												$to[] = $tmpinvoice->thirdparty->contact_get_property($data_email['id'], 'email');
											}
										}
									}
									if (empty($to) && !empty($recipient->email)) {
										$to[] = $recipient->email;
									}
									if (empty($to)) {
										$errormesg = "Failed to send remind to thirdparty id=".$tmpinvoice->socid.". No email defined for invoice or customer.";
										$error++;
									}
								} else {
									$errormesg = "Failed to load recipient with thirdparty id=".$tmpinvoice->socid;
									$error++;
								}
							}

							// Sender
							$from = getDolGlobalString('MAIN_MAIL_EMAIL_FROM');
							if (!empty($arraymessage->email_from)) {	// If a sender is defined into template, we use it in priority
								$from = $arraymessage->email_from;
							}
							if (empty($from)) {
								$errormesg = "Failed to get sender into global setup MAIN_MAIL_EMAIL_FROM";
								$error++;
							}

							if (!$error && !empty($to)) {
								$this->db->begin();

								$to = implode(',', $to);
								if (!empty($arraymessage->email_to)) {	// If a recipient is defined into template, we add it
									$to = $to.','.$arraymessage->email_to;
								}

								// Errors Recipient
								$errors_to = getDolGlobalString('MAIN_MAIL_ERRORS_TO');

								$trackid = 'inv'.$tmpinvoice->id;
								$sendcontext = 'standard';

								$email_tocc = '';
								if (!empty($arraymessage->email_tocc)) {	// If a CC is defined into template, we use it
									$email_tocc = $arraymessage->email_tocc;
								}

								$email_tobcc = '';
								if (!empty($arraymessage->email_tobcc)) {	// If a BCC is defined into template, we use it
									$email_tobcc = $arraymessage->email_tobcc;
								}

								//join file is asked
								$joinFile = [];
								$joinFileName = [];
								$joinFileMime = [];
								if ($arraymessage->joinfiles == 1 && !empty($tmpinvoice->last_main_doc)) {
									$joinFile[] = DOL_DATA_ROOT.'/'.$tmpinvoice->last_main_doc;
									$joinFileName[] = basename($tmpinvoice->last_main_doc);
									$joinFileMime[] = dol_mimetype(DOL_DATA_ROOT.'/'.$tmpinvoice->last_main_doc);
								}

								// Mail Creation
								$cMailFile = new CMailFile($sendTopic, $to, $from, $sendContent, $joinFile, $joinFileMime, $joinFileName, $email_tocc, $email_tobcc, 0, 1, $errors_to, '', $trackid, '', $sendcontext, '');

								// Sending Mail
								if ($cMailFile->sendfile()) {
									$nbMailSend++;

									// Add a line into event table
									require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

									// Insert record of emails sent
									$actioncomm = new ActionComm($this->db);

									$actioncomm->type_code = 'AC_OTH_AUTO'; // Event insert into agenda automatically
									$actioncomm->socid = $tmpinvoice->thirdparty->id; // To link to a company
									$actioncomm->contact_id = 0;

									$actioncomm->code = 'AC_EMAIL';
									$actioncomm->label = 'sendEmailsRemindersOnInvoiceDueDateOK (nbdays='.$nbdays.' paymentmode='.$paymentmode.' template='.$template.' datetouse='.$datetouse.' forcerecipient='.$forcerecipient.')';
									$actioncomm->note_private = $sendContent;
									$actioncomm->fk_project = $tmpinvoice->fk_project;
									$actioncomm->datep = dol_now();
									$actioncomm->datef = $actioncomm->datep;
									$actioncomm->percentage = -1; // Not applicable
									$actioncomm->authorid = $user->id; // User saving action
									$actioncomm->userownerid = $user->id; // Owner of action
									// Fields when action is an email (content should be added into note)
									$actioncomm->email_msgid = $cMailFile->msgid;
									$actioncomm->email_subject = $sendTopic;
									$actioncomm->email_from = $from;
									$actioncomm->email_sender = '';
									$actioncomm->email_to = $to;
									//$actioncomm->email_tocc = $sendtocc;
									//$actioncomm->email_tobcc = $sendtobcc;
									//$actioncomm->email_subject = $subject;
									$actioncomm->errors_to = $errors_to;

									$actioncomm->elementtype = 'invoice';
									$actioncomm->fk_element = $tmpinvoice->id;

									//$actioncomm->extraparams = $extraparams;

									$actioncomm->create($user);
								} else {
									$errormesg = $cMailFile->error.' : '.$to;
									$error++;

									// Add a line into event table
									require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

									// Insert record of emails sent
									$actioncomm = new ActionComm($this->db);

									$actioncomm->type_code = 'AC_OTH_AUTO'; // Event insert into agenda automatically
									$actioncomm->socid = $tmpinvoice->thirdparty->id; // To link to a company
									$actioncomm->contact_id = 0;

									$actioncomm->code = 'AC_EMAIL';
									$actioncomm->label = 'sendEmailsRemindersOnInvoiceDueDateKO';
									$actioncomm->note_private = $errormesg;
									$actioncomm->fk_project = $tmpinvoice->fk_project;
									$actioncomm->datep = dol_now();
									$actioncomm->datef = $actioncomm->datep;
									$actioncomm->percentage = -1; // Not applicable
									$actioncomm->authorid = $user->id; // User saving action
									$actioncomm->userownerid = $user->id; // Owner of action
									// Fields when action is an email (content should be added into note)
									$actioncomm->email_msgid = $cMailFile->msgid;
									$actioncomm->email_from = $from;
									$actioncomm->email_sender = '';
									$actioncomm->email_to = $to;
									//$actioncomm->email_tocc = $sendtocc;
									//$actioncomm->email_tobcc = $sendtobcc;
									//$actioncomm->email_subject = $subject;
									$actioncomm->errors_to = $errors_to;

									//$actioncomm->extraparams = $extraparams;

									$actioncomm->create($user);
								}

								$this->db->commit();	// We always commit
							}

							if ($errormesg) {
								$errorsMsg[] = $errormesg;
							}
						}
					} else {
						$errorsMsg[] = 'Failed to fetch record invoice with ID = '.$obj->id;
						$error++;
					}
				}
			}
		} else {
			$error++;
		}

		if (!$error) {
			$this->output .= 'Nb d\'emails envoyé : '.$nbMailSend;

			dol_syslog(__METHOD__." end - ".$this->output, LOG_INFO);

			return 0;
		} else {
			$this->error = 'Nb d\'emails envoyé : '.$nbMailSend.', '.(!empty($errorsMsg) ? implode(', ', $errorsMsg) : $error);

			dol_syslog(__METHOD__." end - ".$this->error, LOG_INFO);

			return $error;
		}
	}
}