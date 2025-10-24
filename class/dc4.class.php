<?php
/* Copyright (C) 2018-2019      Pierre Ardoin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more DC4.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


require_once DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php";

require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_order/modules_commandefournisseur.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/modules/commande/modules_commande.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
if (! empty($conf->supplier_proposal->enabled))
    require_once DOL_DOCUMENT_ROOT . '/supplier_proposal/class/supplier_proposal.class.php';
if (!empty($conf->produit->enabled))
    require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
if (!empty($conf->projet->enabled)) {
    require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
}
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP


/**
 *      \class      DC4Lines
 *      \brief      Class to manage margin
 */


     
class DC4 extends CommonObject
{
	var $db;
	var $error;
	var $element = 'DC4';
	var $table_element = '';
	var $table_element_line = 'DC4';
	var $fk_element = '';
	var $ismultientitymanaged = 0;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

	var $lines = array();
    var $line;

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
	    global $conf, $langs, $object;
			
        $this->lines = array();

		$sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."DC4_deleg_csst WHERE `fk_object` = ".$object->id." AND `fk_element` = '".$object->element."'";

		dol_syslog("DC4::fetch sql=".$sql, LOG_DEBUG);

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

                    $this->lines[$i] = $obj;
                    $this->lines[$i]->dc4_object_declaration = $obj->dc4_object_declaration ? $obj->dc4_object_declaration : 0;
                    $this->lines[$i]->dc4_hypothese = $obj->dc4_hypothese ? $obj->dc4_hypothese : 0;
                    $this->lines[$i]->dc4_date_initiale = trim($obj->dc4_date_initiale);
                    $this->lines[$i]->avance = trim($obj->avance);
                    $this->lines[$i]->dc4_documents_fournis =  trim($obj->dc4_documents_fournis);
                    $this->lines[$i]->paiement_direct = $obj->paiement_direct ? $obj->paiement_direct : 0;
                    $this->lines[$i]->libelle_poste_cctp =  trim($obj->libelle_poste_cctp);
                    $this->lines[$i]->sps_travaux =  $obj->sps_travaux ? $obj->sps_travaux : 0;
                    $this->lines[$i]->sps_date_remise = trim($obj->sps_date_remise);
                    $this->lines[$i]->cissct =  $obj->cissct ? $obj->cissct : 0;
                    $this->lines[$i]->DIUO = $obj->DIUO ? $obj->DIUO : 0;
                    $this->lines[$i]->responsabilite = $obj->responsabilite ? $obj->responsabilite : 0;

                    $i++;
                }
            } 
            else
            {

                $this->line  = new DC4Line($this->db);

                $this->line->fk_object = $object->id;
                $this->line->fk_element = $object->element;

                //On inscrit la date la plus récente au moment de la création
                if (!empty($this->db->idate($object->date_commande))) {
                    $this->line->dc4_date_initiale = $this->db->idate($object->date_commande);
                } elseif (!empty($this->db->idate($object->date_approve2))) {
                    $this->line->dc4_date_initiale = $this->db->idate($object->date_approve2);
                } elseif (!empty($this->db->idate($object->date_approve))) {
                    $this->line->dc4_date_initiale = $this->db->idate($object->date_approve);
                } elseif (!empty($this->db->idate($object->date_valid))) {
                    $this->line->dc4_date_initiale = $this->db->idate($object->date_valid);
                } else {
                    $this->line->dc4_date_initiale = $this->db->idate($object->date);
                } 

                $this->line->libelle_poste_cctp =  "";
                $this->line->sps_date_remise = $this->db->idate(dol_now());

                //print("Test Date commande : ".$object->date_commande);
                //var_dump($object);

                
                $result = $this->line->insert();

                print $db;

                $this->lines = array();

                $sql = "SELECT *";
                $sql.= " FROM ".MAIN_DB_PREFIX."DC4_deleg_csst WHERE `fk_object` = ".$object->id." AND `fk_element` = '".$object->element."'";

                dol_syslog("DC4::fetch sql=".$sql, LOG_DEBUG);

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

                            $this->lines[$i] = $obj;

                            $this->lines[$i]->dc4_object_declaration = $obj->dc4_object_declaration ? $obj->dc4_object_declaration : 0;
                            $this->lines[$i]->dc4_date_initiale = trim($obj->dc4_date_initiale);
                            $this->lines[$i]->dc4_hypothese = $obj->dc4_hypothese ? $obj->dc4_hypothese : 0;
                            $this->lines[$i]->avance = $obj->avance ? $obj->avance : 0;
                            $this->lines[$i]->dc4_documents_fournis =  $obj->dc4_documents_fournis ? $obj->dc4_documents_fournis : 0;
                            $this->lines[$i]->paiement_direct = $obj->paiement_direct ? $obj->paiement_direct : 0;
                            $this->lines[$i]->libelle_poste_cctp =  trim($obj->libelle_poste_cctp);
                            $this->lines[$i]->sps_travaux =  $obj->sps_travaux ? $obj->sps_travaux : 0;
                            $this->lines[$i]->sps_date_remise = trim($obj->sps_date_remise);
                            $this->lines[$i]->cissct =  $obj->cissct ? $obj->cissct : 0;
                            $this->lines[$i]->DIUO =  $obj->DIUO ? $obj->DIUO : 0;
                            $this->lines[$i]->responsabilite =  $obj->responsabilite ? $obj->responsabilite : 0;

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
        
        $dc4_object_declaration =  GETPOST('dc4_object_declaration');
        $dc4_date_initiale = strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['dc4_date_initialemonth'], $_POST['dc4_date_initialeday'], $_POST['dc4_date_initialeyear']));
        $dc4_hypothese =  GETPOST('dc4_hypothese');
        $avance = GETPOST('avance');
        $dc4_documents_fournis = GETPOST('dc4_documents_fournis');
        $paiement_direct = GETPOST('paiement_direct');
        $libelle_poste_cctp = GETPOST('libelle_poste_cctp');
        $sps_travaux = GETPOST('sps_travaux');
        $sps_date_remise = strftime('%Y-%m-%d', dol_mktime(12, 0 , 0, $_POST['sps_date_remisemonth'], $_POST['sps_date_remiseday'], $_POST['sps_date_remiseyear']));
        $cissct =  GETPOST('cissct');
        $DIUO =  GETPOST('DIUO');
        $responsabilite =  GETPOST('responsabilite');

		$line = new DC4Line($this->db);
		
		$result = $line->fetch($lineid);
		
		if ($result)
		{
            $line->field = $field;

            $line->dc4_object_declaration = $dc4_object_declaration ;
            $line->dc4_date_initiale = $dc4_date_initiale ;
            $line->dc4_hypothese = $dc4_hypothese ;
            $line->avance = $avance ;
            $line->dc4_documents_fournis = $dc4_documents_fournis ;
            $line->paiement_direct = $paiement_direct ;
            $line->libelle_poste_cctp = $libelle_poste_cctp ;
            $line->sps_travaux = $sps_travaux ;
            $line->sps_date_remise = $sps_date_remise ;
            $line->cissct = $cissct ;
            $line->DIUO = $DIUO ;
            $line->responsabilite = $responsabilite ;
	
			$result = $line->update();

			if ($result > 0)
			{		
				$this->fetch();
			
				$this->error = $langs->trans('DC4LineUpdated');		
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
			$this->error = $langs->trans('DC4LineDoesNotExist');
			return 0;
		}
	        
    }
        	
}

/**
 *	\class      	DC4Line
 *	\brief      	Class to manage margins
 *	\remarks		Uses lines of llx_DC4_deleg_csst tables
 */

class DC4Line
{
    var $db;
    var $error;

    var $oldline;


	var $rowid;
	var $fk_object;
	var $fk_element;

    var $dc4_object_declaration ;
    var $dc4_date_initiale ;
    var $dc4_hypothese ;
    var $avance ;
    var $dc4_documents_fournis ;
    var $paiement_direct ;
    var $libelle_poste_cctp ;
    var $sps_travaux ;
    var $sps_date_remise;
    var $cissct ;
    var $DIUO ;
    var $responsabilite ;

   /**
	*  \brief  Constructeur de la classe
	*  @param  DB          handler acces base de donnees
	*/
    function __construct($DB)
    {
        $this->db = $DB;
    }

   /**
     *	\brief     	Insert line in database
     *	@param      notrigger		1 no triggers
     *	@return		int				<0 if KO, >0 if OK
     */
    function fetch($lineid = 0)
    {
        global $langs, $user, $conf;

        
        $sql = "SELECT *";
		$sql.= " FROM ".MAIN_DB_PREFIX."DC4_deleg_csst";
        $sql.= " WHERE `rowid` = ".$lineid." AND `fk_element` = '".$object->element."'";

        dol_syslog("DC4Line::fetch sql=".$sql);

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

                $this->line = $obj;

                $this->line->dc4_object_declaration = $obj->dc4_object_declaration ? $obj->dc4_object_declaration : 0;
                $this->line->dc4_date_initiale = trim($obj->dc4_date_initiale);
                $this->line->dc4_hypothese = $obj->dc4_hypothese ? $obj->dc4_hypothese : 0;
                $this->line->avance = $obj->avance ? $obj->avance : 0;
                $this->line->dc4_documents_fournis =  $obj->dc4_documents_fournis ? $obj->dc4_documents_fournis : 0;
                $this->line->paiement_direct = $obj->paiement_direct ? $obj->paiement_direct : 0;
                $this->line->libelle_poste_cctp =  trim($obj->libelle_poste_cctp);
                $this->line->sps_travaux =  $obj->sps_travaux ? $obj->sps_travaux : 0;
                $this->line->sps_date_remise = trim($obj->sps_date_remise);
                $this->line->cissct =  $obj->cissct ? $obj->cissct : 0;
                $this->line->DIUO =  $obj->DIUO ? $obj->DIUO : 0;
                $this->line->responsabilite =  $obj->responsabilite ? $obj->responsabilite : 0;
				
                //$this->db->free($result);
				
				return $this->rowid;
            }
            else
            {
                $this->error = $langs->trans('DC4LineDoesNotExist');
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
        global $langs, $user, $conf, $object;
		
        $this->db->begin();
       
        //
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."DC4_deleg_csst";
        $sql.= " (`fk_object`, `fk_element`, `dc4_date_initiale`, `libelle_poste_cctp`, `sps_date_remise`)";
        $sql.= " VALUES (".$this->fk_object.",";
        $sql.= " '".$this->fk_element."', ";
        $sql.= " '".$this->dc4_date_initiale."', ";
        $sql.= " '".$this->libelle_poste_cctp."', ";
        $sql.= " '".$this->sps_date_remise."'";
        $sql.= ')';

        dol_syslog("DC4Line::insert sql=".$sql);

        $resql = $this->db->query($sql);
      if ($resql)
        {			

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('LINEDC4_INSERT', $this, $user ,$langs, $conf);
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
        global $langs, $user, $conf, $object;


		// Clean parameters

        $this->dc4_object_declaration = $this->dc4_object_declaration ? $this->dc4_object_declaration : 0;
        $this->dc4_date_initiale = trim($this->dc4_date_initiale);
        $this->avance = $this->avance ? $this->avance : 0;
        $this->dc4_documents_fournis =  $this->dc4_documents_fournis ? $this->dc4_documents_fournis : 0;
        $this->paiement_direct = $this->paiement_direct ? $this->paiement_direct : 0;
        $this->libelle_poste_cctp =  trim($this->libelle_poste_cctp);
        $this->sps_travaux =  $this->sps_travaux ? $this->sps_travaux : 0;
        $this->sps_date_remise = trim($this->sps_date_remise);
        $this->cissct =  $this->cissct ? $this->cissct : 0;
        $this->DIUO =  $this->DIUO ? $this->DIUO : 0;
        $this->responsabilite =  $this->responsabilite ? $this->responsabilite : 0;
				
        $this->db->begin();

        //
        $sql = "UPDATE ".MAIN_DB_PREFIX."DC4_deleg_csst";
        $sql.= " SET rowid = '".$object->id."'";
        if ($this->field == "dc4_object_declaration") {
            $sql.= ", `dc4_object_declaration` = '".$this->dc4_object_declaration."'";
        }
        if ($this->field == "dc4_date_initiale") {
            $sql.= ", `dc4_date_initiale` = '".$this->dc4_date_initiale."'";
        } elseif ($this->dc4_object_declaration != 3 ){ //On vérifie la date initiale sauf si on est sur un acte modificatif
            // On récupère la date la pls avancée dans la procédure de passage de commande.
            if ($this->dc4_date_initiale !== $this->db->idate($object->date_commande)) {
                    $sql.= ", `dc4_date_initiale` = '".$this->db->idate($object->date_commande)."'";
                    $date_test = "date_commande :".$this->db->idate($object->date_commande);
                } elseif (!empty($this->db->idate($object->date_approve2))) {
                   $sql.= ", `dc4_date_initiale` = '".$this->db->idate($object->date_approve2)."'";
                   $date_test = "date_approve2 :".$this->db->idate($object->date_approve2);
                } elseif (!empty($this->db->idate($object->date_approve))) {
                    $sql.= ", `dc4_date_initiale` = '".$this->db->idate($object->date_approve)."'";
                    $date_test = "date_approve :".$this->db->idate($object->date_approve);
                } elseif (!empty($this->db->idate($object->date_valid))) {
                    $sql.= ", `dc4_date_initiale` = '".$this->db->idate($object->date_valid)."'";
                    $date_test = "date_valid :".$this->db->idate($object->date_valid);
                }
        }
        if ($this->field == "avance") {
		  $sql.= ", `avance` = '".$this->avance."'";
        }
        if ($this->field == "dc4_hypothese") {
            $sql.= ", `dc4_hypothese` = '".$this->dc4_hypothese."'";
        }
        if ($this->field == "dc4_documents_fournis") { 
            $sql.= ", `dc4_documents_fournis` = '".$this->dc4_documents_fournis."'";
        }
        if ($this->field == "paiement_direct") {
            $sql.= ", `paiement_direct` = '".$this->paiement_direct."'";
        }
        if ($this->field == "libelle_poste_cctp") {
            $sql.= ", `libelle_poste_cctp` = '".$this->libelle_poste_cctp."'";
        }
        if ($this->field == "sps_travaux") {
            $sql.= ", `sps_travaux` = '".$this->sps_travaux."'";
        }
        if ($this->field == "sps_date_remise") {
            $sql.= ", `sps_date_remise` = '".$this->sps_date_remise."'";
        }
        if ($this->field == "cissct") {
            $sql.= ", `cissct` = '".$this->cissct."'";
        }
        if ($this->field == "DIUO") {
            $sql.= ", `DIUO` = '".$this->DIUO."'";
        }
        if ($this->field == "responsabilite") {
            $sql.= ", `responsabilite` = '".$this->responsabilite."'";
        }
		$sql.= " WHERE ( fk_object = '".$object->id."' OR fk_object = '".$object->rowid."') AND `fk_element` = '".$object->element."'";

        //var_dump($sql);
        //var_dump($object);
        //var_dump($this);

        dol_syslog("DC4Line::update sql=".$sql);

        $resql = $this->db->query($sql);

        if ($resql)
        {

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('LINEDC4_UPDATE', $this, $user ,$langs, $conf);
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
