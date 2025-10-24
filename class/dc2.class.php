<?php
/* Copyright (C) 2018      Pierre Ardoin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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

/*
AB_idem_DC1
A
B
C1
C2
C2_idem
C2_adresse_internet
C2_renseignement_adresse
D1_liste
D1_reference
D1_idem
D1_adresse_internet
D1_renseignement_adresse
D2
E1_registre_pro
E1_registre_spec
E3_idem
E3_adresse_internet
E3_renseignement_adresse
F_CA3_debut
F_CA3_fin
F_CA3_montant
F_CA2_debut
F_CA2_fin
F_CA2_montant
F_CA1_debut
F_CA1_fin
F_CA1_montant
F_date_creation
F2
F3
F4_idem
F4_adresse_internet
F4_renseignement_adresse
G1
G2_idem
G2_adresse_internet
G2_renseignement_adresse
H
I1
I2
*/


require_once(DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/propale/modules_propale.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/propal.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';


/**
 *      \class      DC2s
 *      \brief      Class to manage margin
 */
class DC2 extends CommonObject
{
	var $db;
	var $error;
	var $element = 'DC2';
	var $table_element = '';
	var $table_element_line = 'DC2';
    var $table_element_line2 = 'DC2_groupement';
	var $fk_element = '';
	var $ismultientitymanaged = 0;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

	var $lines = array();
	var $line_dc1;
    var $line_dc2;
	
   /**
	*  \brief  Constructeur de la classe
	*  @param  DB          handler acces base de donnees
	*/
	function __construct($db)
	{
		$this->db = $db;
	}

   /**
	*  \brief  Execute action
	*  @param  action      action to execute
    *  @param  args        arguments array
    *  @return int         <0 if KO, >0 if OK
	*/
    function call($action, $args)
    {
        global $langs;


        if (empty($action))
        {
            return 0;
        }

        if (method_exists($this, $action))
        {
            $result = call_user_func_array(array($this, $action), $args);

            return $result;
        }
        else
        {
            return 0;
        }
    }

	
	/**
	 * Fetch object lines from database
	 *
	 * @return 	int		<0 if KO, >0 if OK
	 */
	function fetch()
	{
	    global $conf, $langs, $object, $dc1_line;
			
        $this->lines = array();

		$sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."DC2 WHERE `fk_object` = ".$object->id;

		dol_syslog("DC2::fetch sql=".$sql, LOG_DEBUG);

		$result = $this->db->query($sql);


		if ($result > 0)
		{
            $num = $this->db->num_rows($result);
        	$i = 0 ;

			if ($num == 1)
            {
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($result);


                    $this->lines[$i]            = $obj;
                    $this->lines[$i]->AB_idem_DC1 = $obj->AB_idem_DC1 ? $obj->AB_idem_DC1 : 0;
                    $this->lines[$i]->A = trim($obj->A);
                    $this->lines[$i]->B = trim($obj->B);
                    $this->lines[$i]->C1 = $obj->C1 ? $obj->C1 : 0;
                    $this->lines[$i]->C2 = $obj->C2 ? $obj->C2 : 0;
                    $this->lines[$i]->C2_Date = trim($obj->C2_Date);
                    $this->lines[$i]->C2_idem = $obj->C2_idem ? $obj->C2_idem : 0;
                    $this->lines[$i]->C2_adresse_internet = trim($obj->C2_adresse_internet);
                    $this->lines[$i]->C2_renseignement_adresse = trim($obj->C2_renseignement_adresse);
                    $this->lines[$i]->D1_liste =  trim($obj->D1_liste);
                    $this->lines[$i]->D1_reference =  trim($obj->D1_reference);
                    $this->lines[$i]->D1_idem = $obj->D1_idem ? $obj->D1_idem : 0;
                    $this->lines[$i]->D1_adresse_internet =  trim($obj->D1_adresse_internet);
                    $this->lines[$i]->D1_renseignement_adresse =  trim($obj->D1_renseignement_adresse);
                    $this->lines[$i]->D2 = $obj->D2 ? $obj->D2 : 0;
                    $this->lines[$i]->E1_registre_pro =  trim($obj->E1_registre_pro);
                    $this->lines[$i]->E1_registre_spec =  trim($obj->E1_registre_spec);
                    $this->lines[$i]->E3_idem = $obj->E3_idem ? $obj->E3_idem : 0;
                    $this->lines[$i]->E3_adresse_internet =  trim($obj->E3_adresse_internet);
                    $this->lines[$i]->E3_renseignement_adresse =  trim($obj->E3_renseignement_adresse);
                    $this->lines[$i]->F_CA3_debut =  trim($obj->F_CA3_debut);
                    $this->lines[$i]->F_CA3_fin =  trim($obj->F_CA3_fin);
                    $this->lines[$i]->F_CA3_montant =  trim($obj->F_CA3_montant);
                    $this->lines[$i]->F_CA2_debut =  trim($obj->F_CA2_debut);
                    $this->lines[$i]->F_CA2_fin =  trim($obj->F_CA2_fin);
                    $this->lines[$i]->F_CA2_montant =  trim($obj->F_CA2_montant);
                    $this->lines[$i]->F_CA1_debut =  trim($obj->F_CA1_debut);
                    $this->lines[$i]->F_CA1_fin =  trim($obj->F_CA1_fin);
                    $this->lines[$i]->F_CA1_montant =  trim($obj->F_CA1_montant);
                    $this->lines[$i]->F_date_creation =  trim($obj->F_date_creation);
                    $this->lines[$i]->F2 =  trim($obj->F2);
                    $this->lines[$i]->F3 = $obj->F3 ? $obj->F3 : 0;
                    $this->lines[$i]->F4_idem = $obj->F4_idem ? $obj->F4_idem : 0;
                    $this->lines[$i]->F4_adresse_internet =  trim($obj->F4_adresse_internet);
                    $this->lines[$i]->F4_renseignement_adresse =  trim($obj->F4_renseignement_adresse);
                    $this->lines[$i]->G1 =  trim($obj->G1);
                    $this->lines[$i]->G2_idem = $obj->G2_idem ? $obj->G2_idem : 0;
                    $this->lines[$i]->G2_adresse_internet =  trim($obj->G2_adresse_internet);
                    $this->lines[$i]->G2_renseignement_adresse =  trim($obj->G2_renseignement_adresse);
                    $this->lines[$i]->H =  trim($obj->H);
                    $this->lines[$i]->I1 =  trim($obj->I1);
                    $this->lines[$i]->I2 =  trim($obj->I2);

                    $i++;
                }

            } 
            else
            {

                $this->line  = new DC2Line($this->db);
            
                $this->line->fk_object = $object->id;
                $this->line->fk_element = $object->element; 
                $this->line->AB_idem_DC1 = "1";
                $this->line->A = $object->socid;
                $this->line->B = $dc1_line->objet_consultation;
                $this->line->C1 = "0";
                $this->line->C2 = "0";
                $this->line->C2_Date = "1970-01-01";
                $this->line->C2_idem = "0";
                $this->line->C2_adresse_internet = "";
                $this->line->C2_renseignement_adresse = "";
                $this->line->D1_liste = "";
                $this->line->D1_reference = "";
                $this->line->D1_idem = "0";
                $this->line->D1_adresse_internet = "";
                $this->line->D1_renseignement_adresse = "";
                $this->line->D2 = "0";
                $this->line->E1_registre_pro = "";
                $this->line->E1_registre_spec = "";
                $this->line->E3_idem = "0";
                $this->line->E3_adresse_internet = "";
                $this->line->E3_renseignement_adresse = "";
                $this->line->F_CA3_debut = "1970-01-01";
                $this->line->F_CA3_fin = "1970-01-01";
                $this->line->F_CA3_montant = "0";
                $this->line->F_CA2_debut = "1970-01-01";
                $this->line->F_CA2_fin = "1970-01-01";
                $this->line->F_CA2_montant = "0";
                $this->line->F_CA1_debut = "1970-01-01";
                $this->line->F_CA1_fin = "1970-01-01";
                $this->line->F_CA1_montant = "0";
                $this->line->F_date_creation = "1970-01-01";
                $this->line->F2 = "";
                $this->line->F3 = "0";
                $this->line->F4_idem = "0";
                $this->line->F4_adresse_internet = "";
                $this->line->F4_renseignement_adresse = "";
                $this->line->G1 = "";
                $this->line->G2_idem = "0";
                $this->line->G2_adresse_internet = "";
                $this->line->G2_renseignement_adresse = "";
                $this->line->H = "";
                $this->line->I1 = "";
                $this->line->I2 = "";
                
                $result = $this->line->insert();

                $this->lines = array();

                $sql = "SELECT *";
                $sql.= " FROM ".MAIN_DB_PREFIX."DC2 WHERE `fk_object` = ".$object->id;

                dol_syslog("DC2::fetch sql=".$sql, LOG_DEBUG);

                $result = $this->db->query($sql);

                
                if ($result > 0)
                {
                    $num = $this->db->num_rows($result);
                    $i = 0 ;

                    if ($num == 1)
                    {
                        while ($i < $num)
                        {
                            $obj = $this->db->fetch_object($result);


                            $this->lines[$i]            = $obj;
                            $this->lines[$i]->AB_idem_DC1 = $obj->AB_idem_DC1 ? $obj->AB_idem_DC1 : 0;
                            $this->lines[$i]->A = trim($obj->A);
                            $this->lines[$i]->B = trim($obj->B);
                            $this->lines[$i]->C1 = $obj->C1 ? $obj->C1 : 0;
                            $this->lines[$i]->C2 = $obj->C2 ? $obj->C2 : 0;
                            $this->lines[$i]->C2_Date = trim($obj->C2_Date);
                            $this->lines[$i]->C2_idem = $obj->C2_idem ? $obj->C2_idem : 0;
                            $this->lines[$i]->C2_adresse_internet = trim($obj->C2_adresse_internet);
                            $this->lines[$i]->C2_renseignement_adresse = trim($obj->C2_renseignement_adresse);
                            $this->lines[$i]->D1_liste =  trim($obj->D1_liste);
                            $this->lines[$i]->D1_reference =  trim($obj->D1_reference);
                            $this->lines[$i]->D1_idem = $obj->D1_idem ? $obj->D1_idem : 0;
                            $this->lines[$i]->D1_adresse_internet =  trim($obj->D1_adresse_internet);
                            $this->lines[$i]->D1_renseignement_adresse =  trim($obj->D1_renseignement_adresse);
                            $this->lines[$i]->D2 = $obj->D2 ? $obj->D2 : 0;
                            $this->lines[$i]->E1_registre_pro =  trim($obj->E1_registre_pro);
                            $this->lines[$i]->E1_registre_spec =  trim($obj->E1_registre_spec);
                            $this->lines[$i]->E3_idem = $obj->E3_idem ? $obj->E3_idem : 0;
                            $this->lines[$i]->E3_adresse_internet =  trim($obj->E3_adresse_internet);
                            $this->lines[$i]->E3_renseignement_adresse =  trim($obj->E3_renseignement_adresse);
                            $this->lines[$i]->F_CA3_debut =  trim($obj->F_CA3_debut);
                            $this->lines[$i]->F_CA3_fin =  trim($obj->F_CA3_fin);
                            $this->lines[$i]->F_CA3_montant =  trim($obj->F_CA3_montant);
                            $this->lines[$i]->F_CA2_debut =  trim($obj->F_CA2_debut);
                            $this->lines[$i]->F_CA2_fin =  trim($obj->F_CA2_fin);
                            $this->lines[$i]->F_CA2_montant =  trim($obj->F_CA2_montant);
                            $this->lines[$i]->F_CA1_debut =  trim($obj->F_CA1_debut);
                            $this->lines[$i]->F_CA1_fin =  trim($obj->F_CA1_fin);
                            $this->lines[$i]->F_CA1_montant =  trim($obj->F_CA1_montant);
                            $this->lines[$i]->F_date_creation =  trim($obj->F_date_creation);
                            $this->lines[$i]->F2 =  trim($obj->F2);
                            $this->lines[$i]->F3 = $obj->F3 ? $obj->F3 : 0;
                            $this->lines[$i]->F4_idem = $obj->F4_idem ? $obj->F4_idem : 0;
                            $this->lines[$i]->F4_adresse_internet =  trim($obj->F4_adresse_internet);
                            $this->lines[$i]->F4_renseignement_adresse =  trim($obj->F4_renseignement_adresse);
                            $this->lines[$i]->G1 =  trim($obj->G1);
                            $this->lines[$i]->G2_idem = $obj->G2_idem ? $obj->G2_idem : 0;
                            $this->lines[$i]->G2_adresse_internet =  trim($obj->G2_adresse_internet);
                            $this->lines[$i]->G2_renseignement_adresse =  trim($obj->G2_renseignement_adresse);
                            $this->lines[$i]->H =  trim($obj->H);
                            $this->lines[$i]->I1 =  trim($obj->I1);
                            $this->lines[$i]->I2 =  trim($obj->I2);

                            $i++;
                        }

                    }
                }

            }  
            return 1;   
        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;
            return -1;
        }

	}

	
    /**
     *  \brief Delete a line 
     *
   //  *  @param     lineid           Line id of propal
     //*  @return    int              <0 if KO, id the line added if OK
     */	
/*	function deleteline($user)
	{
		global $conf, $langs;
			
		$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
			
		$line = new DC2Line($this->db);
		
		$result = $line->fetch($lineid);
		
		if ($result)
		{
			$line->delete();
			
			$this->fetch();
			$this->error = $langs->trans('DC2LineDeleted');

			return 1;
		}
		else
		{
			$this->error = $langs->trans('DC2LineDoesNotExist');
			return 0;
		}
	}
*/
    /**
     *  \brief Add a line 
     *
   //  *  @param     user             User who adds
   //  *  @return    int              <0 if KO, id the line added if OK
     */
/*
    function addline($user)
    {
        global $langs, $conf, $object;

		$label 	= GETPOST('label');		
		$amount 	= price2num(GETPOST('amount'));		

		// Insert line
		$this->line  = new DC2Line($this->db);
		
		$this->line->fk_object = $object->id;
		$this->line->fk_element = $object->element;	
		$this->line->AB_idem_DC1 = $object->entity;    
        $this->line->A = $A;
        $this->line->B = $B; 
        $this->line->C1 = $C1;
        $this->line->C2 = $C2; 
        $this->line->C2_idem = $C2_idem; 
        $this->line->C2_adresse_internet = $C2_adresse_internet;
        $this->line->adresse_internet = $adresse_internet; 
        $this->line->renseignement_adresse = $renseignement_adresse; 
        $this->line->dc2 = $dc2;
		
		$result = $this->line->insert();
		
		if ($result > 0)
		{		
			$this->fetch();
			
			$this->error = $langs->trans('DC2LineAdded');		
			return 1;
		}
		else
		{
			$this->error = $this->line->error;

			return -2;
		}
	        
    }
*/

    /**
     *  \brief Update a line 
     *
     *  @param     user             User who adds
     *  @return    int              <0 if KO, id the line added if OK
     */
    function updateline($user)
    {
        global $langs, $conf, $object;

		$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
        $field = GETPOST('field');

        $AB_idem_DC1 =  GETPOST('AB_idem_DC1');
        $A = GETPOST('A');
        $B = GETPOST('B');
        $C1 = GETPOST('C1');
        $C2 = GETPOST('C2');
        $C2_Date = strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['C2_Datemonth'], $_POST['C2_Dateday'], $_POST['C2_Dateyear']));
        $C2_idem = GETPOST('C2_idem');
        $C2_adresse_internet = GETPOST('C2_adresse_internet');
        $C2_renseignement_adresse = GETPOST('C2_renseignement_adresse');
        $D1_liste =  GETPOST('D1_liste');
        $D1_reference =  GETPOST('D1_reference');
        $D1_idem = GETPOST('D1_idem');
        $D1_adresse_internet =  GETPOST('D1_adresse_internet');
        $D1_renseignement_adresse =  GETPOST('D1_renseignement_adresse');
        $D2 = GETPOST('D2');
        $E1_registre_pro =  GETPOST('E1_registre_pro');
        $E1_registre_spec =  GETPOST('E1_registre_spec');
        $E3_idem = GETPOST('E3_idem');
        $E3_adresse_internet =  GETPOST('E3_adresse_internet');
        $E3_renseignement_adresse =  GETPOST('E3_renseignement_adresse');
        $F_CA3_debut =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_CA3_debutmonth'], $_POST['F_CA3_debutday'], $_POST['F_CA3_debutyear']));
        $F_CA3_fin =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_CA3_finmonth'], $_POST['F_CA3_finday'], $_POST['F_CA3_finyear']));
        $F_CA3_montant =  GETPOST('F_CA3_montant');
        $F_CA2_debut =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_CA2_debutmonth'], $_POST['F_CA2_debutday'], $_POST['F_CA2_debutyear']));
        $F_CA2_fin =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_CA2_finmonth'], $_POST['F_CA2_finday'], $_POST['F_CA2_finyear']));
        $F_CA2_montant =  GETPOST('F_CA2_montant');
        $F_CA1_debut =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_CA1_debutmonth'], $_POST['F_CA1_debutday'], $_POST['F_CA1_debutyear']));
        $F_CA1_fin =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_CA1_finmonth'], $_POST['F_CA1_finday'], $_POST['F_CA1_finyear']));
        $F_CA1_montant =  GETPOST('F_CA1_montant');
        $F_date_creation =  strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['F_date_creationmonth'], $_POST['F_date_creationday'], $_POST['F_date_creationyear']));
        $F2 =  GETPOST('F2');
        $F3 = GETPOST('F3');
        $F4_idem = GETPOST('F4_idem');
        $F4_adresse_internet =  GETPOST('F4_adresse_internet');
        $F4_renseignement_adresse =  GETPOST('F4_renseignement_adresse');
        $G1 =  GETPOST('G1');
        $G2_idem = GETPOST('G2_idem');
        $G2_adresse_internet =  GETPOST('G2_adresse_internet');
        $G2_renseignement_adresse =  GETPOST('G2_renseignement_adresse');
        $H =  GETPOST('H');
        $I1 =  GETPOST('I1');
        $I2 =  GETPOST('I2');

		$line = new DC2Line($this->db);
		
		$result = $line->fetch($lineid);
		
		if ($result)
		{
            $line->field = $field;

            $line->AB_idem_DC1 = $AB_idem_DC1 ;
            $line->A = $A ;
            $line->B = $B ;
            $line->C1 = $C1 ;
            $line->C2 = $C2 ;
            $line->C2_Date = $C2_Date ;
            $line->C2_idem = $C2_idem ;
            $line->C2_adresse_internet = $C2_adresse_internet ;
            $line->C2_renseignement_adresse = $C2_renseignement_adresse ;
            $line->D1_liste = $D1_liste ;
            $line->D1_reference = $D1_reference ;
            $line->D1_idem = $D1_idem ;
            $line->D1_adresse_internet = $D1_adresse_internet ;
            $line->D1_renseignement_adresse = $D1_renseignement_adresse ;
            $line->D2 = $D2 ;
            $line->E1_registre_pro = $E1_registre_pro ;
            $line->E1_registre_spec = $E1_registre_spec ;
            $line->E3_idem = $E3_idem ;
            $line->E3_adresse_internet = $E3_adresse_internet ;
            $line->E3_renseignement_adresse = $E3_renseignement_adresse ;
            $line->F_CA3_debut = $F_CA3_debut ;
            $line->F_CA3_fin = $F_CA3_fin ;
            $line->F_CA3_montant = $F_CA3_montant ;
            $line->F_CA2_debut = $F_CA2_debut ;
            $line->F_CA2_fin = $F_CA2_fin ;
            $line->F_CA2_montant = $F_CA2_montant ;
            $line->F_CA1_debut = $F_CA1_debut ;
            $line->F_CA1_fin = $F_CA1_fin ;
            $line->F_CA1_montant = $F_CA1_montant ;
            $line->F_date_creation = $F_date_creation ;
            $line->F2 = $F2 ;
            $line->F3 = $F3 ;
            $line->F4_idem = $F4_idem ;
            $line->F4_adresse_internet = $F4_adresse_internet ;
            $line->F4_renseignement_adresse = $F4_renseignement_adresse ;
            $line->G1 = $G1 ;
            $line->G2_idem = $G2_idem ;
            $line->G2_adresse_internet = $G2_adresse_internet ;
            $line->G2_renseignement_adresse = $G2_renseignement_adresse ;
            $line->H = $H ;
            $line->I1 = $I1 ;
            $line->I2 = $I2 ;
	
			$result = $line->update();

			if ($result > 0)
			{		
				$this->fetch();
			
				$this->error = $langs->trans('DC2LineUpdated');		
				return $line->rowid;
			}
			else
			{
				$this->error = $this->line->error;

				return -2;
			}
		}
		else
		{
			$this->error = $langs->trans('DC2LineDoesNotExist');
			return 0;
		}
	        
    }
        	
}

/**
 *	\class      	DC2Line
 *	\brief      	Class to manage margins
 *	\remarks		Uses lines of llx_DC2 tables
 */
class DC2Line
{
    var $db;
    var $error;

    var $oldline;


	var $rowid;
	var $fk_object;
	var $fk_element;

    var $AB_idem_DC1 ;
    var $A ;
    var $B ;
    var $C1 ;
    var $C2 ;
    var $C2_Date ;
    var $C2_idem ;
    var $C2_adresse_internet;
    var $C2_renseignement_adresse ;
    var $D1_liste ;
    var $D1_reference ;
    var $D1_idem ;
    var $D1_adresse_internet ;
    var $D1_renseignement_adresse ;
    var $D2 ;
    var $E1_registre_pro ;
    var $E1_registre_spec ;
    var $E3_idem ;
    var $E3_adresse_internet ;
    var $E3_renseignement_adresse ;
    var $F_CA3_debut ;
    var $F_CA3_fin ;
    var $F_CA3_montant ;
    var $F_CA2_debut ;
    var $F_CA2_fin ;
    var $F_CA2_montant ;
    var $F_CA1_debut ;
    var $F_CA1_fin ;
    var $F_CA1_montant ;
    var $F_date_creation ;
    var $F2 ;
    var $F3 ;
    var $F4_idem ;
    var $F4_adresse_internet ;
    var $F4_renseignement_adresse ;
    var $G1 ;
    var $G2_idem ;
    var $G2_adresse_internet ;
    var $G2_renseignement_adresse ;
    var $H ;
    var $I1 ;
    var $I2 ;



   /**
	*  \brief  Constructeur de la classe
	*  @param  DB          handler acces base de donnees
	*/
    function __construct($DB)
    {
        $this->db = $DB;

    }

	 /**
     * 	Delete line in database
     *
     *	@return	 int  <0 si ko, >0 si ok
     */
 /*
    function delete()
    {
        global $conf, $user, $langs;

		$error=0;

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."DC2 WHERE rowid=".$this->rowid;

        dol_syslog("DC2Line::delete sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            // Appel des triggers
            include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
            $interface=new Interfaces($this->db);
            $result=$interface->run_triggers('LINEDC2_DELETE',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            // Fin appel triggers

            return 1;
        }
        else
        {
            $this->error=$this->db->lasterror();
            dol_syslog("DC2Line::delete ".$this->error, LOG_ERR);
            return -1;
        }
    }
*/

   /**
     *	\brief     	Insert line in database
     *	@param      notrigger		1 no triggers
     *	@return		int				<0 if KO, >0 if OK
     */
    function fetch($lineid = 0)
    {
        global $langs, $user, $conf;

        
        $sql = "SELECT *";
		$sql.= " FROM ".MAIN_DB_PREFIX."DC2";
        $sql.= " WHERE `rowid` = ".$lineid;

        dol_syslog("DC2Line::fetch sql=".$sql);

        $result = $this->db->query($sql);

        if ($result)
        {

            $num = $this->db->num_rows($result);

            if ($num)
            {
                $obj = $this->db->fetch_object($result);



				$this->rowid		= $obj->rowid ? $obj->rowid : 0;
				$this->fk_object	= $obj->fk_objectdet ? $obj->fk_objectdet : 0;
				$this->fk_element	= $obj->fk_element;

                $this->field = $field;
				
                $this->line            = $obj;
                $this->line->AB_idem_DC1 = $obj->AB_idem_DC1 ? $obj->AB_idem_DC1 : 0;
                $this->line->A = trim($obj->A);
                $this->line->B = trim($obj->B);
                $this->line->C1 = $obj->C1 ? $obj->C1 : 0;
                $this->line->C2 = $obj->C2 ? $obj->C2 : 0;
                $this->line->C2_Date = trim($obj->C2_Date);
                $this->line->C2_idem = $obj->C2_idem ? $obj->C2_idem : 0;
                $this->line->C2_adresse_internet = trim($obj->C2_adresse_internet);
                $this->line->C2_renseignement_adresse = trim($obj->C2_renseignement_adresse);
                $this->line->D1_liste =  trim($obj->D1_liste);
                $this->line->D1_reference =  trim($obj->D1_reference);
                $this->line->D1_idem = $obj->D1_idem ? $obj->D1_idem : 0;
                $this->line->D1_adresse_internet =  trim($obj->D1_adresse_internet);
                $this->line->D1_renseignement_adresse =  trim($obj->D1_renseignement_adresse);
                $this->line->D2 = $obj->D2 ? $obj->D2 : 0;
                $this->line->E1_registre_pro =  trim($obj->E1_registre_pro);
                $this->line->E1_registre_spec =  trim($obj->E1_registre_spec);
                $this->line->E3_idem = $obj->E3_idem ? $obj->E3_idem : 0;
                $this->line->E3_adresse_internet =  trim($obj->E3_adresse_internet);
                $this->line->E3_renseignement_adresse =  trim($obj->E3_renseignement_adresse);
                $this->line->F_CA3_debut =  trim($obj->F_CA3_debut);
                $this->line->F_CA3_fin =  trim($obj->F_CA3_fin);
                $this->line->F_CA3_montant =  trim($obj->F_CA3_montant);
                $this->line->F_CA2_debut =  trim($obj->F_CA2_debut);
                $this->line->F_CA2_fin =  trim($obj->F_CA2_fin);
                $this->line->F_CA2_montant =  trim($obj->F_CA2_montant);
                $this->line->F_CA1_debut =  trim($obj->F_CA1_debut);
                $this->line->F_CA1_fin =  trim($obj->F_CA1_fin);
                $this->line->F_CA1_montant =  trim($obj->F_CA1_montant);
                $this->line->F_date_creation =  trim($obj->F_date_creation);
                $this->line->F2 =  trim($obj->F2);
                $this->line->F3 = $obj->F3 ? $obj->F3 : 0;
                $this->line->F4_idem = $obj->F4_idem ? $obj->F4_idem : 0;
                $this->line->F4_adresse_internet =  trim($obj->F4_adresse_internet);
                $this->line->F4_renseignement_adresse =  trim($obj->F4_renseignement_adresse);
                $this->line->G1 =  trim($obj->G1);
                $this->line->G2_idem = $obj->G2_idem ? $obj->G2_idem : 0;
                $this->line->G2_adresse_internet =  trim($obj->G2_adresse_internet);
                $this->line->G2_renseignement_adresse =  trim($obj->G2_renseignement_adresse);
                $this->line->H =  trim($obj->H);
                $this->line->I1 =  trim($obj->I1);
                $this->line->I2 =  trim($obj->I2);
				
                //$this->db->free($result);
				
				return $this->rowid;
            }
            else
            {
                $this->error = $langs->trans('DC2LineDoesNotExist');
                return -1;
            }

        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;

            return -1;
        }
    }
	
    /**
     *	\brief     	Insert line in database
     *	@param      notrigger		1 no triggers
     *	@return		int				<0 if KO, >0 if OK
     */
    function insert($notrigger = 0)
    {
        global $langs, $user, $conf;
		
        $this->db->begin();

        
        //
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."DC2";
        $sql.= " (`fk_object`, `fk_element`, `AB_idem_DC1`, `A`, `B`, `C1` ,`C2`,`C2_Date`, `C2_idem`, `C2_adresse_internet`,`C2_renseignement_adresse`, `D1_liste`, `D1_reference`, `D1_idem`, `D1_adresse_internet`, `D1_renseignement_adresse`, `D2`, `E1_registre_pro`, `E1_registre_spec`, `E3_idem`, `E3_adresse_internet`, `E3_renseignement_adresse`, `F_CA3_debut`, `F_CA3_fin`, `F_CA3_montant`, `F_CA2_debut`, `F_CA2_fin`, `F_CA2_montant`, `F_CA1_debut`, `F_CA1_fin`, `F_CA1_montant`, `F_date_creation`, `F2`, `F3`, `F4_idem`, `F4_adresse_internet`, `F4_renseignement_adresse`, `G1`, `G2_idem`, `G2_adresse_internet`, `G2_renseignement_adresse`, `H`, `I1`, `I2`)";
        $sql.= " VALUES (".$this->fk_object.",";
        $sql.= " '".$this->fk_element."', ";
        $sql.= " '".$this->AB_idem_DC1."', ";
        $sql.= " '".$this->A."', ";
        $sql.= " '".$this->B."', ";
        $sql.= " '".$this->C1."', ";
        $sql.= " '".$this->C2."', ";
        $sql.= " '".$this->C2_Date."', ";
        $sql.= " '".$this->C2_idem."', ";
        $sql.= " '".$this->C2_adresse_internet."', ";
        $sql.= " '".$this->C2_renseignement_adresse."', ";
        $sql.= " '".$this->D1_liste."', ";
        $sql.= " '".$this->D1_reference."', ";
        $sql.= " '".$this->D1_idem."', ";
        $sql.= " '".$this->D1_adresse_internet."', ";
        $sql.= " '".$this->D1_renseignement_adresse."', ";
        $sql.= " '".$this->D2."', ";
        $sql.= " '".$this->E1_registre_pro."', ";
        $sql.= " '".$this->E1_registre_spec."', ";
        $sql.= " '".$this->E3_idem."', ";
        $sql.= " '".$this->E3_adresse_internet."', ";
        $sql.= " '".$this->E3_renseignement_adresse."', ";
        $sql.= " '".$this->F_CA3_debut."', ";
        $sql.= " '".$this->F_CA3_fin."', ";
        $sql.= " '".$this->F_CA3_montant."', ";
        $sql.= " '".$this->F_CA2_debut."', ";
        $sql.= " '".$this->F_CA2_fin."', ";
        $sql.= " '".$this->F_CA2_montant."', ";
        $sql.= " '".$this->F_CA1_debut."', ";
        $sql.= " '".$this->F_CA1_fin."', ";
        $sql.= " '".$this->F_CA1_montant."', ";
        $sql.= " '".$this->F_date_creation."', ";
        $sql.= " '".$this->F2."', ";
        $sql.= " '".$this->F3."', ";
        $sql.= " '".$this->F4_idem."', ";
        $sql.= " '".$this->F4_adresse_internet."', ";
        $sql.= " '".$this->F4_renseignement_adresse."', ";
        $sql.= " '".$this->G1."', ";
        $sql.= " '".$this->G2_idem."', ";
        $sql.= " '".$this->G2_adresse_internet."', ";
        $sql.= " '".$this->G2_renseignement_adresse."', ";
        $sql.= " '".$this->H."', ";
        $sql.= " '".$this->I1."', ";
        $sql.= " '".$this->I2."'";
        $sql.= ')';

        dol_syslog("DC2Line::insert sql=".$sql);

        $resql = $this->db->query($sql);
      if ($resql)
        {			
            //$this->update_ndfp_tms();

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('LINEDC2_INSERT', $this, $user ,$langs, $conf);
                if ($result < 0) {
                    $this->error = $langs->trans('ErrorCallingTrigger');
                    $this->db->rollback();
                    return -1;
                }
                // Fin appel triggers
            }

            $this->db->commit();

            return 1;

        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;
            $this->db->rollback();

            return -2;
        }


    }

    /**
     *	\brief     	Update line in database
     *	@param      notrigger		1 no triggers
     *	@return		int				<0 if KO, >0 if OK
     */
    function update($notrigger = 0)
    {
        global $langs, $user, $conf;


		// Clean parameters

        $this->AB_idem_DC1 = $this->AB_idem_DC1 ? $this->AB_idem_DC1 : 0;
        $this->A = trim($this->A);
        $this->B = trim($this->B);
        $this->C1 = $this->C1 ? $this->C1 : 0;
        $this->C2 = $this->C2 ? $this->C2 : 0;
        $this->C2_Date = trim($this->C2_Date);
        $this->C2_idem = $this->C2_idem ? $this->C2_idem : 0;
        $this->C2_adresse_internet = trim($this->C2_adresse_internet);
        $this->C2_renseignement_adresse = trim($this->C2_renseignement_adresse);
        $this->D1_liste =  trim($this->D1_liste);
        $this->D1_reference =  trim($this->D1_reference);
        $this->D1_idem = $this->D1_idem ? $this->D1_idem : 0;
        $this->D1_adresse_internet =  trim($this->D1_adresse_internet);
        $this->D1_renseignement_adresse =  trim($this->D1_renseignement_adresse);
        $this->D2 = $this->D2 ? $this->D2 : 0;
        $this->E1_registre_pro =  trim($this->E1_registre_pro);
        $this->E1_registre_spec =  trim($this->E1_registre_spec);
        $this->E3_idem = $this->E3_idem ? $this->E3_idem : 0;
        $this->E3_adresse_internet =  trim($this->E3_adresse_internet);
        $this->E3_renseignement_adresse =  trim($this->E3_renseignement_adresse);
        $this->F_CA3_debut =  trim($this->F_CA3_debut);
        $this->F_CA3_fin =  trim($this->F_CA3_fin);
        $this->F_CA3_montant =  trim($this->F_CA3_montant);
        $this->F_CA2_debut =  trim($this->F_CA2_debut);
        $this->F_CA2_fin =  trim($this->F_CA2_fin);
        $this->F_CA2_montant =  trim($this->F_CA2_montant);
        $this->F_CA1_debut =  trim($this->F_CA1_debut);
        $this->F_CA1_fin =  trim($this->F_CA1_fin);
        $this->F_CA1_montant =  trim($this->F_CA1_montant);
        $this->F_date_creation =  trim($this->F_date_creation);
        $this->F2 =  trim($this->F2);
        $this->F3 = $this->F3 ? $this->F3 : 0;
        $this->F4_idem = $this->F4_idem ? $this->F4_idem : 0;
        $this->F4_adresse_internet =  trim($this->F4_adresse_internet);
        $this->F4_renseignement_adresse =  trim($this->F4_renseignement_adresse);
        $this->G1 =  trim($this->G1);
        $this->G2_idem = $this->G2_idem ? $this->G2_idem : 0;
        $this->G2_adresse_internet =  trim($this->G2_adresse_internet);
        $this->G2_renseignement_adresse =  trim($this->G2_renseignement_adresse);
        $this->H =  trim($this->H);
        $this->I1 =  trim($this->I1);
        $this->I2 =  trim($this->I2);
				
        $this->db->begin();
	


        //
        $sql = "UPDATE ".MAIN_DB_PREFIX."DC2";
        $sql.= " SET rowid = '".$this->rowid."'";
        if ($this->field == "AB_idem_DC1") {
            $sql.= ", `AB_idem_DC1` = '".$this->AB_idem_DC1."'";
        }
        if ($this->field == "A") {
            $sql.= ", `A` = '".$this->A."'";
        }
        if ($this->field == "B") {
		  $sql.= ", `B` = '".$this->B."'";
        }
        if ($this->field == "C1") {
            $sql.= ", `C1` = '".$this->C1."'";
        }
        if ($this->field == "C2") {
            $sql.= ", `C2` = '".$this->C2."'";
        }
        if ($this->field == "C2_Date") {
            $sql.= ", `C2_Date` = '".$this->C2_Date."'";
        }
        if ($this->field == "C2_idem") {
            $sql.= ", `C2_idem` = '".$this->C2_idem."'";
        }
        if ($this->field == "C2_adresse_internet") {
            $sql.= ", `C2_adresse_internet` = '".$this->C2_adresse_internet."'";
        }
        if ($this->field == "C2_renseignement_adresse") {
            $sql.= ", `C2_renseignement_adresse` = '".$this->C2_renseignement_adresse."'";
        }
        if ($this->field == "D1_liste") {
            $sql.= ", `D1_liste` = '".$this->D1_liste."'";
        }
        if ($this->field == "D1_reference") {
            $sql.= ", `D1_reference` = '".$this->D1_reference."'";
        }
        if ($this->field == "D1_idem") {
            $sql.= ", `D1_idem` = '".$this->D1_idem."'";
        }
        if ($this->field == "D1_adresse_internet") {
            $sql.= ", `D1_adresse_internet` = '".$this->D1_adresse_internet."'";
        }
        if ($this->field == "D1_renseignement_adresse") {
            $sql.= ", `D1_renseignement_adresse` = '".$this->D1_renseignement_adresse."'";
        }
        if ($this->field == "D2") {
            $sql.= ", `D2` = '".$this->D2."'";
        }
        if ($this->field == "E1_registre_pro") {
            $sql.= ", `E1_registre_pro` = '".$this->E1_registre_pro."'";
        }
        if ($this->field == "E1_registre_spec") {
            $sql.= ", `E1_registre_spec` = '".$this->E1_registre_spec."'";
        }
        if ($this->field == "E3_idem") {
            $sql.= ", `E3_idem` = '".$this->E3_idem."'";
        }
        if ($this->field == "E3_adresse_internet") {
            $sql.= ", `E3_adresse_internet` = '".$this->E3_adresse_internet."'";
        }
        if ($this->field == "E3_renseignement_adresse") {
            $sql.= ", `E3_renseignement_adresse` = '".$this->E3_renseignement_adresse."'";
        }
        if ($this->field == "F_CA3_montant") {
            $sql.= ", `F_CA3_debut` = '".$this->F_CA3_debut."'";
        }
        if ($this->field == "F_CA3_montant") {
            $sql.= ", `F_CA3_fin` = '".$this->F_CA3_fin."'";
        }
        if ($this->field == "F_CA3_montant") {
            $sql.= ", `F_CA3_montant` = '".$this->F_CA3_montant."'";
        }
        if ($this->field == "F_CA2_montant") {
            $sql.= ", `F_CA2_debut` = '".$this->F_CA2_debut."'";
        }
        if ($this->field == "F_CA2_montant") {
            $sql.= ", `F_CA2_fin` = '".$this->F_CA2_fin."'";
        }
        if ($this->field == "F_CA2_montant") {
            $sql.= ", `F_CA2_montant` = '".$this->F_CA2_montant."'";
        }
        if ($this->field == "F_CA1_montant") {
            $sql.= ", `F_CA1_debut` = '".$this->F_CA1_debut."'";
        }
        if ($this->field == "F_CA1_montant") {
            $sql.= ", `F_CA1_fin` = '".$this->F_CA1_fin."'";
        }
        if ($this->field == "F_CA1_montant") {
            $sql.= ", `F_CA1_montant` = '".$this->F_CA1_montant."'";
        }
        if ($this->field == "F_date_creation") {
            $sql.= ", `F_date_creation` = '".$this->F_date_creation."'";
        }
        if ($this->field == "F2") {
            $sql.= ", `F2` = '".$this->F2."'";
        }
        if ($this->field == "F3") {
            $sql.= ", `F3` = '".$this->F3."'";
        }
        if ($this->field == "F4_idem") {
            $sql.= ", `F4_idem` = '".$this->F4_idem."'";
        }
        if ($this->field == "F4_adresse_internet") {
            $sql.= ", `F4_adresse_internet` = '".$this->F4_adresse_internet."'";
        }
        if ($this->field == "F4_renseignement_adresse") {
            $sql.= ", `F4_renseignement_adresse` = '".$this->F4_renseignement_adresse."'";
        }
        if ($this->field == "G1") {
            $sql.= ", `G1` = '".$this->G1."'";
        }
        if ($this->field == "G2_idem") {
            $sql.= ", `G2_idem` = '".$this->G2_idem."'";
        }
        if ($this->field == "G2_adresse_internet") {
            $sql.= ", `G2_adresse_internet` = '".$this->G2_adresse_internet."'";
        }
        if ($this->field == "G2_renseignement_adresse") {
            $sql.= ", `G2_renseignement_adresse` = '".$this->G2_renseignement_adresse."'";
        }
        if ($this->field == "H") {
            $sql.= ", `H` = '".$this->H."'";
        }
        if ($this->field == "I1") {
            $sql.= ", `I1` = '".$this->I1."'";
        }
        if ($this->field == "I2") {
            $sql.= ", `I2` = '".$this->I2."'";
        }
		$sql.= " WHERE rowid = '".$this->rowid."'";

        dol_syslog("DC2Line::update sql=".$sql);

        $resql = $this->db->query($sql);

        //echo $sql; // Vérification  

        if ($resql)
        {

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('LINEDC2_UPDATE', $this, $user ,$langs, $conf);
                if ($result < 0) {
                    $this->error = $langs->trans('ErrorCallingTrigger');
                    $this->db->rollback();
                    return -1;
                }
                // Fin appel triggers
            }

            $this->db->commit();

            return $this;

        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;
            $this->db->rollback();

            return -2;
        }
    } 
    
}
?>
