<?php
/*
 * Copyright (C) 2020		 Pierre Ardoin       <mapiolca@me.com>.
 *
 * This program is free software; you can redistribute it and/or modifyion 2.0 (the "License");
 * it under the terms of the GNU General Public License as published bypliance with the License.
 * the Free Software Foundation; either version 3 of the License, or
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

class Rating
{

    var $db;
    var $error;
    var $ismultientitymanaged = 0;  // 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

    var $lines = array();
    var $line;

    function __construct($db)
    {
        $this->db = $db;
    }

    function init(&$object, $type = FALSE)
    {
        if ($type === FALSE) {
            $type = $object->element;
        }
        $this->$type = new RatingChild($this->db, $type, $object);
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
     * @return  int     <0 if KO, >0 if OK
     */
    function fetch()
    {
        global $conf, $langs, $object;
            
        $this->lines = array();

        $sql = "SELECT r.rowid, r.fk_object, r.fk_element, r.elementtype, r.elementrated, r.rate, r.comment"; 
        $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_rating AS r";      
        $sql.= " WHERE r.fk_object  = ".$object->id." AND r.elementrated = '".$type."' AND r.elementtype = '".$type."'";             
        $sql.= " ORDER BY r.rowid";


        dol_syslog("Rating::fetch sql=".$sql, LOG_DEBUG);

        $result = $this->db->query($sql);
        
        
        
        if ($result > 0)
        {
            $num = $this->db->num_rows($result);
            $i = 0;

            if ($num)
            {
                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($result);
                    
                    $this->lines[$i]            = $obj;
                    $this->lines[$i]->rate     = trim($obj->rate);
                    $this->lines[$i]->comment     = trim($obj->comment);
                    $this->lines[$i]->elementrated     = trim($obj->elementrated);
                    $this->lines[$i]->elementtype     = trim($obj->elementtype);
                    
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
            
        $line = new RatingChild($this->db, $type, $object, $lineid);
        
        $result = $line->fetch($lineid);
        
        if ($result)
        {
            $line->delete($lineid);
            
            $this->fetch();
            $this->error = $langs->trans('RatingLineDeleted');

            return 1;
        }
        else
        {
            $this->error = $langs->trans('RatingLineDoesNotExist');
            return 0;
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
        $rate  = GETPOST('edit_rating');     
        $comment = GETPOST('comment');     

        $line = new RatingChild($this->db, $type, $object);
        
        $result = $line->fetch($lineid);
        
        if ($result)
        {
    
            $result = $line->update($lineid, $rate, $comment);

            if ($result > 0)
            {       
                $this->fetch();
            
                $this->error = $langs->trans('RatingLineUpdated');      
                return $lineid;
            }
            else
            {
                $this->error = $this->line->error;

                return -2;
            }
        }
        else
        {
            $this->error = $langs->trans('RatingLineDoesNotExist');
            return 0;
        }
            
    }


}

class RatingChild
{
    var $error;

    var $oldline;



    var $fk_object;
    var $fk_element;

    var $rate;
    var $comment;

    private $db, $type, $object, $children, $lineid, $rowid;

    function __construct($db, $type, &$object)
    {
        $this->db       = $db;
        $this->type     = $type;
        $this->object   = $object;
        $this->children = array();
    }

    function collect_rate()
    {
        $sql   = array();
        $sql[] = "SELECT r.`rowid`, r.`user_id`, r.`entity`, r.`rate`, r.`comment`, r.`datec`, r.`fk_element`, r.`elementtype`, r.`elementrated` "
            .", u.`lastname`, u.`firstname`, e.`label` as name_entity"
            ." FROM `".MAIN_DB_PREFIX."LMDB_rating` r "
            ." LEFT JOIN `".MAIN_DB_PREFIX."user` u ON r.`user_id` = u.`rowid` "
            ." LEFT JOIN `".MAIN_DB_PREFIX."entity` e ON r.`entity` = e.`rowid` "
            ." WHERE `fk_element` = '{$this->object->id}' AND `elementtype` = '{$this->object->element}' AND `elementrated` = '{$this->type}' "
            ." ORDER BY r.datec DESC ";
        foreach ($this->children as $child) {
            $sql[] = "SELECT r.`rowid`, r.`user_id`, r.`rate`, r.`comment`, r.`datec`, r.`fk_element`, r.`elementtype`, r.`elementrated` "
                .", u.`lastname`, u.`firstname`, e.`label` as name_entity"
                ." FROM `".MAIN_DB_PREFIX."LMDB_rating` r "
                ." LEFT JOIN `".MAIN_DB_PREFIX."user` u ON r.`user_id` = u.`rowid` "
                ." LEFT JOIN `".MAIN_DB_PREFIX."entity` e ON r.`entity` = e.`rowid` "
                ." LEFT JOIN `".MAIN_DB_PREFIX."{$child->table}` c ON r.`fk_element` = c.`rowid` "
                ." WHERE c.`{$child->fk_link}` = '{$this->object->id}' AND `elementtype` = '{$child->elementtype}' AND `elementrated` = '{$child->elementrated}' "
                ." ORDER BY r.datec DESC ";
        }
        $sql = "( ".implode(' )UNION ( ', $sql)." ) ORDER BY datec DESC";

        dol_syslog("RATING::$sql", LOG_DEBUG);
        return $this->db->query($sql);
    }

    function display()
    {
        global $langs;

        $resql = $this->collect_rate();

        $num = $this->db->num_rows($resql);

        if ($num == 0) {
            $html = '<strike>N/A</strike>';
        } else {
            $tooltip   = '<div width="100%">';
            $tooltipAr = array();
            $moy       = 0;
            $i         = 0;
            while ($i < $num) {
                $rate = $this->db->fetch_object($resql);
                $moy  += $rate->rate;
                if ($i < 10) {
                    $rateRow = '<div class="star-ratings-sprite"><span style="width:'.abs(20 * $rate->rate).'%" ';
                    if ($rate->elementrated != $this->type) {
                        if (class_exists($rate->elementrated)) {
                            $object      = new $rate->elementrated($this->db);
                            $object->fetch($rate->fk_element);
                            $childMarker = '<div class="child-marker-rating"> ('.$object->getNomUrl(0).')</div>';
                        } else {
                            $childMarker = '<div class="child-marker-rating"> '.$langs->trans('Child'.$rate->elementrated,
                                    $rate->fk_element).'</div>';
                        }
                    } else {
                        $childMarker = '';
                    }
                    if ($rate->rate < 0) {
                        $rateRow .= ' class="star-ratings-sprite-rating-negative"></span></div> '.$childMarker.'<br />';
                    } else {
                        $rateRow .= ' class="star-ratings-sprite-rating"></span></div> '.$childMarker.'<br />';
                    }
                    $rateRow .= $langs->trans('ByOn', $rate->firstname, $rate->lastname, $rate->name_entity, dol_print_date($rate->datec));
                    if (!empty($rate->comment)) {
                        $rateRow .= ' <br /><big>&ldquo; '.$rate->comment.' &rdquo;</big>';
                    }
                    $tooltipAr[] = $rateRow;
                }
                $i++;
            }
            $tooltip .= implode('<hr />', $tooltipAr).'</div>';
            $html    = '<div class="ratingMod classfortooltip" title="'.dol_escape_htmltag($tooltip).'"><div class="star-ratings-sprite">';
            $moy     = $moy * 20 / $i;
            if ($moy < 0) {
                $html .= '<span style="width:'.abs($moy).'%" class="star-ratings-sprite-rating-negative"></span></div>';
            } else {
                $html .= '<span style="width:'.$moy.'%" class="star-ratings-sprite-rating"></span></div>';
            }
            $html .= '<sup>('.$i.')</sup></div>';
        }
        return $html.' - <a href="" title="'.$langs->trans('AddRating'.$this->type).'" class="rating-add" elementrated="'.$this->type.'" elementtype="'.$this->object->element.'" fk_element="'.$this->object->id.'" >'.$langs->trans('AddRating').'</a> ';
    }

    function add_child($elementrated, $elementtype, $table, $fk_link)
    {
        $tmp               = new stdClass();
        $tmp->elementrated = $elementrated;
        $tmp->elementtype  = $elementtype;
        $tmp->table        = $table;
        $tmp->fk_link      = $fk_link;
        $this->children[]  = $tmp;
    }


       /**
     *  \brief      Insert line in database
     *  @param      notrigger       1 no triggers
     *  @return     int             <0 if KO, >0 if OK
     */
    function fetch($lineid = 0)
    {
        global $langs, $user, $conf;

        //
        $sql = "SELECT `rowid`, `fk_object`, `elementrated`, `elementtype`, `rate`, `comment`";
        $sql.= " FROM ".MAIN_DB_PREFIX."LMDB_rating";
        $sql.= " WHERE `rowid` = ".$lineid;

        dol_syslog("RatingLine::fetch sql=".$sql);

        $result = $this->db->query($sql);
        $num = 0;

        if ($result)
        {
            $num = $this->db->num_rows($result);

            if ($num)
            {
                $obj = $this->db->fetch_object($result);

                $this->rowid        = $obj->rowid ? $obj->rowid : 0;
                $this->fk_object    = $obj->fk_objectdet ? $obj->fk_objectdet : 0;
                $this->elementrated   = $obj->elementrated;
                $this->elementtype   = $obj->elementtype;
                
                $this->rate        = trim($obj->rate);
                $this->comment       = $obj->comment ? $obj->comment : 0;
                
                //$this->db->free($result);
                
                return $this->rowid;
            }
            else
            {
                $this->error = $langs->trans('RatingLineDoesNotExist');
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
     *  Delete line in database
     *
     *  @return  int  <0 si ko, >0 si ok
     */
    function delete($lineid)
    {
        global $conf, $user, $langs;

        $error=0;

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."LMDB_rating WHERE rowid=".$lineid;

        dol_syslog("RatingLine::delete sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            // Appel des triggers
            include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
            $interface=new Interfaces($this->db);
            $result=$interface->run_triggers('LINERATING_DELETE',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            // Fin appel triggers

            return 1;
        }
        else
        {
            $this->error=$this->db->lasterror();
            dol_syslog("RatingLine::delete ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  \brief      Update line in database
     *  @param      notrigger       1 no triggers
     *  @return     int             <0 if KO, >0 if OK
     */
    function update($lineid, $rate, $comment)
    {
        global $langs, $user, $conf;
                
        $this->db->begin();

        //
        $sql = "UPDATE ".MAIN_DB_PREFIX."LMDB_rating";
        $sql.= " SET `rate` = '".$rate."'";
        $sql.= ", `comment` = '".$comment."'";
        $sql.= " WHERE rowid = ".$lineid;


        dol_syslog("RatingLine::update sql=".$sql);

        $resql = $this->db->query($sql);
        if ($resql)
        {

            if (! $notrigger)
            {
                // Appel des triggers
                include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                $interface = new Interfaces($this->db);
                $result = $interface->run_triggers('LINERATING_UPDATE', $this, $user ,$langs, $conf);
                if ($result < 0) {
                    $this->error = $langs->trans('ErrorCallingTrigger');
                    $this->db->rollback();
                    return -1;
                }
                // Fin appel triggers
            }

            $this->db->commit();

            return $lineid;

        }
        else
        {
            $this->error = $this->db->error()." sql=".$sql;
            $this->db->rollback();

            return -2;
        }
    } 

}