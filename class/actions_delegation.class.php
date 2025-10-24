<?php

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

class Actionsdelegation 
{ 

	/**
	\* Constructor
	*
	\*  @param		DoliDB		$db      Database handler
	*/

	public function __construct($db)
	{
	    $this->db = $db;
	}


	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function getInputIdProf($parameters, &$object, &$action, $hookmanager) 
	{
		//print_r($parameters);
		//echo "action: " . $action;
		//print_r($object);

		global $conf, $user, $langs;
		global $form;

		if (empty($conf->delegation->enabled)) return 0;

		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		//$parameters=array('formlength'=>$formlength, 'selected'=>$preselected, 'idprof'=>$idprof, 'htmlname'=>$htmlname, 'country_code'=>$country_code);

		$currentcontext = explode(':', $parameters['currentcontext']);
		if (in_array('thirdpartycard', $currentcontext) && $idprof == 3 && isset($conf->global->LMDB_USE_IDPROF3_DICTIONARY))
		{		
			global $conf,$langs;
			$langs->load("dict");

			$htmlname = 'idprof'.$idprof;
			$out = "";

			$sql = "SELECT r.rowid, r.idprof3 as code, r.activity as label, r.active, c.code as country_code, c.label as country";
			$sql.= " FROM ".MAIN_DB_PREFIX."c_idprof3 as r, ".MAIN_DB_PREFIX."c_country as c";
			$sql.= " WHERE r.country_code=c.code";
			//$sql.= " COLLATE utf8_general_ci";
			$sql.= " AND r.active = 1";
			$sql.= " AND c.active = 1";
			if ($country_code && ! is_numeric($country_code)) $sql .= " AND c.code = '".$this->db->escape($country_code)."'";
			$sql.= " ORDER BY r.country_code, code, label ASC";

			dol_syslog(get_class($this)."::select_idprof3", LOG_DEBUG);
			$resql=$this->db->query($sql);
			if ($resql)
			{	$morecss.= "maxwidth200onsmartphone maxwidth500";
				$moreattrib.=' autocomplete="off"';
				$this->resprints = '<select class="flat'.($morecss?' '.$morecss:'').'"'.($moreattrib?' '.$moreattrib:'').' name="'.$htmlname.'" id="'.$htmlname.'" maxlength="'.$maxlength.'" value="'.$selected.'">';
				$num = $this->db->num_rows($resql);
				$i = 0;
				if ($num)
				{
					$country='';
					while ($i < $num)
					{
						$obj = $this->db->fetch_object($resql);
						if ($obj->code == 0) {
							$this->resprints.= '<option value="0">&nbsp;</option>';
						}
						else {
							if ($country_code == '' || $country_code != $obj->country_code)
							{
								// Show break
								$key=$langs->trans("Country".strtoupper($obj->country_code));
								$valuetoshow=($key != "Country".strtoupper($obj->country_code))?$obj->country_code." - ".$key:$obj->country_code;
								$this->resprints.= '<option value="-1" disabled>----- '.$valuetoshow." -----</option>\n";
								$country=$obj->country;
							}

							if ($selected > 0 && $selected == $obj->code)
							{
								$this->resprints.= '<option value="'.$obj->code.'" selected>'.$obj->code.' - '.$obj->label.'</option>';
							}
							else
							{
								$this->resprints.= '<option value="'.$obj->code.'">'.$obj->code.' - '.$obj->label.'</option>';
							}
						}
						$i++;
					}
				}
				$this->resprints.= '</select>';

				$this->resprints.='
					<script>
					        	$(document).ready(function () {
					        		$("#'.$htmlname.'").select2({
					        		    dir: "ltr",
					        			width: "resolve",		/* off or resolve */
										minimumInputLength: 0,
										language: select2arrayoflanguage,
					    				containerCssClass: ":all:",					/* Line to add class of origin SELECT propagated to the new <span class="select2-selection...> tag */
										templateResult: function (data, container) {	/* Format visible output into combo list */
						 					/* Code to add class of origin OPTION propagated to the new select2 <li> tag */
											if (data.element) { $(container).addClass($(data.element).attr("class")); }
										    //console.log(data.html);
											if ($(data.element).attr("data-html") != undefined) return htmlEntityDecodeJs($(data.element).attr("data-html"));		// If property html set, we decode html entities and use this
										    return data.text;
										},
										templateSelection: function (selection) {		/* Format visible output of selected value */
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

				if (! $error)
				{
					return 1 ; // or return 1 to replace standard code
				}
				else
				{
					$this->errors[] = 'Error message';
					return -1;
				}

			}
			else
			{
				dol_print_error($this->db);
			}	
		
		//return 0;

		}else{

			$this->resprints = "";
			return 0; 
		}

	}

		/**
	 *
	 */
	public function formObjectOptions($parameters=false, &$object, &$action='')
	{
		global $conf, $user, $langs, $db;
		global $form;

		if (empty($conf->delegation->enabled)) return 0;

		$langs->load('budget@delegation');

		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		$currentcontext = explode(':', $parameters['context']);

		$this->resprints = "\n".'<!-- BEGIN LMDB formObjectOptions -->'."\n";

		if ((in_array('ordersuppliercard', $currentcontext)) && !is_null($object->fk_project)){
			
			$context = 'ordersuppliercard';
			
			dol_include_once("/delegation/class/taskobject.class.php");
			$SetTask = new SetTask($db);

			if (!$error && !$cancel)
			{
			    $result = $SetTask->call($action, array($user));

			    if ($result > 0)
			    {
			        $message = $SetTask->error; //
			    }
			    else
			    {
			        $message = $SetTask->error;
			        $error = true;
			    }
			}

			$this->resprints.= '<tr><td>'.$langs->trans('lmdb_poste').' '.img_picto($langs->trans('lmdb_poste_help'), 'info', 'style="cursor:help"');

			//$this->resprints.= '<br>action : '.$action;
			//$this->resprints.= '<br>fk_project : '.$object->fk_project;

			if ($action != 'editposte'){
                $this->resprints.= '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=editposte&amp;id='.$object->id.'">'.img_edit($langs->trans('SetPoste'), 1).'</a></td>';

                $task = $SetTask->fetch($context);

				$task = $SetTask->fetch2($context);
				$numLines = sizeof($SetTask->tasks);
				if ($numLines) {
					for($i = 0; $i < $numLines; $i++){
						$task_details = $SetTask->tasks[$i];
						//var_dump(array($SetTask->tasks[$i]));
						$this->resprints.= "<td>".$task_details->task."</td>";
					}
				}
				else{
                	$this->resprints.= "<td></td>";
                }
					
                
                
			}

			if ($action == 'editposte') {

				$task = $SetTask->fetch($context);
                $numLines = sizeof($SetTask->tasks);
                //var_dump($numLines);
                for($i = 0; $i < $numLines; $i++){

                	$task_details = $SetTask->tasks[$i];
                	//var_dump(array($SetTask->tasks[$i]));
				
					$htmlname = 'taskid';
					$poste = '0';
					$sql = "SELECT t.rowid as id, t.label as task, t.fk_task_parent as poste";
					$sql.= " FROM ".MAIN_DB_PREFIX."projet_task as t";
					$sql.= " WHERE t.fk_projet=".$object->fk_project;
					$sql.= " AND t.fk_task_parent = ".$poste;
					$sql.= " ORDER BY t.rowid ASC";

					//$this->resprints.= '<br>sql : '.$sql;


					dol_syslog(get_class($this)."::select_task", LOG_DEBUG);
					$resql=$this->db->query($sql);

					
					if ($resql)
					{	$morecss.= "";
						$moreattrib.=' autocomplete="off"';
						$this->resprints.= '</td><td>';
						$this->resprints.= '<form name="task" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
						$this->resprints.='<input type="hidden" name="action" value="updatetask" />';
						$this->resprints.='<input type="hidden" name="lineid" value="'.$task_details->lineid.'"/>';
						$this->resprints.= '<select class="flat'.($morecss?' '.$morecss:'').'"'.($moreattrib?' '.$moreattrib:'').' name="'.$htmlname.'" id="'.$htmlname.'" maxlength="'.$maxlength.'" value="'.$selected.'">';
						
						$num = $this->db->num_rows($resql);
						//$this->resprints.= '<br>num : '.$num;
						
						$i = 0;
						if ($num)
						{	
							$rang=1;
							if ($task_details->fk_task != 0) {
								$task = $SetTask->fetch2($context);
								$numLines = sizeof($SetTask->tasks);
								if ($numLines) {
									for($i0 = 0; $i0 < $numLines; $i0++){
										$task_details = $SetTask->tasks[$i0];
										//var_dump(array($SetTask->tasks[$i]));
										$this->resprints.= '<option value="'.$task_details->fk_task.'">'.$langs->trans('Currently').' : '.$task_details->task.'</option>';
									}
								}
								else{
				                	$this->resprints.= "<td></td>";
				                }
							}
							$this->resprints.= '<option value="0"></option>';
							while ($i < $num)
							{	$rang++;
								$par0 = $resql->fetch_object();

								// Show break
								$this->resprints.= '<option value="'.$par0->id.'">'.$par0->task."</option>\n";

								$poste=$par0->id;

								$i++;
							}
						}
						$this->resprints.= '</select>&nbsp;<input type="submit" class="button" value="Modifier"></td>';
					}
				}
			}

			//var_dump($task_details->task);
			if (!empty($task_details->task) ) { // Non affiché si précédent vide
				
				$this->resprints.= '<tr><td>'.$langs->trans('lmdb_poste_category').' '.img_picto($langs->trans('lmdb_poste_category_help'), 'info', 'style="cursor:help"');

            	if ($action != 'editcategory') {	// affiché si pas en mode "editcategory"
            		$this->resprints.= '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=editcategory&amp;id='.$object->id.'">'.img_edit($langs->trans('SetCategory'), 1).'</a></td>';

            		$category= $SetTask->fetchcategory($context);

					$numLines = sizeof($SetTask->categories);
					if ($numLines) {
						for($i = 0; $i < $numLines; $i++){
							$task_category = $SetTask->categories[$i];
							//var_dump(array($SetTask->categories[$i]));

							if ($task_category->fk_category == '0' || is_null($task_category->fk_category)) {
								
								$this->resprints.="<td></td>";
							}else{

								$sql = "SELECT a.rowid as id, a.code as code, a.category as label, a.active, a.position";
								$sql.= " FROM ".MAIN_DB_PREFIX."LMDB_poste_category as a";
								$sql.= " WHERE";
								//$sql.= " a.country_code=c.code";
								//$sql.= " COLLATE utf8_general_ci";
								//$sql.= " a.active = 1";
								$sql.= " a.rowid=".$task_category->fk_category."";
								//$sql.= " AND"
								//$sql.= "c.active = 1";
								//if ($country_code && ! is_numeric($country_code)) $sql .= " AND c.code = '".$this->db->escape($country_code)."'";
								$sql.= " ORDER BY a.position, code, label ASC";

								//$this->resprints.= '<br>sql : '.$sql;


								dol_syslog(get_class($this)."::select_category", LOG_DEBUG);
								$resql=$this->db->query($sql);
								//var_dump($resql);

								if ($resql)
						        {

						            $num = $this->db->num_rows($resql);

						            if ($num)
						            {
						                $obj = $this->db->fetch_object($resql);
						                //var_dump($obj);

						                $this->category               = $obj;
						                $this->category->id      = $obj->id ? $obj->id : 0;
						                $this->category->category = trim($obj->label);						                
						                //$this->db->free($resql);

						                $this->resprints.= "<td>".$this->category->category."</td>";
						                
						            }
						            else
						            {
						                $this->error = $langs->trans('SetCategoryLineDoesNotExist');
						                return -1;
						            }

						        }
						        else
						        {
						            $this->error = $this->db->error()." sql=".$sql;

						            return -1;
						        }
							}
						}
					}
				}
				

				if ($action == 'editcategory') {

					$category = $SetTask->fetchcategory($context);
	                $numLines = sizeof($SetTask->categories);
	                //var_dump($numLines);
	                for($i = 0; $i < $numLines; $i++){

	                	$category_details = $SetTask->categories[$i];
	                	//var_dump(array($SetTask->categories));
	                	//var_dump($category_details);
					
						$htmlname = 'categoryid';
						$category = '0';

						$sql = "SELECT a.rowid as id, a.code as code, a.category as label, a.active, a.position";
						$sql.= " FROM ".MAIN_DB_PREFIX."LMDB_poste_category as a";
						$sql.= " WHERE";
						//$sql.= " a.country_code=c.code";
						//$sql.= " COLLATE utf8_general_ci";
						$sql.= " a.active = 1";
						//$sql.= " AND c.active = 1";
						//if ($country_code && ! is_numeric($country_code)) $sql .= " AND c.code = '".$this->db->escape($country_code)."'";
						$sql.= " ORDER BY a.position, code, label ASC";

						//$this->resprints.= '<br>sql : '.$sql;


						dol_syslog(get_class($this)."::select_category", LOG_DEBUG);
						$resql=$this->db->query($sql);
						//var_dump($resql);

						
						if ($resql)
						{
							$morecss.= "";
							$moreattrib.=' autocomplete="off"';
							$this->resprints.= '</td><td>';
							$this->resprints.= '<form name="category" method="POST" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
							$this->resprints.='<input type="hidden" name="action" value="updatecategory" />';
							$this->resprints.='<input type="hidden" name="lineid" value="'.$category_details->lineid.'"/>';
							$this->resprints.= '<select class="flat'.($morecss?' '.$morecss:'').'"'.($moreattrib?' '.$moreattrib:'').' name="'.$htmlname.'" id="'.$htmlname.'" maxlength="'.$maxlength.'" value="'.$selected.'">';
							
							$num = $this->db->num_rows($resql);
							//$this->resprints.= '<br>num : '.$num;
							
							$i = 0;
							if ($num)
							{	
								$rang=1;
								if ($category_details->fk_task != 0) {
									$category = $SetTask->fetchcategory($context);
									$numLines = sizeof($SetTask->categories);
									if ($numLines) {
										for($i0 = 0; $i0 < $numLines; $i0++){
											$category_details = $SetTask->categories[$i0];
											//var_dump(array($SetTask->tasks[$i]));
											$this->resprints.= '<option value="'.$category_details->fk_category.'">'.$langs->trans('Currently').' : '.$category_details->category.'</option>';
										}
									}
									else{
					                	$this->resprints.= "<td></td>";
					                }
								}
								$this->resprints.= '<option value="0"></option>';
								while ($i < $num)
								{	$rang++;
									$cat = $resql->fetch_object();
									var_dump($cat->id);

									// Show break
									$this->resprints.= '<option value="'.$cat->id.'" >'.$cat->label."</option>\n";

									$category=$cat->id;

									$i++;
								}
							}
					
							$this->resprints.= '</select>&nbsp;<input type="submit" class="button" value="Modifier"></td>';
							
						}
					}
				}

			}

			if ($action == 'editposte'|| $action =='editcategory') {
				$this->resprints.='
					<script>
					        	$(document).ready(function () {
					        		$("#'.$htmlname.'").select2({
					        		    dir: "ltr",
					        			width: "resolve",		/* off or resolve */
										minimumInputLength: 0,
										language: select2arrayoflanguage,
					    				containerCssClass: ":all:",					/* Line to add class of origin SELECT propagated to the new <span class="select2-selection...> tag */
										templateResult: function (data, container) {	/* Format visible output into combo list */
						 					/* Code to add class of origin OPTION propagated to the new select2 <li> tag */
											if (data.element) { $(container).addClass($(data.element).attr("class")); }
										    //console.log(data.html);
											if ($(data.element).attr("data-html") != undefined) return htmlEntityDecodeJs($(data.element).attr("data-html"));		// If property html set, we decode html entities and use this
										    return data.text;
										},
										templateSelection: function (selection) {		/* Format visible output of selected value */
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
			}
			$this->resprints.= '</form></tr>';

		}

		if ((in_array('productcard', $currentcontext))){
			
			$context = 'productcard';

			$langs->load("rating@delegation");
			dol_include_once("/delegation/lib/form.php");
			dol_include_once("/delegation/class/rating.class.php");

			// Init rating
			$rating = new Rating($db);
			$rating->init($object,'product');

			global $langs;

			$this->resprints.= '<tr><td>';
            $this->resprints.= $langs->trans('ProductRating');
            $this->resprints.= '</td><td>';
            $this->resprints.= $rating->product->display();
            $this->resprints.= '</td>';
            $this->resprints.= $htmllogobar; $htmllogobar='';
            $this->resprints.= '</tr>';

            $pageyes = "".dol_buildpath("/delegation/ajax/post.php",2)."?action=add_rating&confirm=yes&";
            
            $this->resprints.= '<script type="text/javascript">';

			$this->resprints.= '
				jQuery(document).ready(function () {
			        $(function () {
			            $("#dialog-rating").dialog({
			                autoOpen: false,
			                open: function () {
			                    $(this).parent().find("button.ui-button:eq(1)").focus();
			                },
			                resizable: false,
			                height: "250",
			                width: "330",
			                modal: true,
			                closeOnEscape: false,
			                buttons: {
			                    "'.$langs->trans("SubmitRating").'": function () {
			                        var options = $("#dialog-rating form").serialize();
			                        var pageyes = "'.$pageyes.'";
			                        var urljump = pageyes + options;
			                        //alert(urljump);
			                        if (pageyes.length > 0) {
			                            location.href = urljump;
			                        }
			                        $(this).dialog("close");
			                    },
			                    "'.$langs->trans("Cancel").'": function () {
			                        $(this).dialog("close");
			                    }
			                }
			            });

			            $(".rating-add").click(function () {
							$("#dialog-rating").dialog({title: $(this).attr("title")});
							$("#dialog-rating input#fk_element").val($(this).attr("fk_element"));
							$("#dialog-rating input#elementtype").val($(this).attr("elementtype"));
							$("#dialog-rating input#elementrated").val($(this).attr("elementrated"));
							$("#dialog-rating").dialog("open");
							return false; // prevent default action of links
			            });
			        });
			    });
			</script>';
			get_rating_form();

		}

		$this->resprints.= '<!-- END LMDB formObjectOptions -->'."\n";

		return 0;
	}




/*-------__-----__------__------__------___------__------__------__----*/
}

