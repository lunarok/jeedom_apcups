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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';


function apcups_install() {
    $cron = cron::byClassAndFunction('apcups', 'pull');
    if (!is_object($cron)) {
        $cron = new cron();
        $cron->setClass('apcups');
        $cron->setFunction('pull');
        $cron->setEnable(1);
        $cron->setDeamon(0);
        $cron->setSchedule('*/2 * * * *');
        $cron->save();
    }
    if (!is_object(eqlogic::byLogicalId('127.0.0.1', 'apcups'))) {
        $local = new apcups();
        $local->setEqType_name('apcups');
        $local->setConfiguration('addr', '127.0.0.1');
        $local->setConfiguration('port', '3551');
        $local->setName('Onduleur USB');
        $local->setIsEnable(true);
        $local->save();
	}

}

function apcups_update() {
    $cron = cron::byClassAndFunction('apcups', 'pull');
    if (!is_object($cron)) {
        $cron = new cron();
        $cron->setClass('apcups');
        $cron->setFunction('pull');
        $cron->setEnable(1);
        $cron->setDeamon(0);
        $cron->setSchedule('*/2 * * * *');
        $cron->save();
    }
}

function apcups_remove() {
    $cron = cron::byClassAndFunction('apcups', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
?>
