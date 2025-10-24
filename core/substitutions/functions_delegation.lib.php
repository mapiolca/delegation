<?php
/* Copyright (C) 2023 Eric Seigne <eric.seigne@cap-rel.fr>
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
 *	\file			htdocs/core/modules/substitutions/functions_stancer.lib.php
 *	\brief			A set of functions for Dolibarr
 *					This file contains functions for plugin stancer.
 */


/**
 * 		Function called to complete substitution array (before generating on ODT, or a personalized email)
 * 		functions xxx_completesubstitutionarray are called by make_substitutions() if file
 * 		is inside directory htdocs/core/substitutions
 *
 *		@param	array		$substitutionarray	Array with substitution key=>val
 *		@param	Translate	$outlangs			Output langs
 *		@param	Object		$object				Object to use to get values
 * 		@return	void							The entry parameter $substitutionarray is modified
 */
//dol_include_once('/stancer/lib/stancer.lib.php');

function stancer_completesubstitutionarray(&$substitutionarray, $outlangs, $object)
{
	global $conf;

	//TODO
	if (is_object($object) && ($object->id > 0 || $object->specimen)) {	// We do not add substitution entries if object is not instantiated (->id not > 0)
		$substitutionarray['__STANCER_SEPA_RUM__']=0000000;
		$substitutionarray['__STANCER_SEPA_DELAIS__']=getDolGlobalString('STANCER_DELAY_SEPA');
		//$substitutionarray['__STANCER_SEPA_URL__']=stancerShowOnlineCBLinkForCustomer($object->thirdparty);

		// $substitutionarray['__ONLINE_PAYMENT_URL__'] = 0000000;
		// $substitutionarray['__ONLINE_PAYMENT_TEXT_AND_URL__'] = 0000000;
		// DOL_MAIN_URL_ROOT.'/custom/stancer/public/newpayment.php
	}
}
