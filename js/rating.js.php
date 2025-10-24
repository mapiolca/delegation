<?php
/**
 *  \file       htdocs/societe/soc.php
 *  \ingroup    societe
 *  \brief      Third party card page
 */
/*
  Copyright (C) 2017		 Oscss-Shop       <support@oscss-shop.fr>.

  This program is free software; you can redistribute it and/or modifyion 2.0 (the "License");
  it under the terms of the GNU General Public License as published bypliance with the License.
  the Free Software Foundation; either version 3 of the License, or

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.
  or see http://www.gnu.org/
 */
/*
  Created on : 07 juillet 2017, 11:02:12
  Author     : Norbert Penel
 */ 
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include("../main.inc.php");
if (!$res && file_exists("../../main.inc.php")) $res = @include("../../main.inc.php");
if (!$res && file_exists("../../../main.inc.php")) $res = @include("../../../main.inc.php");
if (!$res && file_exists("../../../../main.inc.php")) $res = @include("../../../../main.inc.php");
if (!$res && file_exists("../../../dolibarr/htdocs/main.inc.php"))
        $res = @include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (!$res && file_exists("../../../../dolibarr/htdocs/main.inc.php"))
        $res = @include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (!$res && file_exists("../../../../../dolibarr/htdocs/main.inc.php"))
        $res = @include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (!$res) die("Include of main fails");

global $langs;
$langs->load("rating@rating");
ob_start();
?>
<script type="text/javascript">
    <?php ob_end_clean(); ?>
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
                    "<?= $langs->trans('SubmitRating') ?>": function () {
                        var options = $('#dialog-rating form').serialize();
                        var pageyes = "<?= dol_buildpath('/rating/ajax/post.php',2) ?>?action=add_rating&confirm=yes&";
                        var urljump = pageyes + options;
                        //alert(urljump);
                        if (pageyes.length > 0) {
                            location.href = urljump;
                        }
                        $(this).dialog("close");
                    },
                    "<?= $langs->trans('Cancel') ?>": function () {
                        $(this).dialog("close");
                    }
                }
            });

            $(".rating-add").click(function () {
                $("#dialog-rating").dialog({title: $(this).attr('title')});
                $("#dialog-rating input#fk_element").val($(this).attr('fk_element'));
                $("#dialog-rating input#elementtype").val($(this).attr('elementtype'));
                $("#dialog-rating input#elementrated").val($(this).attr('elementrated'));
                $("#dialog-rating").dialog("open");
                return false; // prevent default action of links
            });
        });
    });
