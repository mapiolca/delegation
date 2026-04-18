<?php
/* Copyright (C) 2026	Pierre Ardoin	<developpeur@lesmetiersdubatiment.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';

/**
 * Get the external contact used as DC4 representative on a supplier order.
 *
 * @param	DoliDB	$db			Database handler.
 * @param	object	$object		Supplier order object.
 * @return	array<string,string>	Representative data.
 */
function delegationGetSupplierOrderRepresentativeData($db, $object)
{
	global $langs;

	$data = array(
		'name' => '',
		'job' => '',
		'display' => '',
	);

	if (!is_object($object) || empty($object->id) || !method_exists($object, 'getIdContact')) {
		return $data;
	}

	$contactIds = $object->getIdContact('external', 'DELEGDC4');
	if (empty($contactIds) || !is_array($contactIds)) {
		return $data;
	}

	$contactId = (int) $contactIds[0];
	if ($contactId <= 0) {
		return $data;
	}

	$contact = new Contact($db);
	if ($contact->fetch($contactId) <= 0) {
		return $data;
	}

	$name = trim($contact->getFullName($langs));
	$job = trim((string) $contact->poste);

	$data['name'] = $name;
	$data['job'] = $job;
	$data['display'] = trim($name.(!empty($job) ? ', '.$job : ''));

	return $data;
}

