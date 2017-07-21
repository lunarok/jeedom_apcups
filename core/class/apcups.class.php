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
      $apcups->updateCommands();
    }
  }

  public function preUpdate() {
    if ($this->getConfiguration('addr') == '') {
      throw new Exception(__('L\'adresse ne peut etre vide',__FILE__));
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
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('other');
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'event');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création event');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Evènement', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('event');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('other');
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'timeleft');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création timeleft');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Temps sur batterie', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('timeleft');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'mn' );
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'linev');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création linev');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Courant Entrant', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('linev');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'V' );
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'battv');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création battv');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Voltage Pile', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('battv');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'V' );
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'model');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création model');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Modèle d\'onduleur', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('model');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('other');
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'loadpct');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création loadpct');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('% Charge', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('loadpct');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( '%' );
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'bcharge');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création bcharge');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Batterie', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('bcharge');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( '%' );
      $apcupsCmd->save();
    }

    $apcupsCmd = $this->getCmd(null, 'outpower');
    if (!is_object($apcupsCmd)) {
      log::add('apcups', 'debug', 'Création outpower');
      $apcupsCmd = new apcupsCmd();
      $apcupsCmd->setName(__('Puissance fournie', __FILE__));
      $apcupsCmd->setEqLogic_id($this->id);
      $apcupsCmd->setLogicalId('outpower');
      $apcupsCmd->setType('info');
      $apcupsCmd->setSubType('numeric');
      $apcupsCmd->setUnite( 'W' );
    }
    $apcupsCmd->setDisplay('generic_type','POWER');
    $apcupsCmd->save();

    $this->updateCommands();

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
    return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'apcups', 'apcups')));
  }

  /**
   * Fetch new informations from apcups daemon
   *
   * @return array of information
   *     each key contains the name of an item given by apcaccess
   *     each value of this array is composed of the following sub keys
   *        - raw : the raw value from apcaccess
   *        - integer : the first integer value available or null
   *        - float : the first float available or null
   *        - word : the first word available, it's the first piece
   *             of letters before the first space or the end of line
   *        - unit : the full text unit name (if available)
   */
  public function getInformations() {
    $addr = $this->getConfiguration('addr', '127.0.0.1');
    $port = $this->getConfiguration('port', 3551);
    $command = sprintf("/sbin/apcaccess status %s:%d", $addr, $port);

    # execute shell command
    $apcaccess = shell_exec(escapeshellcmd($command));
    if (is_null($apcaccess) || empty($apcaccess)) {
      throw new Exception(__("The command", __FILE__) . " $command " . __('has failed or not returned any string.', __FILE__));
    }
    log::add('apcups', 'debug', "Get information string $apcaccess from apcaccess");

    # parse informations
    $informations = [];
    foreach (explode(PHP_EOL, $apcaccess) as $row) {
      if (empty($row)) {
        continue;
      }
      $info = explode(':', $row, 2);
  	  if (count($info) != 2) {
        log::add('apcups', 'debug', "The information row $row is not parsable");
        continue;
      }
  	  $key = trim($info[0]);
  	  $value = trim($info[1]);
      preg_match('/(?P<float>(?P<integer>\d+)(\.\d+)?)/', $value, $matches);
      preg_match('/(?P<word>[a-zA-Z0-9_.-]+)/', $value, $matches_word);
      preg_match('/(?P<unit>(volts|percent|seconds|minutes))/i', $value, $matches_unit);
  	  $informations[$key] = [
        'raw' => $value,
        'integer' => isset($matches['integer']) ? $matches['integer'] : null,
        'float' => isset($matches['float']) ? $matches['float'] : null,
        'word' => isset($matches_word['word']) ? $matches_word['word'] : null,
        'unit' => isset($matches_unit['unit']) ? $matches_unit['unit'] : null
      ];
      log::add('apcups', 'debug', "Get information key $key with value $value");
	}

    return $informations;
  }

  /**
   * Update all command of this equipment with new informations
   */
  protected function updateCommands() {
	$informations = $this->getInformations();
	$puissance = $this->getConfiguration('puissance', '');

    # loop for each command and update its infos according to some specific case
    foreach ($this->getCmd('info') as $cmd) {
      $key = strtoupper($cmd->getLogicalId());
      log::add('apcups', 'debug', 'Update command ' . $cmd->getLogicalId());
      switch ($cmd->getLogicalId()) {
        case 'event':
          log::add('apcups', 'debug', ' => ignore');
          continue 2;
        case 'model':
          log::add('apcups', 'debug', ' => apply model case');
          if (isset($informations[$key])) {
            $value = $informations[$key]['raw'];
          }
          break;
        case 'outpower':
          log::add('apcups', 'debug', ' => apply outpower case');
          if (isset($puissance) && isset($informations['LOADPCT'])) {
            $value = $puissance * $informations['LOADPCT']['float'] / 100 * 0.66;
          } else {
            $value = 0;
          }
          break;
        default:
          log::add('apcups', 'debug', ' => apply default case');
          if (isset($informations[$key])) {
            if ($cmd->getSubType() == 'numeric') {
              $value = $informations[$key]['float'];
            } else {
              $value = $informations[$key]['word'];
            }
          }
          break;
      }

      if($cmd->getLogicalId() == 'bcharge') {
        log::add('apcups', 'debug', ' => update battery status');
        $this->batteryStatus($value);
      }
      log::add('apcups', 'debug', ' => update command ' . $cmd->getLogicalId() . ' with ' . $value);
      $this->checkAndUpdateCmd($cmd->getLogicalId(), $value);
    }

    $this->refreshWidget();
  }

  public static function saveEvent() {
    $hostname = init('hostname');
    $event = init('event');
    $ip = getClientIp();
    log::add('apcups', 'info', "reçu event '$event' pour '$hostname' de '$ip'");
    $elogic = self::byLogicalId($hostname, 'apcups');
    if (is_object($elogic)) {
      $elogic->checkAndUpdateCmd('event', $event);
      log::add('apcups', 'info', "mise à jour event '$event' pour '$hostname' de $ip");
    } else {
      log::add('apcups', 'warning', "echec de mise à jour event '$event' pour '$hostname' de $ip : $hostname introuvable");
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

}

?>
