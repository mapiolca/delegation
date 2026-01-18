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
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';


/**
 *      \class      Delegations
 *      \brief      Class to manage margin
 */
class Delegation extends CommonObject
{
	var $db;
	var $error;
	var $element = 'delegation';
	var $table_element = '';
	var $table_element_line = 'delegation_det';
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

		$sql = "SELECT dd.rowid, dd.fk_object, dd.fk_element, dd.fk_facture_fourn, dd.label, dd.amount";
		$sql.= " FROM ".MAIN_DB_PREFIX."delegation_det AS dd";		
		$sql.= " WHERE dd.fk_object  = ".$object->id." AND dd.fk_element = '".$this->db->escape($object->element)."'";		       
        $sql.= " ORDER BY dd.rowid";


		dol_syslog("Delegation::fetch sql=".$sql, LOG_DEBUG);

		$result = $this->db->query($sql);
		
		
		
		if ($result)
		{
            $num = $this->db->num_rows($result);
            $i = 0;

            if ($num)
			{
                while ($i < $num)
				{
        			$obj = $this->db->fetch_object($result);
					
					$this->lines[$i]					= $obj;
					$this->lines[$i]->fk_facture_fourn	= $obj->fk_facture_fourn ? (int) $obj->fk_facture_fourn : 0;
					$this->lines[$i]->label				= trim($obj->label);
					
                    $i++;
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

	function getSumDelegation()
	{
		$lines = $this->lines;
		$total = 0;
		
		for ($i=0; $i<sizeof($lines); $i++)
		{
			$total = $total + $lines[$i]->amount;

		}	
	
		return $total;
	}
	
    /**
     *  \brief Delete a line 
     *
     *  @param     lineid           Line id of propal
     *  @return    int              <0 if KO, id the line added if OK
     */	
	function deleteline($user)
	{
		global $conf, $langs;
			
		$lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
			
		$line = new DelegationLine($this->db);
		
		$result = $line->fetch($lineid);
		
		if ($result)
		{
			$line->delete();
			
			$this->fetch();
			$this->error = $langs->trans('DelegationLineDeleted');

			return 1;
		}
		else
		{
			$this->error = $langs->trans('DelegationLineDoesNotExist');
			return 0;
		}
	}
	
    /**
     *  \brief Add a line 
     *
     *  @param     user             User who adds
     *  @return    int              <0 if KO, id the line added if OK
     */
	function addline($user)
	{
		return $this->addsupplierinvoice($user);
	}

	/**
	 *  \brief Add a supplier invoice line
	 *
	 *  @param     user             User who adds
	 *  @return    int              <0 if KO, id the line added if OK
	 */
	function addsupplierinvoice($user)
	{
		global $langs, $conf, $object;

		$fkFactureFourn = (int) GETPOST('fk_facture_fourn', 'int');

		if ($fkFactureFourn <= 0) {
			$this->error = $langs->trans('DelegationSupplierInvoiceMissing');
			return -2;
		}

		// EN: Load supplier invoice and validate eligibility.
		// FR: Charger la facture fournisseur et valider l'éligibilité.
		$invoice = new FactureFournisseur($this->db);
		$result = $invoice->fetch($fkFactureFourn);
		if ($result <= 0) {
			$this->error = $langs->trans('DelegationSupplierInvoiceNotFound');
			return -2;
		}

		if (! empty($conf->global->DELEGATION_PAYMENT_MODE_ID) && (int) $invoice->fk_mode_reglement !== (int) $conf->global->DELEGATION_PAYMENT_MODE_ID) {
			$this->error = $langs->trans('DelegationSupplierInvoiceNotAllowed');
			return -2;
		}

		if (! empty($object->fk_project) && (int) $invoice->fk_projet !== (int) $object->fk_project) {
			$this->error = $langs->trans('DelegationSupplierInvoiceNotAllowed');
			return -2;
		}

		$alreadyPaid = $invoice->getSommePaiement();
		$remaining = price2num($invoice->total_ttc - $alreadyPaid, 'MT');
		if ($remaining <= 0) {
			$this->error = $langs->trans('DelegationSupplierInvoiceNotAllowed');
			return -2;
		}

		// EN: Prevent duplicate links for the same supplier invoice.
		// FR: Empêcher les doublons pour la même facture fournisseur.
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."delegation_det";
		$sql.= " WHERE fk_object = ".(int) $object->id;
		$sql.= " AND fk_element = '".$this->db->escape($object->element)."'";
		$sql.= " AND fk_facture_fourn = ".(int) $fkFactureFourn;
		$resql = $this->db->query($sql);
		if ($resql && $this->db->num_rows($resql) > 0) {
			$this->error = $langs->trans('DelegationSupplierInvoiceAlreadyLinked');
			return -2;
		}

		// EN: Insert line with remaining amount as default.
		// FR: Insérer la ligne avec le reste à payer par défaut.
		$this->line  = new DelegationLine($this->db);
		$this->line->fk_object = $object->id;
		$this->line->fk_element = $object->element;
		$this->line->fk_facture_fourn = $fkFactureFourn;
		$this->line->label = $invoice->ref;
		$this->line->amount = $remaining;

		$result = $this->line->insert();

		if ($result > 0) {
			$this->fetch();
			$this->error = $langs->trans('DelegationLineAdded');
			return 1;
		}

		$this->error = $this->line->error;
		return -2;
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
		$label 	= GETPOST('label');		
		$amount 	= price2num(GETPOST('amount'));		

		$line = new DelegationLine($this->db);
		
		$result = $line->fetch($lineid);
		
		if ($result)
		{
			$line->label = $label;	
			$line->amount = $amount;
	
			$result = $line->update();

			if ($result > 0)
			{		
				$this->fetch();
			
				$this->error = $langs->trans('DelegationLineUpdated');		
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
			$this->error = $langs->trans('DelegationLineDoesNotExist');
			return 0;
		}
	        
    }
        	
}

/**
 *	\class      	DelegationLine
 *	\brief      	Class to manage margins
 *	\remarks		Uses lines of llx_delegation_det tables
 */
class DelegationLine
{
    var $db;
    var $error;

    var $oldline;


	var $rowid;
	var $fk_object;
	var $fk_element;
	
	var $label;
	var $amount;
	var $fk_facture_fourn;

	function __construct($db)
    {
        $this->db = $db;
    }


   /**
	*  \brief  Constructeur de la classe
	*  @param  DB          handler acces base de donnees
	*/
    //function DelegationLine($db)
    //{
    //    $this->db = $db;
    //}

	 /**
     * 	Delete line in database
     *
     *	@return	 int  <0 si ko, >0 si ok
     */
    function delete()
    {
        global $conf, $user, $langs;

		$error=0;

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."delegation_det WHERE rowid=".$this->rowid;

        dol_syslog("DelegationLine::delete sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            // Appel des triggers
            include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
            $interface=new Interfaces($this->db);
            $result=$interface->run_triggers('LINEDELEGATION_DELETE',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            // Fin appel triggers

            return 1;
        }
        else
        {
            $this->error=$this->db->lasterror();
            dol_syslog("DelegationLine::delete ".$this->error, LOG_ERR);
            return -1;
        }
    }
	
   /**
     *	\brief     	Insert line in database
     *	@param      notrigger		1 no triggers
     *	@return		int				<0 if KO, >0 if OK
     */
    function fetch($lineid = 0)
    {
        global $langs, $user, $conf;

        //
        $sql = "SELECT `rowid`, `fk_object`, `fk_element`, `fk_facture_fourn`, `label`, `amount`";
		$sql.= " FROM ".MAIN_DB_PREFIX."delegation_det";
        $sql.= " WHERE `rowid` = ".$lineid;

        dol_syslog("DelegationLine::fetch sql=".$sql);

        $result = $this->db->query($sql);
        $num = 0;

        if ($result)
        {
            $num = $this->db->num_rows($result);

            if ($num)
            {
                $obj = $this->db->fetch_object($result);

				$this->rowid				= $obj->rowid ? $obj->rowid : 0;
				$this->fk_object			= $obj->fk_object ? $obj->fk_object : 0;
				$this->fk_element			= $obj->fk_element;
				$this->fk_facture_fourn		= $obj->fk_facture_fourn ? (int) $obj->fk_facture_fourn : 0;
				$this->label				= trim($obj->label);
				$this->amount				= $obj->amount ? $obj->amount : 0;
				
                //$this->db->free($result);
				
				return $this->rowid;
            }
            else
            {
                $this->error = $langs->trans('DelegationLineDoesNotExist');
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

	
		// Clean parameters
		$this->label = trim($this->label);
		$this->amount = $this->amount ? $this->amount : 0;

		
        $this->db->begin();

        
	    if (empty($this->label) || $this->amount==0) {

	    	$this->error = (empty($this->label) && $this->amount==0) ? $langs->trans('lmdb_error_empty_line') : ((empty($this->label) && $this->amount!=0) ? $langs->trans('lmdb_error_empty_label') : ((!empty($this->label) && $this->amount==0) ? $langs->trans('lmdb_error_amount_null') : 0));
	    	
	        $this->db->rollback();

			return -2;
	    } else {	    
    
	        //
	        $sql = "INSERT INTO ".MAIN_DB_PREFIX."delegation_det";
	        $sql.= " (`fk_object`, `fk_element`, `fk_facture_fourn`, `label`, `amount`)";
	        $sql.= " VALUES (".$this->fk_object.",";
	        $sql.= " '".$this->fk_element."', ";
	        $sql.= " ".(! empty($this->fk_facture_fourn) ? (int) $this->fk_facture_fourn : "NULL").",";
	        $sql.= " '".$this->db->escape($this->label)."', ";
	        $sql.= " ".$this->amount;
	        $sql.= ')';

	        dol_syslog("DelegationLine::insert sql=".$sql);

	        $resql = $this->db->query($sql);
	        if ($resql)
	        {			
	            //$this->update_ndfp_tms();

	            if (! $notrigger)
	            {
	                // Appel des triggers
	                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
	                $interface = new Interfaces($this->db);
	                $result = $interface->run_triggers('LINEDELEGATION_INSERT', $this, $user ,$langs, $conf);
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
		$this->label = trim($this->label);
		$this->amount = $this->amount ? $this->amount : 0;
				
        $this->db->begin();
	
        //
        $sql = "UPDATE ".MAIN_DB_PREFIX."delegation_det";
        $sql.= " SET `label` = '".$this->db->escape($this->label)."'";
		$sql.= ", `amount` = ".$this->amount;
		$sql.= " WHERE rowid = ".$this->rowid;


        dol_syslog("DelegationLine::update sql=".$sql);

        $resql = $this->db->query($sql);
        if ($resql)
        {

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('LINEDELEGATION_UPDATE', $this, $user ,$langs, $conf);
                if ($result < 0) {
                    $this->error = $langs->trans('ErrorCallingTrigger');
                    $this->db->rollback();
                    return -1;
                }
                // Fin appel triggers
            }

            $this->db->commit();

            return $this->rowid;

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
