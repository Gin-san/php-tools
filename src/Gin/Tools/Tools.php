<?php
/*
** Tools.php
*/

namespace Gin\Tools;

class Tools
{
  public static function memory_usage()
  {
    $mem_usage = memory_get_usage(true);
    if ($mem_usage < 1024) {
      return $mem_usage . " bytes";
    } elseif ($mem_usage < 1048576) {
      return round($mem_usage / 1024, 2) . " kilobytes";
    }
    return round($mem_usage / 1048576, 2) . " megabytes";
  }

  public static function clearUTF($text)
  {
    setlocale(LC_ALL, 'en_US.UTF8');
    return str_replace(' ', '_', trim(preg_replace('#[^\p{L}\p{N}]+#u', ' ', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $text)))));
  }

  // source: http://stackoverflow.com/questions/2353473/can-php-tell-if-the-server-os-it-64-bit#answer-5432564
  public static function is_64bit() {
    $int = "9223372036854775807";
    $int = intval($int);
    if ($int == 9223372036854775807) {
      return true;
    } elseif ($int == 2147483647) {
      return false;
    }
    return "error";
  }
}
