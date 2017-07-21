<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
$eqLogic = apcups::byId(init('id'));
?>

<span class='pull-right'>
    <a class="btn btn-default pull-right" id="bt_refreshValues"><i class="fa fa-refresh"></i> {{Rafraichir}}</a>
</span>
<div id='div_valuesAlert' style="display: none;"></div>
<table class="table table-condensed" id="table_values">
    <thead>
        <tr>
            <th>{{Nom de la valeur}}</th>
            <th>{{Valeur brute}}</th>
            <th>{{Valeur entière}}</th>
            <th>{{Valeur décimale}}</th>
            <th>{{Premier mot}}</th>
            <th>{{Unité}}</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<?php include_file('core', 'apcups', 'class.js', 'apcups');?>
<?php include_file('desktop', 'values', 'js', 'apcups');?>
