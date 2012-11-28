<?php
/*
** constant.php
*/

namespace Tools\Constant;

use Symfony\Component\Yaml\Yaml,
  Symfony\Component\Finder\Finder;

class Constant
{
  const LOADBYFILENAME = 1;
  const LOADBYDIRECTORY = 2;

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

    if (count($filenames) > 0) {
      $constants = array();
      foreach ($filenames as $filename) {
	if (file_exists($filename)) {
	  $yml = Yaml::parse($filename);
	  if (is_array($yml)) {
	    foreach ($yml as $key => $value) {
	      if (is_array($value)) {
		foreach ($value as $k => $v) {
		  if (is_array($v)) {
		    continue;
		  }
		  $constants[$key . '_' . $k] = $v;
		}
		continue;
	      }
	      $constants[$key] = $value;
	    }
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
}
