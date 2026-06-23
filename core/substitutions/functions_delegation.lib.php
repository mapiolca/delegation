<?php
/* Copyright (C) 2026	Pierre Ardoin		<developpeur@lesmetiersdubatiment.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

/**
 * Complete substitution array for the Delegation module.
 *
 * Dolibarr calls functions named <modulekey>_completesubstitutionarray()
 * from files declared through module_parts['substitutions'].
 *
 * @param	array<string,string|float|null>	$substitutionarray	Array with substitution key => value
 * @param	Translate						$outputlangs		Output language handler
 * @param	?CommonObject					$object				Source object
 * @param	?mixed							$parameters			Additional parameters
 * @return	void
 */
function delegation_completesubstitutionarray(&$substitutionarray, $outputlangs, $object = null, $parameters = null)
{
	if (! is_object($object)) {
		return;
	}

	// No module-specific substitution is required yet.
}
