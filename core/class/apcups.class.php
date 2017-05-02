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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class apcups extends eqLogic {
  public static $_widgetPossibility = array('custom' => true);

  public static function health() {
    $return = array();
    if (!is_object(eqlogic::byLogicalId('127.0.0.1', 'apcups'))) {
        $return[] = array(
      'test' => __('Apcupsd local non nécessaire', __FILE__),
      'result' => __('OK', __FILE__),
      'advice' => __('Onduleur local non présent', __FILE__),
      'state' => true,
    );
	  } else {
    $pid = trim( shell_exec ('ps ax | grep "apcups" | grep -v "grep" | wc -l') );
    if ($pid != '' && $pid != '0') {
      $service = true;
    } else {
      $service = false;
    }
    $return[] = array(
      'test' => __('Apcupsd', __FILE__),
      'result' => ($service) ? __('OK', __FILE__) : __('NOK', __FILE__),
      'advice' => ($service) ? '' : __('Indique si le service apcupsd est démarré', __FILE__),
      'state' => $service,
    );
    }
    return $return;
  }

  public static function dependancy_info() {
    $return = array();
    $return['log'] = 'apcups_dep';
    $cmd = "dpkg -l | grep apcups";
    exec($cmd, $output, $return_var);
    if ($output[0] != "") {
      $return['state'] = 'ok';
    } else {
      $return['state'] = 'nok';
    }
    return $return;
  }

  public static function dependancy_install() {
    $install_path = dirname(__FILE__) . '/../../resources';
    if (!config::byKey('internalPort')) {
      $url = 'http://127.0.0.1' . config::byKey('internalComplement') . '/core/api/jeeApi.php?api=' . config::byKey('api');
    } else {
      $url = 'http://127.0.0.1:' . config::byKey('internalPort') . config::byKey('internalComplement') . '/core/api/jeeApi.php?api=' . config::byKey('api');
    }
    passthru('sudo /bin/bash ' . $install_path . '/install.sh ' . $install_path . ' ' . $url . ' > ' . log::getPathToLog('apcups_dep') . ' 2>&1 &');
  }

  public static function pull() {
    foreach (eqLogic::byType('apcups',true) as $apcups) {
      log::add('apcups', 'debug', 'pull cron');
        foreach ($apcups->getCmd('info') as $cmd) {
          $value = $cmd->execute();
          if ($value != $cmd->execCmd()) {
            $cmd->event($value);
          }
        }
        $apcups->refreshWidget();
    }
  }

  public function preUpdate() {
    if ($this->getConfiguration('addr') == '') {
      throw new Exception(__('L\adresse ne peut etre vide',__FILE__));
    }
    if ($this->getConfiguration('port') == '') {
      throw new Exception(__('Le port ne peut etre vide',__FILE__));
    }
  }

  public function preSave() {
    $this->setLogicalId($this->getConfiguration('addr'));
  }

  public function postSave() {
    $apcupsCmd = $this->getCmd(null, 'status');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création status');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Statut', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('status');
      $apcupsCmd->setConfiguration('data', 'status');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('other');
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'event');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création event');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Evènement', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('event');
      $apcupsCmd->setConfiguration('data', 'event');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('other');
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'timeleft');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création timeleft');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Temps sur batterie', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('timeleft');
      $apcupsCmd->setConfiguration('data', 'timeleft');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'mn' );
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'linev');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création linev');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Courant Entrant', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('linev');
      $apcupsCmd->setConfiguration('data', 'linev');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'V' );
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'battv');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création battv');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Voltage Pile', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('battv');
      $apcupsCmd->setConfiguration('data', 'battv');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'V' );
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'model');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création model');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Modèle d\'onduleur', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('model');
      $apcupsCmd->setConfiguration('data', 'model');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('other');
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'loadpct');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création loadpct');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('% Charge', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('loadpct');
      $apcupsCmd->setConfiguration('data', 'loadpct');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( '%' );
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'bcharge');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création bcharge');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Batterie', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('bcharge');
      $apcupsCmd->setConfiguration('data', 'bcharge');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( '%' );
      $apcupsCmd->setIsHistorized(0);
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'outpower');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création outpower');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Puissance fournie', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('outpower');
      $apcupsCmd->setConfiguration('data', 'outpower');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'W' );
      $apcupsCmd->setIsHistorized(0);
    }
    $apcupsCmd->setDisplay('generic_type','POWER');
    $apcupsCmd->save();

  }

  public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    if ($this->getDisplay('hideOn' . $version) == 1) {
      return '';
    }

    foreach ($this->getCmd('info') as $cmd) {
      $replace['#' . $cmd->getLogicalId() . '_history#'] = '';
      $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
      $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
      $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();
      if ($cmd->getIsHistorized() == 1) {
        $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
      }
    }

    $bcharge = $this->getCmd(null, 'bcharge');
    $replace['#battery_charge#'] = is_object($bcharge) ? $bcharge->getConfiguration('value') : '';
    $replace['#battery_charge_id#'] = is_object($bcharge) ? $bcharge->getId() : '';

    $linev = $this->getCmd(null, 'linev');
    $replace['#input_current#'] = is_object($linev) ? $linev->getConfiguration('value') : '';
    $replace['#input_current_id#'] = is_object($linev) ? $linev->getId() : '';

    $battv = $this->getCmd(null, 'battv');
    $replace['#battery_voltage#'] = is_object($battv) ? $battv->getConfiguration('value') : '';
    $replace['#battery_voltage_id#'] = is_object($battv) ? $battv->getId() : '';

    $status = $this->getCmd(null, 'status');
    $replace['#ups_status#'] = is_object($status) ? $status->getConfiguration('value') : '';
    $replace['#ups_status_id#'] = is_object($status) ? $status->getId() : '';

    $loadpct = $this->getCmd(null, 'loadpct');
    $replace['#ups_load#'] = is_object($loadpct) ? $loadpct->getConfiguration('value') : '';
    $replace['#ups_load_id#'] = is_object($loadpct) ? $loadpct->getId() : '';

    $outpower = $this->getCmd(null, 'outpower');
    $replace['#output_power#'] = is_object($outpower) ? $outpower->getConfiguration('value') : '';
    $replace['#output_power_id#'] = is_object($outpower) ? $outpower->getId() : '';

    $timeleft = $this->getCmd(null, 'timeleft');
    $replace['#battery_runtime#'] = is_object($timeleft) ? $timeleft->getConfiguration('value') : '';
    $replace['#battery_runtime_id#'] = is_object($timeleft) ? $timeleft->getId() : '';

    $model = $this->getCmd(null, 'model');
    $replace['#ups_model#'] = is_object($model) ? $model->getConfiguration('value') : '';


    return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'apcups', 'apcups')));
  }

  public function getInformations() {
    foreach ($this->getCmd('info') as $cmd) {
      $value = $cmd->execute();
      if ($value != $cmd->execCmd()) {
        $cmd->event($value);
      }
    }
    $this->refreshWidget();
  }

  public static function saveEvent() {
    $hostname = init('hostname');
    $event = init('event');
    $ip = getClientIp();
    echo $ip;
    log::add('apcups', 'info', 'event ' . $event . ' pour ' . $hostname . ' de ' . $ip);
    $elogic = self::byLogicalId($hostname, 'apcups');
    if (is_object($elogic)) {
      $elogic->setStatus('lastCommunication', date('Y-m-d H:i:s'));
      $elogic->save();
      $cmdlogic = apcupsCmd::byEqLogicIdAndLogicalId($elogic->getId(),'event');
      $cmdlogic->setConfiguration('value', $event);
      $cmdlogic->save();
      $cmdlogic->event($event);
      log::add('apcups', 'info', 'event ' . $event . ' pour ' . $hostname . ' de ' . $ip);
    }
  }

  public static function event() {
    $messageType = init('messagetype');
    log::add('apcups', 'info', 'event');
    switch ($messageType) {
      case 'saveEvent' : log::add('apcups', 'info', 'event'); self::saveEvent(); break;
    }
  }

}

class apcupsCmd extends cmd {
  public function execute($_options = null) {
    if($this->getConfiguration('data')=="event"){
      return;
    }
    $eqLogic = $this->getEqLogic();
    $addr = $eqLogic->getConfiguration('addr', '');
    $port = $eqLogic->getConfiguration('port', '');
    $puissance = $eqLogic->getConfiguration('puissance', '');
    $apcupsd = $addr . ':' . $port;

    log::add('apcups', 'debug', 'apcupsd : ' . $apcupsd . ' puissance ' . $puissance);

    $test = strtoupper($this->getConfiguration('data'));
    //$command = 	"apcaccess | grep TIMELEFT | awk '{print $3}'";
    if ($this->getConfiguration('data')=="model"){
      $command = "/sbin/apcaccess status " . $apcupsd . " | grep " . $test . " | awk 'BEGIN {FS=\" : \"} {print $2}'";
    } elseif ($this->getConfiguration('data')=="outpower"){
      $command = "/sbin/apcaccess status " . $apcupsd . " | grep LOADPCT | awk '{print $3}'";
    } else {
      $command = "/sbin/apcaccess status " . $apcupsd . " | grep " . $test . " | awk '{print $3}'";
    }
    $valeur = exec($command);
    if ($this->getConfiguration('data')=="outpower"){
      if (isset($puissance)) {
        $valeur = $puissance * $valeur / '100' * '0.66';
      } else {
        $valeur = '0';
      }
    }
    log::add('apcups', 'debug', $command . ' : ' . $valeur);
    if($this->getConfiguration('data')=="bcharge"){
      $eqLogic->batteryStatus($valeur);
    }
    $this->setConfiguration('value', $valeur);
    $this->save();
    return $valeur;
  }
}

?>
