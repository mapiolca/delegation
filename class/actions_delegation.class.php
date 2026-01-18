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

class Actionsdelegation
{
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
}
