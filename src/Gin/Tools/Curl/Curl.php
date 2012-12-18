<?php
/*
** Curl.php
*/

namespace Gin\Tools\Curl;

use Gin\Tools\Curl\UrlGenerator;

class Curl
{
  /*
  ** execute a http request and return the server response
  ** manager GET/POST
  ** manager url with tags like '{name}' or parameters for get or post request
  */
  public static function execute($url, $parameters, $method = "GET", $tags = array())
  {
    if (is_array($tags) && count($tags) > 1) {
      $url = UrlGenerator::generateTaggedUrl($url, $tags);
    }

    $url = strtolower($method) == "get" ? UrlGenerator::generateGetUrl($url, $parameters) : $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if (defined("CURL_USER_AGENT")) {
      curl_setopt($ch,CURLOPT_USERAGENT, constant("CURL_USER_AGENT"));
    }
    if (strtolower($url) == "post") {
      curl_setopt($ch, CURLOPT_POST, true);
      if (is_array($parameters) && count($parameters) > 1) {
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
      }
    }

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return $response;
  }

  /*
  ** Download an image from a url with the filename given
  ** return true if file uploaded or false otherwise
  */
  public static function fetchimage($url_img, $filename)
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