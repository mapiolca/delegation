<?php
/*
 * Copyright (C) 2017		 Oscss-Shop       <support@oscss-shop.fr>.
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

function get_rating_form($type="",$title="")
{
    global $langs, $conf;
    print '<div id="dialog-rating'.$type.'" class="dialog-rating" title="'.$title.'" style="display: none;">';
    print '<form>';
    for ($i = 0; $i < 6; $i++) {
        switch (true) {
            case $i == 0:
                print '<input id="rat'.$i.'" type="radio" value="'.$i.'" name="rating" checked/>';
                print '<label for="rat'.$i.'" class="star-neutral" unselectable="on" onselectstart="return false">'.$i;
                break;
            case $i < 0:
                print '<input id="rat'.$i.'" type="radio" value="'.$i.'" name="rating"/>';
                print '<label for="rat'.$i.'" class="star-negative" unselectable="on" onselectstart="return false">'.$i;
                break;
            case $i > 0:
                print '<input id="rat'.$i.'" type="radio" value="'.$i.'" name="rating"/>';
                print '<label for="rat'.$i.'" class="star-positive" unselectable="on" onselectstart="return false">'.$i;
                break;
        }
        print '</label>';
    }
    print '<textarea rows="5" name="comment" placeholder="'.$langs->trans('PlaceholderComment').'" class="comment">';
    print '</textarea>';
    print '<input type="hidden" id="fk_element" name="fk_element" value="" />';
    print '<input type="hidden" id="elementtype" name="elementtype" value="" />';
    
    print '<input type="hidden" id="rate_entity" name="rate_entity" value="'.$conf->entity.'" />'; 
    
    print '<input type="hidden" id="elementrated" name="elementrated" value="" />';
    print '</form>';
    print '</div>';
}
