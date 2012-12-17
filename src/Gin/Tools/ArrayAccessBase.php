<?php
/*
** ArrayAccessBase.php
*/

namespace Gin\Tools;

use ArrayAccess;

class ArrayAccessBase implements ArrayAccess
{
  protected $_values = array();

  public function __call($method, $arg)
  {
    $key = strtolower(preg_replace('/\.$/', '', preg_replace('/(^get)?([A-Z][^A-Z]+)/', '$2.', $method)));
    if (!empty($key) && strcasecmp($key, $method) != 0) {
      //echo $configname . PHP_EOL;
      if (isset($this->_values[$key])) {
	return $this->_values[$key];
      }
    }
  }

  public function offsetSet($id, $value)
  {
    $this->_values[$id] = $value;
  }

  public function offsetGet($id)
  {
    if (!array_key_exists($id, $this->_values)) {
      throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
    }

    return $this->_values[$id];
  }

  public function offsetExists($id)
  {
    return isset($this->_values[$id]);
  }

  public function offsetUnset($id)
  {
    unset($this->_values[$id]);
  }

}