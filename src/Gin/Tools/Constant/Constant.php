<?php
/*
** constant.php
*/

namespace Gin\Tools\Constant;

use Symfony\Component\Yaml\Yaml,
  Symfony\Component\Finder\Finder;

class Constant
{
  const LOADBYFILENAME = 1;
  const LOADBYDIRECTORY = 2;

  public static function getValuesFromArray($array, $prefix = null)
  {
    $constants = array();
    //echo $prefix . PHP_EOL;
    //print_r($array);
    foreach ($array as $key => $value) {
      if (is_array($value)) {
	$constants = array_merge($constants, self::getValuesFromArray($value, $prefix ? $prefix . '_' . $key : $key));
	continue;
      }
      $constants[$prefix ? $prefix . '_' . $key : $key] = $value;
    }
    return $constants;
  }

  public static function loadConstant($name, $mode = self::LOADBYFILENAME)
  {
    $filenames = array($name);
    if (self::LOADBYDIRECTORY === $mode
	&& file_exists($name)
	&& is_dir($name)
	&& is_readable($name)) {
      $finder = new Finder();
      $files = $finder->files()
	->name("*.yml")
	->depth(0)
	->in($name);
      $filenames = array();
      foreach ($files as $file) {
	$filenames[] = $file->getRealPath();
      }
    }

    $constants = array();
    if (count($filenames) > 0) {
      foreach ($filenames as $filename) {
	if (file_exists($filename)) {
	  $yml = Yaml::parse($filename);
	  if (is_array($yml)) {
	    $constants = self::getValuesFromArray($yml);
	  }
	}
      }

      if (count($constants) > 0) {
	foreach ($constants as $key => $value) {
	  define($key, $value);
	}
      }
    }
  }

  public static function getConstantArrays($libelleConstant,$withprefix=false)
  { 
    $consts = get_defined_constants(true); 
    $consts = $consts['user']; 
    $constantArray = array();

    $regexp ='/^' . $libelleConstant . '_(.+)$/'; 
    foreach ($consts as $name => $val) { 
      if (preg_match($regexp, $name)) { 
	$constantArray[($withprefix ? $name : preg_replace($regexp, '$1', $name))] = $val; 
      } 
    } 
    return $constantArray;
  }
}
