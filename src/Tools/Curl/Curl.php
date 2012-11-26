<?php
/*
** Curl.php
*/

namespace Tools\Curl;

use Tools\Curl\UrlGenerator;

class Curl
{

  public static function execute(string $url, array $parameters, string $method = "GET", array $tags = array())
  {
    if (is_array($tags) && count($tags) > 1) {
      $url = UrlGenerator::generateTaggedUrl($url, $tags);
    }

    $url = strtolower($method) == "get" ? UrlGenerator::generateGetUrl($url, $parameters) : $url;
    //echo $url . "\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    if (strtolower($url) == "post") {
      curl_setopt($ch, CURLOPT_POST, true);
      if (is_array($parameters) && count($parameters) > 1) {
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
      }
    }

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    //print_r($info);
    curl_close($ch);
    return $response;
  }

  public static function fetchimage(string $url_img, string $filename)
  {
    if (file_exists($filename)) {
      return true;
    }
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url_img);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
    $fileContents = curl_exec($ch);
    curl_close($ch);
    $newImg = @imagecreatefromstring($fileContents);
    if ($newImg != false) {
      imagejpeg($newImg, $filename, 80);
      imagedestroy($newImg);
      return true;
    }
    return false;
  }
}