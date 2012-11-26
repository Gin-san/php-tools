<?php
/*
** Configuration.php
*/

namespace Tools\Configuration;

use Tools\ArrayAccessBase,
  Symfony\Component\Yaml\Yaml;

class Configuration extends ArrayAccessBase
{
  private $_raw;

  public function __construct($config)
  {
    $this->setConfig($config);
  }

  public function setConfig($config)
  {
    if (!is_array($config)) {
      $config = Yaml::parse($config);
    }
    $this->_raw = $config;
    $this->loadConfig($config);
  }

  public function getConfig()
  {
    return $this->_values;
  }

  public function loadConfig($config = array())
  {
    if (count($config) < 1 || $this->_raw !== null) {
      $config = $this->_raw;
    }
    $this->buildConfig($config);
  }

  public function buildConfig($config, $baseKey = '')
  {
    foreach ($config as $key => $value) {
      $bkey = preg_replace('/^\./', '', $baseKey . '.' . $key);
      $this->_values[$bkey] = $value;
      if (is_array($value)) {
	$this->buildConfig($value, $bkey);
      }
    }
  }

  public function get($name)
  {
    if (!isset($this->_values[$name])) {
      return null;
    }
    return $this->_values[$name];
  }
}
