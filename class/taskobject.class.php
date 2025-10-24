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
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */



require_once DOL_DOCUMENT_ROOT ."/core/class/commonobject.class.php";

require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';


/**
 *	\class      	DetailsLine
 *	\brief      	Class to manage margins
 *	\remarks		Uses lines of llx_LMDB_projet_task tables
 */
class SetTask extends CommonObject
{
    var $db;
    var $error;

    var $oldline;

    var $id;
	var $rowid;

	var $fk_object;

    var $fk_task ;
    var $fk_category;

    var $context ;

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
    /*---------------------------------------------------------------------------------------------
    ---------------------------------------------------------------------------------------------
        /**
     * Fetch object lines from database
     *
     * @return  int     <0 if KO, >0 if OK
     */
    function fetch($context)
    {
        global $conf, $langs, $object;

        if ($object->fk_project != 0) 
        {
            $fk_object = $object->id;
            $this->tasks = array();

            $sql = "SELECT l.rowid as lineid, l.fk_task, l.fk_object, l.hook_context";
            $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_projet_task as l";
            $sql.= " WHERE l.hook_context = '".$context."'";
            $sql.= " AND l.fk_object = ".$object->id;

            dol_syslog("SetTask::fetch sql=".$sql, LOG_DEBUG);

            $resfetch = $this->db->query($sql);

            if ($resfetch > 0)
            {
                $num = $this->db->num_rows($resfetch);
                $i = 0 ;

                if ($num == 1)
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resfetch);

                        $this->tasks[$i] = $obj;
                        $this->tasks[$i]->fk_task = $obj->fk_task ? $obj->fk_task : 0;

                        $i++;
                    }
                } 
                else
                {

                    $this->task  = new SetTaskLine($this->db);
                
                    $this->task->fk_object = $object->id;
                    $this->task->fk_task = "0";
                    $this->task->hook_context = $context;
                    
                    $result = $this->task->insert();

                    $this->tasks = array();

                    dol_syslog(get_class($this)."::show_task", LOG_DEBUG);

                    $ret=$this->db->query($sql);

                    $n_task = $this->db->num_rows($ret);

                    if ($num == 1)
                    {
                        while ($i < $num)
                        {
                            $obj = $this->db->fetch_object($ret);

                            $this->tasks[$i] = $obj;

                            $this->tasks[$i]->fk_object = $obj->fk_object ? $obj->fk_object : 0;
                            $this->tasks[$i]->hook_context = trim($obj->hook_context);
                            $this->tasks[$i]->fk_task = $obj->fk_task ? $obj->fk_task : 0;
                            $this->tasks[$i]->label = trim($obj->label);

                            $i++;
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
    }

    function fetch2($context)
    {
        global $conf, $langs, $object;

        if ($object->fk_project != 0) 
        {
            $fk_object = $object->id;
            $this->tasks = array();

            $sql = "SELECT t.rowid as id, t.label as task, l.rowid as lineid, l.fk_task, l.fk_object, l.hook_context";
            $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_projet_task as l";
            $sql.= ", ".MAIN_DB_PREFIX."projet_task as t";
            $sql.= " WHERE l.hook_context = '".$context."'";
            $sql.= " AND l.fk_object = ".$object->id;
            $sql.= " AND t.rowid = l.fk_task";
            $sql.= " ORDER BY t.rowid ASC";

            dol_syslog("SetTask::fetch sql=".$sql, LOG_DEBUG);

            $resfetch = $this->db->query($sql);

            if ($resfetch > 0)
            {
                $num = $this->db->num_rows($resfetch);
                $i = 0 ;

                if ($num == 1)
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resfetch);

                        $this->tasks[$i] = $obj;
                        $this->tasks[$i]->fk_task = $obj->fk_task ? $obj->fk_task : 0;

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
    }

    function fetch_task($id)
    {
        global $conf, $langs, $object;

        if ($id != '0') 
        {

            $this->tasks = array();

            $sql = "SELECT t.rowid as id, t.label as task, t.fk_task_parent";
            $sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
            $sql.= "";
            $sql.= " WHERE";
            $sql.= " t.fk_projet = ".$id;
            //$sql.= " AND t.rowid = l.fk_task";
            $sql.= " AND t.fk_task_parent=0";
            $sql.= " ORDER BY t.rowid ASC";

            dol_syslog("SetTask::fetch_task sql=".$sql, LOG_DEBUG);

            $resfetch = $this->db->query($sql);

            if ($resfetch > 0)
            {
                $num = $this->db->num_rows($resfetch);
                $i = 0 ;

                if ($num >> '0')
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resfetch);

                        $this->tasks[$i] = $obj;
                        $this->tasks[$i]->fk_task = $obj->fk_task ? $obj->fk_task : 0;
                        $this->tasks[$i]->label = $obj->task;

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
    }

    function fetch_task_child($id, $poste)
    {
        global $conf, $langs, $object;

        if ($id != '0') 
        {

            $this->task_child = array();

            $sql = "SELECT t.rowid as id, t.label as task, t.fk_task_parent";
            $sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
            $sql.= "";
            $sql.= " WHERE";
            $sql.= " t.fk_projet = ".$id;
            //$sql.= " AND t.rowid = l.fk_task";
            $sql.= " AND t.fk_task_parent=".$poste;
            $sql.= " ORDER BY t.rowid ASC";

            dol_syslog("SetTask::fetch_task sql=".$sql, LOG_DEBUG);

            $resfetch1 = $this->db->query($sql);

            if ($resfetch1 > 0)
            {
                $num1 = $this->db->num_rows($resfetch1);
                $i1 = 0 ;

                if ($num1)
                {
                    while ($i1 < $num1)
                    {
                        $obj1 = $this->db->fetch_object($resfetch1);

                        $this->task_child[$i1] = $obj1;
                        $this->task_child[$i1]->id = $obj1->id ? $obj1->id : 0;
                        $this->task_child[$i1]->fk_task = $obj1->fk_task ? $obj1->fk_task : 0;
                        $this->task_child[$i1]->label = $obj1->task;

                        $i1++;
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
    }

function fetch_order_child($id, $poste)
    {
        global $conf, $langs, $object;

        // 0 : draft
        // 1 : validated (no more a draft)
        // 2 : approved (someone approved your request. If option SUPPLIER_ORDER_DOUBLE_APPROVAL is set, a second user with permission approve2 has also approved)
        // 3 : sent to supplier
        // 4 : received partially
        // 5 : received completely or closed
        // 9 : refused (Your leader disagree your request)

        // print $conf->global->LMDB_BUDGET_ORDER_STATUS ;

        if ($id != '0') 
        {

            $this->orders = array();

            $sql = "SELECT *";
            $sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseur as c";
            $sql.= " , ".MAIN_DB_PREFIX."LMDB_projet_task as l";
            $sql.= " WHERE";
            $sql.= " c.fk_projet = ".$id;
            $sql.= " AND c.rowid = l.fk_object";
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '0') // All
        {
            $sql.= " AND (c.fk_statut = 0";
            $sql.= " OR c.fk_statut = 1";
            $sql.= " OR c.fk_statut = 2";
            $sql.= " OR c.fk_statut = 3";
            $sql.= " OR c.fk_statut = 4";
            $sql.= " OR c.fk_statut = 5";
        }
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '1') // All without Draft
        {
            $sql.= " AND (c.fk_statut = 1";
            $sql.= " OR c.fk_statut = 2";
            $sql.= " OR c.fk_statut = 3";
            $sql.= " OR c.fk_statut = 4";
            $sql.= " OR c.fk_statut = 5";
        }
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '2') // Approved + Sent + Received + Received Partially
        {
            $sql.= " AND (c.fk_statut = 2";
            $sql.= " OR c.fk_statut = 3";
            $sql.= " OR c.fk_statut = 4";
            $sql.= " OR c.fk_statut = 5";
        }
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '3') // Sent + Received + Received Partially
        {
            $sql.= " AND (c.fk_statut = 3";
            $sql.= " OR c.fk_statut = 4";
            $sql.= " OR c.fk_statut = 5";
        }
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '4') // Received + Received Partially
        {
            $sql.= " AND (c.fk_statut = 4";
            $sql.= " OR c.fk_statut = 5";
        }
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS == '5') // Received
        {
            $sql.= " AND (c.fk_statut = 5";
        }
        if ($conf->global->LMDB_BUDGET_ORDER_STATUS_REFUSED == '1') 
        {
            $sql.= " OR c.fk_statut = 9)";
        }else{
            $sql.=")";
        }
            $sql.= " AND l.fk_task=".$poste;
            $sql.= " ORDER BY c.rowid ASC";

            dol_syslog("SetTask::fetch_orders sql=".$sql, LOG_DEBUG);

            $resfetch = $this->db->query($sql);

            if ($resfetch > 0)
            {
                $num = $this->db->num_rows($resfetch);
                $i = 0 ;

                if ($num)
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resfetch);

                        $this->orders[$i] = $obj;
                        $this->orders[$i]->id = $obj->id ? $obj->id : 0;
                        $this->orders[$i]->fk_task = $obj->fk_task ? $obj->fk_task : 0;
                        $this->orders[$i]->total_ht = $obj->total_ht;

                       // $this->total_ht += $this->order[$i]->total_ht; 

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
    }

       /**
     *  \brief Update a line 
     *
     *  @param     user             User who adds
     *  @return    int              <0 if KO, id the line added if OK
     */
    function updatetask($user)
    {
        global $langs, $conf, $object, $db;

        $lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
        $taskid = GETPOST('taskid');

        $task = new SetTaskLine($this->db);
        
        $result = $task->fetch2($lineid);
        
        if ($result)
        {
            $task->fk_task = $taskid;
    
            $result = $task->update();

            if ($result > 0)
            {       
                $this->fetch();
            
                $this->error = $langs->trans('SetTaskLineUpdated');     
                return $task->rowid;
            }
            else
            {
                $this->error = $this->task->error;

                return -2;
            }
        }
        else
        {
            $this->error = $langs->trans('SetTaskLineDoesNotExist');
            return 0;
        }
    }

     /**
     * Fetch object lines from database
     *
     * @return  int     <0 if KO, >0 if OK
     */
    function fetchcategory($context)
    {
        global $conf, $langs, $object;

        if ($object->fk_project != 0) 
        {
            $fk_object = $object->id;
            $this->categories = array();

            $sql = "SELECT l.rowid as lineid, l.fk_task, l.fk_object, l.hook_context, l.fk_category";
            $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_projet_task as l";
            $sql.= " WHERE l.hook_context = '".$context."'";
            $sql.= " AND l.fk_object = ".$object->id;

            dol_syslog("SetTask::fetchcategory sql=".$sql, LOG_DEBUG);

            $resfetch = $this->db->query($sql);

            if ($resfetch > 0)
            {
                $num = $this->db->num_rows($resfetch);
                $i = 0 ;

                if ($num == 1)
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resfetch);

                        $this->categories[$i] = $obj;
                        $this->categories[$i]->fk_category = $obj->fk_category ? $obj->fk_category : 0;

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
    }

    function fetchcategory2($context)
    {
        global $conf, $langs, $object;

        if ($object->fk_project != 0) 
        {
            $fk_object = $object->id;
            $this->categories = array();

            $sql = "SELECT c.rowid as id, c.category as category, l.rowid as lineid, l.fk_task, l.fk_object, l.hook_context, l.fk_category";
            $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_projet_task as l";
            $sql.= ", ".MAIN_DB_PREFIX."LMDB_poste_category as c";
            $sql.= " WHERE l.hook_context = '".$context."'";
            $sql.= " AND l.fk_object = ".$object->id;
            $sql.= " AND c.rowid = l.fk_category";
            $sql.= " ORDER BY t.rowid ASC";

            dol_syslog("SetTask::fetchcategory2 sql=".$sql, LOG_DEBUG);

            $resfetch = $this->db->query($sql);

            if ($resfetch > 0)
            {
                $num = $this->db->num_rows($resfetch);
                $i = 0 ;

                if ($num == 1)
                {
                    while ($i < $num)
                    {
                        $obj = $this->db->fetch_object($resfetch);

                        $this->categories[$i] = $obj;
                        $this->categories[$i]->fk_category = $obj->fk_category ? $obj->fk_category : 0;

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
    }

     /**
     *  \brief Update a line 
     *
     *  @param     user             User who adds
     *  @return    int              <0 if KO, id the line added if OK
     */
    function updatecategory($user)
    {
        global $langs, $conf, $object, $db;

        $lineid = GETPOST('lineid') ? GETPOST('lineid') : 0;
        $categoryid = GETPOST('categoryid');

        $category = new SetTaskLine($this->db);
        
        $result = $category->fetch2($lineid);
        
        if ($result)
        {
            $category->fk_category = $categoryid;
    
            $result = $category->updatecategory();

            if ($result > 0)
            {       
                $this->fetchcategory();
            
                $this->error = $langs->trans('SetCategoryLineUpdated');     
                return $category->rowid;
            }
            else
            {
                $this->error = $this->category->error;

                return -2;
            }
        }
        else
        {
            $this->error = $langs->trans('SetCategoryLineDoesNotExist'); 
            return 0;
        }
    }

}
/*---------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------*/

class SetTaskLine
{

    var $db;
    var $error;

    var $oldline;


    var $rowid;

    var $fk_object;

    var $fk_task ;
    var $hook_context ;

   /**
	*  \brief  Constructeur de la classe
	*  @param  DB          handler acces base de donnees
	*/
    function __construct($DB)
    {
        $this->db = $DB;
    }
	
       /**
     *  \brief      Insert line in database
     *  @param      notrigger       1 no triggers
     *  @return     int             <0 if KO, >0 if OK
     */
    function fetch($lineid = 0)
    {
        global $langs, $user, $conf;

        $sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_projet_task";
        $sql.= " WHERE `fk_object` = ".$object->id;
        $sql.= " AND `hook_context` = ".$context;

        dol_syslog("SetTaskLine::fetch sql=".$sql);

        $result = $this->db->query($sql);

        if ($result)
        {

            $num = $this->db->num_rows($result);

            if ($num)
            {
                $obj = $this->db->fetch_object($result);

                $this->rowid              = $obj->rowid ? $obj->rowid : 0;
                $this->fk_object          = $obj->fk_object ? $obj->fk_object : 0;

                $this->task               = $obj;
                $this->task->fk_task      = $obj->fk_task ? $obj->fk_task : 0;
                $this->task->hook_context = trim($obj->hook_context);
                
                //$this->db->free($result);
                
                return $this->rowid;
            }
            else
            {
                $this->error = $langs->trans('SetTaskLineDoesNotExist');
                return -1;
            }

        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;

            return -1;
        }
    }

    function fetch2($lineid = 0)
    {
        global $langs, $user, $conf;

        $sql = "SELECT *";
        $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_projet_task";
        $sql.= " WHERE `rowid` = ".$lineid;
        //$sql.= " AND `hook_context` = ".$context;

        dol_syslog("SetTaskLine::fetch sql=".$sql);

        $result = $this->db->query($sql);

        if ($result)
        {

            $num = $this->db->num_rows($result);

            if ($num)
            {
                $obj = $this->db->fetch_object($result);

                $this->rowid              = $obj->rowid ? $obj->rowid : 0;
                $this->fk_object          = $obj->fk_object ? $obj->fk_object : 0;

                $this->task               = $obj;
                $this->task->fk_task      = $obj->fk_task ? $obj->fk_task : 0;
                $this->task->hook_context = trim($obj->hook_context);
                $this->task->fk_category      = $obj->fk_category ? $obj->fk_category : 0;
                
                //$this->db->free($result);
                
                return $this->rowid;
            }
            else
            {
                $this->error = $langs->trans('SetTaskLineDoesNotExist');
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
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."LMDB_projet_task";
        $sql.= " (`fk_object`, `hook_context`, `fk_task`)";
        $sql.= " VALUES (".$this->fk_object.",";
        $sql.= " '".$this->hook_context."', ";
        $sql.= " '".$this->fk_task."'";
        $sql.= ')';

        dol_syslog("SetTaskLine::insert sql=".$sql);

        $resql = $this->db->query($sql);

      if ($resql)
        {			

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('SETTASKLINE_INSERT', $this, $user ,$langs, $conf);
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

        $this->fk_task = $this->fk_task ? $this->fk_task : 0;
				
        $this->db->begin();

        //
        $sql = "UPDATE ".MAIN_DB_PREFIX."LMDB_projet_task";
        $sql.= " SET fk_task = ".$this->fk_task;
		$sql.= " WHERE rowid = ".$this->rowid;

        dol_syslog("SetTask::update sql=".$sql);

        //var_dump($sql);

        $resql = $this->db->query($sql);

        if ($resql)
        {

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('SETTASKLINE_UPDATE', $this, $user ,$langs, $conf);
                if ($result < 0) {
                    $this->error = $langs->trans('ErrorCallingTrigger');
                    $this->db->rollback();
                    return -1;
                }
                // Fin appel triggers
            }

            $this->db->commit();

        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;
            $this->db->rollback();

            return -2;
        }
    }

    /**
     *  \brief      Update line in database
     *  @param      notrigger       1 no triggers
     *  @return     int             <0 if KO, >0 if OK
     */
    function updatecategory($notrigger = 0)
    {
        global $langs, $user, $conf;


        // Clean parameters

        $this->fk_category = $this->fk_category ? $this->fk_category : 0;
                
        $this->db->begin();

        //
        $sql = "UPDATE ".MAIN_DB_PREFIX."LMDB_projet_task";
        $sql.= " SET fk_category = ".$this->fk_category;
        $sql.= " WHERE rowid = ".$this->rowid;

        dol_syslog("SetTask::updatecategory sql=".$sql);

        //var_dump($sql);

        $resql = $this->db->query($sql);

        if ($resql)
        {

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('SETCATEGORYLINE_UPDATE', $this, $user ,$langs, $conf);
                if ($result < 0) {
                    $this->error = $langs->trans('ErrorCallingTrigger');
                    $this->db->rollback();
                    return -1;
                }
                // Fin appel triggers
            }

            $this->db->commit();

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
