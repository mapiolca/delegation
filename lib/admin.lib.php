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

/**
 * Print a setup section title.
 * FR: Afficher un titre de section pour la configuration.
 *
 * @param string $title Title already translated.
 * @return void
 */
function delegation_setup_print_title($title)
{
	print '<tr class="liste_titre">';
	print '<td colspan="2">'.$title.'</td>';
	print '</tr>';
}

/**
 * Print an on/off constant row using the Dolibarr helper.
 * FR: Afficher une ligne on/off pour une constante.
 *
 * @param string $label Label already translated.
 * @param string $constname Constant name.
 * @param string $help Help tooltip text (already translated).
 * @return void
 */
function delegation_setup_print_on_off($label, $constname, $help = '')
{
	global $bc, $var, $langs, $form;

	$var = ! $var;
	print '<tr class="oddeven"'.$bc[$var].'>';
	print '<td align="left">';
	if (! empty($help)) {
		print $form->textwithtooltip($label, $help, 2, 1, img_info());
	} else {
		print $label;
	}
	print '</td>';
	print '<td align="center" width="300">';
	if (function_exists('ajax_constantonoff')) {
		print ajax_constantonoff($constname);
	} else {
		print $form->selectyesno($constname, (int) getDolGlobalInt($constname), 1);
	}
	print '</td>';
	print '</tr>';
}

/**
 * Print a setup row with a custom form input.
 * FR: Afficher une ligne de configuration avec un champ personnalisé.
 *
 * @param string $label Label already translated.
 * @param string $inputhtml Input HTML (select/textarea/input...).
 * @param string $action Action name.
 * @param string $buttonlabel Button label (already translated).
 * @param string $help Help tooltip text (already translated).
 * @return void
 */
function delegation_setup_print_input_form_part($label, $inputhtml, $action, $buttonlabel = '', $help = '')
{
	global $bc, $var, $langs, $form;

	$var = ! $var;
	print '<tr class="oddeven"'.$bc[$var].'>';
	print '<td align="left">';
	if (! empty($help)) {
		print $form->textwithtooltip($label, $help, 2, 1, img_info());
	} else {
		print $label;
	}
	print '</td>';
	print '<td align="center" width="300">';
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="'.$action.'">';
	print $inputhtml;
	$buttonlabel = $buttonlabel ?: $langs->trans("Save");
	print ' <input type="submit" class="button" value="'.$buttonlabel.'">';
	print '</form>';
	print '</td>';
	print '</tr>';
}
