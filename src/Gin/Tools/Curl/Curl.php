<?php
namespace Gin\Tools\Curl;

use Gin\Tools\Curl\UrlGenerator;
use InvalidArgumentException;
use http\Url;

class Curl
{

    const CURL_REQUEST_GET  = "GET";
    const CURL_REQUEST_POST = "POST";

    protected $ch;
    protected $params;
    protected $user_agent;
    protected $cookie_path;
    protected $http_header;

    /**
     * Initialize curl request
     *
     * @param string $user_agent  Request user-agent
     * @param string $cookie_path Session path
     */
    public function __construct(array $default_parameters = array(), $user_agent = null, $cookie_path = null)
    {
        $this->ch          = curl_init();
        $this->params      = $default_parameters;
        $this->setUserAgent($user_agent)
            ->setCookiePath($cookie_path);
    }

    /**
     * Close curl request
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * Execute Curl request
     *
     * @param  string $url    Url to execute the request
     * @param  string $method Method of the request
     * @param  array  $params Parameters
     *
     * @return string Request response
     */
    public function execute($url, $method = "GET", array $params = array())
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('$url parameters must be a valid URL');
        }

        $ch = $this->ch;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($this->useCookie) {
            curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
            curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->getCookiePath());
            curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->getCookiePath());
        }

        $params          = array_merge($this->params, $params);
        $method          = strtoupper($method);
        $url_data        = parse_url($url);
        $extend_url_data = [];

        if ($method == self::CURL_REQUEST_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (count($params) > 0) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
        } elseif (count($params) > 0 && $method == self::CURL_REQUEST_GET) {
            $extend_url_data = [
                'query' => http_build_query($params)
            ];
        }
        if (version_compare(phpversion('http'), '2.0', '>=')) {
            $url = (new Url($url_data, $extend_url_data, Url::JOIN_QUERY))->toString();
        } else {
            $url = http_build_url($url_data, $extend_url_data, constant("HTTP_URL_JOIN_QUERY"));
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        if (count($this->http_header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHttpHeader());
        }

        $response = curl_exec($ch);

        return $response;
    }

    /**
     * Add a value to global parameters
     *
     * @param string $keyname   Keyname parameter
     * @param string $value     Value parameter
     */
    public function addParam($keyname, $value)
    {
        if (!is_scalar($value)) {
            $value = json_encode($value);
        }
        $this->params[$keyname] = $value;

        return $this;
    }

    /**
     * Set request user-agent
     *
     * @param string $user_agent request user-agent
     *
     * @return Curl self
     */
    public function setUserAgent($user_agent)
    {
        if (!empty($user_agent)) {
            $this->user_agent = $user_agent;
        }

        return $this;
    }

    /**
     * Get user-agent used in the request
     *
     * @return string user-agent value
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * Set request cookie/session path
     *
     * @param string $cookie_path path to save cookie/session
     *
     * @return Curl self
     */
    public function setCookiePath($cookie_path)
    {
        $this->useCookie = false;
        if (!empty($cookie_path)) {
            $this->useCookie = true;
            $this->cookie_path = $cookie_path;
        }

        return $this;
    }

    /**
     * Get cookie path used in the request
     *
     * @return string cookie path value
     */
    public function getCookiePath()
    {
        return $this->cookie_path;
    }

    /**
     * Set extra HTTP header
     *
     * @param array $http_header extra HTTP Header
     *
     * @return Curl self
     */
    public function setHttpHeader(array $http_header)
    {
        $this->http_header = $http_header;

        return $http_header;
    }

    /**
     * Get extra HTTP Header
     *
     * @return array extra HTTP header values
     */
    public function getHttpHeader()
    {
        return $this->http_header;
    }

}