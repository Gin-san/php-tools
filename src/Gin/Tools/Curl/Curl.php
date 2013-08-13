<?php
/*
** Curl.php
*/

namespace Gin\Tools\Curl;

use Gin\Tools\Curl\UrlGenerator;

class Curl
{
  static $info = null;

  public static function getInfo()
  {
    return self::$info;
  }

  /*
  ** execute a http request and return the server response
  ** manager GET/POST
  ** manager url with tags like '{name}' or parameters for get or post request
  */
  public static function execute($url, $parameters, $method = "GET", $tags = array(), $options = array())
  {
    if (is_array($tags) && count($tags) > 0) {
      $url = UrlGenerator::generateTaggedUrl($url, $tags);
    }

    $url = strtolower($method) == "get" ? UrlGenerator::generateGetUrl($url, $parameters) : $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if (defined("CURL_COOKIE_PATH")) {
      curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
      curl_setopt( $ch, CURLOPT_COOKIEJAR, constant("CURL_COOKIE_PATH"));
      curl_setopt( $ch, CURLOPT_COOKIEFILE, constant("CURL_COOKIE_PATH"));
    }

    if (defined("CURL_USER_AGENT")) {
      curl_setopt($ch,CURLOPT_USERAGENT, constant("CURL_USER_AGENT"));
    }

    if (strtolower($methode) == "post") {
      curl_setopt($ch, CURLOPT_POST, true);
      if (is_array($parameters) && count($parameters) > 0) {
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
      }
    }

    if (isset($options['http_header']) && is_array($options['http_header'])) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $options['http_header']);
    }


    $response = curl_exec($ch);
    self::$info = curl_getinfo($ch);
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