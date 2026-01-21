<?php
/* Copyright (C) 2024	Pierre Ardoin		<developpeur@lesmetiersdubatiment.fr>
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
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
dol_include_once('/contrat/class/contrat.class.php');

class Actionsdelegation
{
	/**
	 * EN: Backup storage for PDF temporary notes.
	 * FR: Stockage de sauvegarde pour les notes PDF temporaires.
	 *
	 * @var array
	 */
	private $pdfNotePublicBackup = array();

	/**
	 * Constructor
	 *
	 * @param	DoliDB	$db	Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Overloading the getInputIdProf function
	 *
	 * @param	array		$parameters	Hook metadatas (context, etc...)
	 * @param	CommonObject	&$object	The object to process
	 * @param	string		&$action	Current action
	 * @param	HookManager	$hookmanager	Hook manager propagated to allow calling another hook
	 * @return	int				< 0 on error, 0 on success, 1 to replace standard code
	 */
	public function getInputIdProf($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;
		global $form;

		if (empty($conf->delegation->enabled)) {
			return 0;
		}

		if (is_array($parameters) && ! empty($parameters)) {
			foreach ($parameters as $key => $value) {
				$$key = $value;
			}
		}

		$currentcontext = explode(':', $parameters['currentcontext']);
		if (in_array('thirdpartycard', $currentcontext) && $idprof == 3 && isset($conf->global->LMDB_USE_IDPROF3_DICTIONARY)) {
			global $conf, $langs;
			$langs->load("dict");

			$htmlname = 'idprof'.$idprof;
			$out = "";

			$sql = "SELECT r.rowid, r.idprof3 as code, r.activity as label, r.active, c.code as country_code, c.label as country";
			$sql.= " FROM ".MAIN_DB_PREFIX."c_idprof3 as r, ".MAIN_DB_PREFIX."c_country as c";
			$sql.= " WHERE r.country_code=c.code";
			$sql.= " AND r.active = 1";
			$sql.= " AND c.active = 1";
			if ($country_code && ! is_numeric($country_code)) {
				$sql .= " AND c.code = '".$this->db->escape($country_code)."'";
			}
			$sql.= " ORDER BY r.country_code, code, label ASC";

			dol_syslog(get_class($this)."::select_idprof3", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql) {
				$morecss .= "maxwidth200onsmartphone maxwidth500";
				$moreattrib .= ' autocomplete="off"';
				$this->resprints = '<select class="flat'.($morecss ? ' '.$morecss : '').'"'.($moreattrib ? ' '.$moreattrib : '').' name="'.$htmlname.'" id="'.$htmlname.'" maxlength="'.$maxlength.'" value="'.$selected.'">';
				$num = $this->db->num_rows($resql);
				$i = 0;
				if ($num) {
					$country = '';
					$this->resprints.= '<option value="0">&nbsp;</option>';
					while ($i < $num) {
						$obj = $this->db->fetch_object($resql);
						if ($obj->code == 0) {
							$this->resprints.= '<option value="0">&nbsp;</option>';
						} else {
							if ($country_code == '' || $country_code != $obj->country_code) {
								$key = $langs->trans("Country".strtoupper($obj->country_code));
								$valuetoshow = ($key != "Country".strtoupper($obj->country_code)) ? $obj->country_code." - ".$key : $obj->country_code;
								$this->resprints.= '<option value="-1" disabled>----- '.$valuetoshow." -----</option>\n";
								$country = $obj->country;
							}

							if ($selected > 0 && $selected == $obj->code) {
								$this->resprints.= '<option value="'.$obj->code.'" selected>'.$obj->code.' - '.$obj->label.'</option>';
							} else {
								$this->resprints.= '<option value="'.$obj->code.'">'.$obj->code.' - '.$obj->label.'</option>';
							}
						}
						$i++;
					}
				}
				$this->resprints.= '</select>';

				$this->resprints.= '
					<script>
						$(document).ready(function () {
							$("#'.$htmlname.'").select2({
								dir: "ltr",
								width: "resolve",
								minimumInputLength: 0,
								language: select2arrayoflanguage,
								containerCssClass: ":all:",
								templateResult: function (data, container) {
									if (data.element) { $(container).addClass($(data.element).attr("class")); }
									if ($(data.element).attr("data-html") != undefined) return htmlEntityDecodeJs($(data.element).attr("data-html"));
									return data.text;
								},
								templateSelection: function (selection) {
									return selection.text;
								},
								escapeMarkup: function(markup) {
									return markup;
								},
								dropdownCssClass: "ui-dialog"
							});
						});
					</script>
				';

				if (! $error) {
					return 1;
				}

				$this->errors[] = 'Error message';
				return -1;
			}

			dol_print_error($this->db);
			return -1;
		}

		$this->resprints = "";
		return 0;
	}

	/**
	 * EN: Add VAT reverse charge options on supported cards.
	 * FR: Ajouter les options d'autoliquidation de TVA sur les fiches supportées.
	 *
	 * @param	array			$parameters	Hook parameters
	 * @param	CommonObject	&$object	Object to process
	 * @param	string			&$action	Current action
	 * @param	HookManager		$hookmanager	Hook manager
	 * @return	int							< 0 on error, 0 on success, 1 to replace standard code
	 */
	public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		return 0;
	}

	/**
	 * EN: Add VAT reverse charge action button with other actions.
	 * FR: Ajouter le bouton d'autoliquidation avec les autres actions.
	 *
	 * @param	array			$parameters	Hook parameters
	 * @param	CommonObject	&$object	Object to process
	 * @param	string			&$action	Current action
	 * @param	HookManager		$hookmanager	Hook manager
	 * @return	int							< 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		if (empty($conf->delegation->enabled)) {
			return 0;
		}

		if (! getDolGlobalInt('DELEGATION_ENABLE_VAT_REVERSE_CHARGE', 0)) {
			return 0;
		}

		$currentcontext = explode(':', $parameters['currentcontext']);
		if (! $this->isCustomerDocumentContext($currentcontext)) {
			return 0;
		}

		if (! $this->userCanWriteVatReverseCharge($user)) {
			return 0;
		}

		if ($action === 'edit' || $action === 'create') {
			return 0;
		}

		$langs->load("delegation@delegation");
		$status = $this->getVatReverseChargeStatus($object);

		if (! $status['active']) {
			return 0;
		}

		// EN: Render action button next to standard actions.
		// FR: Rendre le bouton d'action à côté des actions standard.
		$this->resprints .= $this->renderVatReverseChargeActionButton($object);

		return 0;
	}

	/**
	 * EN: Handle VAT reverse charge actions.
	 * FR: Gérer les actions liées à l'autoliquidation de TVA.
	 *
	 * @param	array			$parameters	Hook parameters
	 * @param	CommonObject	&$object	Object to process
	 * @param	string			&$action	Current action
	 * @param	HookManager		$hookmanager	Hook manager
	 * @return	int							< 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		if (empty($conf->delegation->enabled)) {
			return 0;
		}

		if (! getDolGlobalInt('DELEGATION_ENABLE_VAT_REVERSE_CHARGE', 0)) {
			return 0;
		}

		$currentcontext = explode(':', $parameters['currentcontext']);
		$isContractCard = in_array('contractcard', $currentcontext, true);
		$isDocumentCard = $this->isCustomerDocumentContext($currentcontext);

		if (! $isContractCard && ! $isDocumentCard) {
			return 0;
		}

		$langs->load("delegation@delegation");

		$canWriteVat = $this->userCanWriteVatReverseCharge($user);
		$canWriteContract = $this->userCanWriteSubcontractContract($user);

		// EN: Persist manual value on contract card.
		// FR: Enregistrer la valeur manuelle sur la fiche contrat.
		if ($isContractCard && GETPOSTISSET('options_delegation_subcontract_vat_reverse_charge') && $canWriteContract
			&& in_array($action, array('create', 'update', 'update_extras', 'setoptions'), true)) {
			if (method_exists($object, 'fetch_optionals')) {
				$object->fetch_optionals();
			}
			$object->array_options['options_delegation_subcontract_vat_reverse_charge'] = GETPOST('options_delegation_subcontract_vat_reverse_charge', 'int');
			if (method_exists($object, 'insertExtraFields')) {
				$object->insertExtraFields();
			}
		}

		// EN: Persist manual value on customer documents.
		// FR: Enregistrer la valeur manuelle sur les documents clients.
		if ($isDocumentCard && GETPOSTISSET('options_delegation_vat_reverse_charge') && $canWriteVat
			&& in_array($action, array('create', 'update', 'update_extras', 'setoptions'), true)) {
			if (method_exists($object, 'fetch_optionals')) {
				$object->fetch_optionals();
			}
			$object->array_options['options_delegation_vat_reverse_charge'] = GETPOST('options_delegation_vat_reverse_charge', 'int');
			if (method_exists($object, 'insertExtraFields')) {
				$object->insertExtraFields();
			}
		}

		if ($isDocumentCard && $action === 'delegation_apply_vat_reverse_charge') {
			if (! $canWriteVat) {
				setEventMessages($langs->trans('NotEnoughPermissions'), null, 'errors');
				return 0;
			}

			$result = $this->applyVatReverseChargeOnLines($object, $user);
			if ($result > 0) {
				setEventMessages($langs->trans('DelegationApplyVatReverseChargeDone'), null, 'mesgs');
			} else {
				setEventMessages($langs->trans('DelegationApplyVatReverseChargeError'), null, 'errors');
			}
		}

		// EN: Force VAT 0 on line add/update when configured.
		// FR: Forcer la TVA à 0 lors de l'ajout/modification de ligne si configuré.
		if ($isDocumentCard && getDolGlobalInt('DELEGATION_VAT_REVERSE_CHARGE_FORCE_VAT0', 0)) {
			if (in_array($action, array('addline', 'updateline'), true)) {
				$status = $this->getVatReverseChargeStatus($object);
				if ($status['active'] && $this->isLineInScopeFromPost()) {
					$_POST['tva_tx'] = 0;
					$_POST['vatrate'] = 0;
				}
			}
		}

		return 0;
	}

	/**
	 * EN: Inject VAT reverse charge legal mention into PDFs.
	 * FR: Injecter la mention légale d'autoliquidation dans les PDF.
	 *
	 * @param	array			$parameters	Hook parameters
	 * @param	CommonObject	&$object	Object to process
	 * @param	string			&$action	Current action
	 * @param	HookManager		$hookmanager	Hook manager
	 * @return	int							< 0 on error, 0 on success, 1 to replace standard code
	 */
	public function beforePDFCreation($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $langs;

		if (empty($conf->delegation->enabled)) {
			return 0;
		}

		if (! getDolGlobalInt('DELEGATION_ENABLE_VAT_REVERSE_CHARGE', 0)) {
			return 0;
		}

		if (! $this->isCustomerDocumentElement($object)) {
			return 0;
		}

		$status = $this->getVatReverseChargeStatus($object);
		if (! $status['active']) {
			return 0;
		}

		$langs->load("delegation@delegation");
		$legalText = $this->getVatReverseChargeLegalText($langs);
		if (empty($legalText)) {
			return 0;
		}

		$key = $object->element.'_'.$object->id;
		if (! array_key_exists($key, $this->pdfNotePublicBackup)) {
			$this->pdfNotePublicBackup[$key] = $object->note_public;
		}

		if (strpos($object->note_public, $legalText) === false) {
			$separator = empty($object->note_public) ? '' : "\n\n";
			$object->note_public .= $separator.$legalText;
		}

		return 0;
	}

	/**
	 * EN: Restore the PDF temporary notes.
	 * FR: Restaurer les notes temporaires des PDF.
	 *
	 * @param	array			$parameters	Hook parameters
	 * @param	CommonObject	&$object	Object to process
	 * @param	string			&$action	Current action
	 * @param	HookManager		$hookmanager	Hook manager
	 * @return	int							< 0 on error, 0 on success, 1 to replace standard code
	 */
	public function afterPDFCreation($parameters, &$object, &$action, $hookmanager)
	{
		$key = $object->element.'_'.$object->id;
		if (array_key_exists($key, $this->pdfNotePublicBackup)) {
			$object->note_public = $this->pdfNotePublicBackup[$key];
			unset($this->pdfNotePublicBackup[$key]);
		}

		return 0;
	}

	/**
	 * EN: Render action button for VAT reverse charge.
	 * FR: Générer le bouton d'action pour l'autoliquidation de TVA.
	 *
	 * @param	CommonObject	$object	Object to process
	 * @return	string
	 */
	private function renderVatReverseChargeActionButton($object)
	{
		global $langs;

		$url = $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delegation_apply_vat_reverse_charge&token='.newToken();
		$output = '<a class="butAction butActionSmall" href="'.dol_escape_htmltag($url).'">';
		$output .= $langs->trans('DelegationApplyVatReverseCharge');
		$output .= '</a>';

		return $output;
	}

	/**
	 * EN: Check if current context is a customer document card.
	 * FR: Vérifier si le contexte est une fiche document client.
	 *
	 * @param	array	$currentcontext	Context array
	 * @return	bool
	 */
	private function isCustomerDocumentContext($currentcontext)
	{
		return in_array('propalcard', $currentcontext, true)
			|| in_array('ordercard', $currentcontext, true)
			|| in_array('invoicecard', $currentcontext, true);
	}

	/**
	 * EN: Check if object is a customer document element.
	 * FR: Vérifier si l'objet est un document client.
	 *
	 * @param	CommonObject	$object	Object to check
	 * @return	bool
	 */
	private function isCustomerDocumentElement($object)
	{
		return in_array($object->element, array('propal', 'commande', 'facture'), true);
	}

	/**
	 * EN: Get VAT reverse charge status for a customer document.
	 * FR: Obtenir le statut d'autoliquidation pour un document client.
	 *
	 * @param	CommonObject	$object	Object to process
	 * @return	array
	 */
	private function getVatReverseChargeStatus($object)
	{
		if (method_exists($object, 'fetch_optionals')) {
			$object->fetch_optionals();
		}

		$documentValue = ! empty($object->array_options['options_delegation_vat_reverse_charge']) ? 1 : 0;
		$contractStatus = $this->getLinkedContractVatReverseCharge($object);

		if ($contractStatus['active']) {
			return array(
				'active' => true,
				'inherited' => true,
				'contract_id' => $contractStatus['contract_id'],
			);
		}

		return array(
			'active' => (bool) $documentValue,
			'inherited' => false,
			'contract_id' => 0,
		);
	}

	/**
	 * EN: Retrieve linked contract reverse charge status.
	 * FR: Récupérer le statut d'autoliquidation du contrat lié.
	 *
	 * @param	CommonObject	$object	Object to process
	 * @return	array
	 */
	private function getLinkedContractVatReverseCharge($object)
	{
		$result = array(
			'active' => false,
			'contract_id' => 0,
		);

		if (empty($object->id)) {
			return $result;
		}

		$sql = "SELECT ee.fk_source, ee.fk_target, ee.sourcetype, ee.targettype";
		$sql.= " FROM ".MAIN_DB_PREFIX."element_element as ee";
		$sql.= " WHERE (ee.sourcetype = '".$this->db->escape($object->element)."'";
		$sql.= " AND ee.fk_source = ".(int) $object->id;
		$sql.= " AND ee.targettype = 'contrat')";
		$sql.= " OR (ee.targettype = '".$this->db->escape($object->element)."'";
		$sql.= " AND ee.fk_target = ".(int) $object->id;
		$sql.= " AND ee.sourcetype = 'contrat')";

		$resql = $this->db->query($sql);
		if (! $resql) {
			return $result;
		}

		while ($obj = $this->db->fetch_object($resql)) {
			$contractId = ($obj->sourcetype === 'contrat') ? (int) $obj->fk_source : (int) $obj->fk_target;
			if ($contractId <= 0) {
				continue;
			}
			$contract = new Contrat($this->db);
			if ($contract->fetch($contractId) <= 0) {
				continue;
			}
			if (method_exists($contract, 'fetch_optionals')) {
				$contract->fetch_optionals();
			}
			if (! empty($contract->array_options['options_delegation_subcontract_vat_reverse_charge'])) {
				$result['active'] = true;
				$result['contract_id'] = $contractId;
				break;
			}
		}

		return $result;
	}

	/**
	 * EN: Apply VAT reverse charge on all lines.
	 * FR: Appliquer l'autoliquidation de TVA sur toutes les lignes.
	 *
	 * @param	CommonObject	$object	Object to update
	 * @param	User			$user	User performing action
	 * @return	int
	 */
	private function applyVatReverseChargeOnLines($object, $user)
	{
		$scope = $this->getVatReverseChargeScope();
		$updated = 0;

		if (method_exists($object, 'fetch_lines')) {
			$object->fetch_lines();
		}

		if (empty($object->lines) || ! is_array($object->lines)) {
			return 0;
		}

		foreach ($object->lines as $line) {
			if (! $this->isLineInScope($line, $scope)) {
				continue;
			}
			if (price2num($line->tva_tx) == 0) {
				continue;
			}
			$line->tva_tx = 0;
			if (method_exists($line, 'update')) {
				$res = $line->update($user, 1);
				if ($res > 0) {
					$updated++;
				}
			} elseif (! empty($object->table_element_line) && ! empty($line->id)) {
				$sql = "UPDATE ".MAIN_DB_PREFIX.$this->db->escape($object->table_element_line);
				$sql.= " SET tva_tx = 0";
				$sql.= " WHERE rowid = ".(int) $line->id;
				if ($this->db->query($sql)) {
					$updated++;
				}
			}
		}

		if ($updated > 0 && method_exists($object, 'update_price')) {
			$object->update_price(1);
		}

		return $updated;
	}

	/**
	 * EN: Check if a line matches the scope from POST.
	 * FR: Vérifier si une ligne correspond au périmètre depuis le POST.
	 *
	 * @return	bool
	 */
	private function isLineInScopeFromPost()
	{
		$scope = $this->getVatReverseChargeScope();
		if ($scope === 'all_lines') {
			return true;
		}

		$type = GETPOST('type', 'int');
		if ($type === '') {
			$type = GETPOST('product_type', 'int');
		}

		return ((int) $type === 1);
	}

	/**
	 * EN: Check if a line matches the configured scope.
	 * FR: Vérifier si une ligne correspond au périmètre configuré.
	 *
	 * @param	object	$line	Line to check
	 * @param	string	$scope	Scope value
	 * @return	bool
	 */
	private function isLineInScope($line, $scope)
	{
		if ($scope === 'all_lines') {
			return true;
		}

		$productType = isset($line->product_type) ? $line->product_type : (isset($line->type) ? $line->type : 0);

		return ((int) $productType === 1);
	}

	/**
	 * EN: Get the scope for VAT reverse charge.
	 * FR: Obtenir le périmètre pour l'autoliquidation de TVA.
	 *
	 * @return	string
	 */
	private function getVatReverseChargeScope()
	{
		$scope = getDolGlobalString('DELEGATION_VAT_REVERSE_CHARGE_SCOPE', 'services_only');
		if (! in_array($scope, array('services_only', 'all_lines'), true)) {
			$scope = 'services_only';
		}

		return $scope;
	}

	/**
	 * EN: Get legal text for VAT reverse charge.
	 * FR: Obtenir le texte légal pour l'autoliquidation de TVA.
	 *
	 * @param	Translate	$langs	Lang handler
	 * @return	string
	 */
	private function getVatReverseChargeLegalText($langs)
	{
		$text = getDolGlobalString('DELEGATION_VAT_REVERSE_CHARGE_LEGAL_TEXT', '');
		if (empty($text)) {
			$text = $langs->trans('DelegationVatReverseChargeLegalTextDefault');
		}

		return $text;
	}

	/**
	 * EN: Check permission to read subcontracting contract.
	 * FR: Vérifier la permission de lecture du contrat de sous-traitance.
	 *
	 * @param	User	$user	User to check
	 * @return	bool
	 */
	private function userCanReadSubcontractContract($user)
	{
		return ($user->admin || (! empty($user->rights->delegation->subcontract_contract->read)));
	}

	/**
	 * EN: Check permission to write subcontracting contract.
	 * FR: Vérifier la permission d'écriture du contrat de sous-traitance.
	 *
	 * @param	User	$user	User to check
	 * @return	bool
	 */
	private function userCanWriteSubcontractContract($user)
	{
		return ($user->admin || (! empty($user->rights->delegation->subcontract_contract->write)));
	}

	/**
	 * EN: Check permission to read VAT reverse charge.
	 * FR: Vérifier la permission de lecture de l'autoliquidation de TVA.
	 *
	 * @param	User	$user	User to check
	 * @return	bool
	 */
	private function userCanReadVatReverseCharge($user)
	{
		return ($user->admin || (! empty($user->rights->delegation->vat_reverse_charge->read)));
	}

	/**
	 * EN: Check permission to write VAT reverse charge.
	 * FR: Vérifier la permission d'écriture de l'autoliquidation de TVA.
	 *
	 * @param	User	$user	User to check
	 * @return	bool
	 */
	private function userCanWriteVatReverseCharge($user)
	{
		return ($user->admin || (! empty($user->rights->delegation->vat_reverse_charge->write)));
	}
}
