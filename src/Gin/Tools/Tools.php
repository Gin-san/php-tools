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
}
