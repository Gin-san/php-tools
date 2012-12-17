<?php

namespace Gin\Tools\Curl;

class UrlGenerator
{
  public static function generateGetUrl($url, $parameters)
  {
    if (count($parameters) > 1){
      $url .= preg_match('/\?/', $url) ? '&' : '?';
      foreach ($parameters as $key => $value) {
	$url .= $key . '=' . $value . '&';
      }
    }
    return $url;
  }

  public static function generateTaggedUrl($url, $tags)
  {
    $m = array();
    $gurl = '';
    if (preg_match('/\{[a-zA-Z0-9\.-_]+\}/', $url, $m)) {
      $t = explode($m[0], $url);
      $fixe = $t[0];
      $fixe = preg_replace('/(^-)*([a-zA-Z0-9\.-_:\/])(-$)*/', '$2', $fixe);
      if (!empty($fixe)) {
	$gurl .= $fixe;
      }
      $tag = $m[0];
      $tag = preg_replace('/(^\{)*([a-zA-Z0-9\.-_])(\}$)*/', '$2', $tag);
      $gurl .= isset($tags[$tag]) ? $tags[$tag] : "";
      if (isset($t[1]) && strlen($t[1]) > 1) {
	$gurl .= self::generateTaggedUrl($t[1], $tags);
      }
    }
    return $gurl;
  }
}