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
}
