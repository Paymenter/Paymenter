<?php
namespace App\Extensions\Servers\DirectAdmin;

/**
 * Socket communication class.
 *
 * Originally designed for use with DirectAdmin's API, this class will fill any HTTP socket need.
 *
 * Very, very basic usage:
 *   $Socket = new HTTPSocket;
 *   echo $Socket->get('http://user:pass@somesite.com/somedir/some.file?query=string&this=that');
 *
 * @author Phi1 'l0rdphi1' Stier <l0rdphi1@liquenox.net>
 *
 * @version 3.0.4
 *
 * 3.0.4
 * store first proxy headers for return, in event of redirect
 *
 * 3.0.3
 * curl Cookie for SESSION_ID+SESSION_KEY changed to setopt with
 *
 * 3.0.2
 * added longer curl timeouts
 *
 * 3.0.1
 * support for tcp:// conversion to http://
 *
 * 3.0.0
 * swapped to use curl to address ssl certificate issues with php 5.6
 *
 * 2.7.2
 * added x-use-https header check
 * added max number of location redirects
 * added custom settable message if x-use-https is found, so users can be told where to set their scripts
 * if a redirect host is https, add ssl:// to remote_host
 *
 * 2.7.1
 * added isset to headers['location'], line 306
 */
class DAHTTPSocket
{
    public $version = '3.0.4';

    /* all vars are private except $error, $query_cache, and $doFollowLocationHeader */

    public $method = 'GET';

    public $remote_host;
    public $remote_port;
    public $remote_uname;
    public $remote_passwd;

    public $result;
    public $result_header;
    public $result_body;
    public $result_status_code;

    public $lastTransferSpeed;

    public $bind_host;

    public $error = [];
    public $warn = [];
    public $query_cache = [];

    public $doFollowLocationHeader = true;
    public $redirectURL;
    public $max_redirects = 5;
    public $ssl_setting_message = 'DirectAdmin appears to be using SSL. Change your script to connect to ssl://';

    public $extra_headers = [];

    public $proxy = false;
    public $proxy_headers = [];

    /**
     * Create server "connection".
     */
    public function connect($host, $port = '')
    {
        if (!is_numeric($port)) {
            $port = 80;
        }

        $this->remote_host = $host;
        $this->remote_port = $port;
    }

    public function bind($ip = '')
    {
        if ($ip == '') {
            $ip = $_SERVER['SERVER_ADDR'];
        }

        $this->bind_host = $ip;
    }

    /**
     * Change the method being used to communicate.
     *
     * @param string|null request method. supports GET, POST, and HEAD. default is GET
     */
    public function set_method($method = 'GET')
    {
        $this->method = strtoupper($method);
    }

    /**
     * Specify a username and password.
     *
     * @param string|null username. defualt is null
     * @param string|null password. defualt is null
     */
    public function set_login($uname = '', $passwd = '')
    {
        if (strlen($uname) > 0) {
            $this->remote_uname = $uname;
        }

        if (strlen($passwd) > 0) {
            $this->remote_passwd = $passwd;
        }
    }

    /**
     * For pass through, this function writes the data in chunks.
     */
    private function stream_chunk($ch, $data)
    {
        echo $data;

        return strlen($data);
    }

    private function stream_header($ch, $data)
    {
        if (!preg_match('/^HTTP/i', $data)) {
            header($data);
        }

        return strlen($data);
    }

    /**
     * Query the server.
     *
     * @param string containing properly formatted server API. See DA API docs and examples. Http:// URLs O.K. too.
     * @param string|array query to pass to url
     * @param int if connection KB/s drops below value here, will drop connection
     */
    public function query($request, $content = '', $doSpeedCheck = 0)
    {
        $this->error = $this->warn = [];
        $this->result_status_code = null;

        $is_ssl = false;

        // is our request a http:// ... ?
        if (preg_match('!^http://!i', $request) || preg_match('!^https://!i', $request)) {
            $location = parse_url($request);
            if (preg_match('!^https://!i', $request)) {
                $this->connect('https://' . $location['host'], $location['port']);
            } else {
                $this->connect('http://' . $location['host'], $location['port']);
            }

            $this->set_login($location['user'], $location['pass']);

            $request = $location['path'];
            $content = $location['query'];

            if (strlen($request) < 1) {
                $request = '/';
            }
        }

        if (preg_match('!^ssl://!i', $this->remote_host)) {
            $this->remote_host = 'https://' . substr($this->remote_host, 6);
        }

        if (preg_match('!^tcp://!i', $this->remote_host)) {
            $this->remote_host = 'http://' . substr($this->remote_host, 6);
        }

        if (preg_match('!^https://!i', $this->remote_host)) {
            $is_ssl = true;
        }

        $array_headers = [
            'Host' => ($this->remote_port == 80 ? $this->remote_host : "$this->remote_host:$this->remote_port"),
            'Accept' => '*/*',
            'Connection' => 'Close'];

        foreach ($this->extra_headers as $key => $value) {
            $array_headers[$key] = $value;
        }

        $this->result = $this->result_header = $this->result_body = '';

        // was content sent as an array? if so, turn it into a string
        if (is_array($content)) {
            $pairs = [];

            foreach ($content as $key => $value) {
                $pairs[] = "$key=" . urlencode($value);
            }

            $content = implode('&', $pairs);
            unset($pairs);
        }

        $OK = true;

        if ($this->method == 'GET' && isset($content) && $content != '') {
            $request .= '?' . $content;
        }

        $ch = curl_init($this->remote_host . ':' . $this->remote_port . $request);

        if ($is_ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 1
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 2
            // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        }

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "HTTPSocket/$this->version");
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLINFO_HEADER_OUT, false);
            curl_setopt($ch, CURLOPT_BUFFERSIZE, 8192); // 8192
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, [$this, 'stream_chunk']);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'stream_header']);
        }

        curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 512);
        curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 120);

        // instance connection
        if ($this->bind_host) {
            curl_setopt($ch, CURLOPT_INTERFACE, $this->bind_host);
        }

        // if we have a username and password, add the header
        if (isset($this->remote_uname) && isset($this->remote_passwd)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->remote_uname . ':' . $this->remote_passwd);
        }

        // for DA skins: if $this->remote_passwd is NULL, try to use the login key system
        if (isset($this->remote_uname) && $this->remote_passwd == null) {
            curl_setopt($ch, CURLOPT_COOKIE, "session={$_SERVER['SESSION_ID']}; key={$_SERVER['SESSION_KEY']}");
        }

        // if method is POST, add content length & type headers
        if ($this->method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

            // $array_headers['Content-type'] = 'application/x-www-form-urlencoded';
            $array_headers['Content-length'] = strlen($content);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $array_headers);

        if (!($this->result = curl_exec($ch))) {
            $this->error[] .= curl_error($ch);
            $OK = false;
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->result_header = substr($this->result, 0, $header_size);
        $this->result_body = substr($this->result, $header_size);
        $this->result_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->lastTransferSpeed = curl_getinfo($ch, CURLINFO_SPEED_DOWNLOAD) / 1024;

        curl_close($ch);

        $this->query_cache[] = $this->remote_host . ':' . $this->remote_port . $request;

        $headers = $this->fetch_header();

        // did we get the full file?
        if (!empty($headers['content-length']) && $headers['content-length'] != strlen($this->result_body)) {
            $this->result_status_code = 206;
        }

        // now, if we're being passed a location header, should we follow it?
        if ($this->doFollowLocationHeader) {
            // dont bother if we didn't even setup the script correctly
            if (isset($headers['x-use-https']) && $headers['x-use-https'] == 'yes') {
                exit($this->ssl_setting_message);
            }

            if (isset($headers['location'])) {
                if ($this->max_redirects <= 0) {
                    exit('Too many redirects on: ' . $headers['location']);
                }

                --$this->max_redirects;
                $this->redirectURL = $headers['location'];
                $this->query($headers['location']);
            }
        }
    }

    public function getTransferSpeed()
    {
        return $this->lastTransferSpeed;
    }

    /**
     * The quick way to get a URL's content :).
     *
     * @param string URL
     * @param bool return as array? (like PHP's file() command)
     *
     * @return string result body
     */
    public function get($location, $asArray = false)
    {
        $this->query($location);

        if ($this->get_status_code() == 200) {
            if ($asArray) {
                return preg_split("/\n/", $this->fetch_body());
            }

            return $this->fetch_body();
        }

        return false;
    }

    /**
     * Returns the last status code.
     * 200 = OK;
     * 403 = FORBIDDEN;
     * etc.
     *
     * @return int status code
     */
    public function get_status_code()
    {
        return $this->result_status_code;
    }

    /**
     * Adds a header, sent with the next query.
     *
     * @param string header name
     * @param string header value
     */
    public function add_header($key, $value)
    {
        $this->extra_headers[$key] = $value;
    }

    /**
     * Clears any extra headers.
     */
    public function clear_headers()
    {
        $this->extra_headers = [];
    }

    /**
     * Return the result of a query.
     *
     * @return string result
     */
    public function fetch_result()
    {
        return $this->result;
    }

    /**
     * Return the header of result (stuff before body).
     *
     * @param string (optional) header to return
     *
     * @return array result header
     */
    public function fetch_header($header = '')
    {
        if ($this->proxy) {
            return $this->proxy_headers;
        }

        $array_headers = preg_split("/\r\n/", $this->result_header);

        $array_return = [0 => $array_headers[0]];
        unset($array_headers[0]);

        foreach ($array_headers as $pair) {
            if ($pair == '' || $pair == "\r\n") {
                continue;
            }
            list($key, $value) = preg_split('/: /', $pair, 2);
            $array_return[strtolower($key)] = $value;
        }

        if ($header != '') {
            return $array_return[strtolower($header)];
        }

        return $array_return;
    }

    /**
     * Return the body of result (stuff after header).
     *
     * @return string result body
     */
    public function fetch_body()
    {
        return $this->result_body;
    }

    /**
     * Return parsed body in array format.
     *
     * @return array result parsed
     */
    public function fetch_parsed_body()
    {
        parse_str($this->result_body, $x);

        return $x;
    }

    /**
     * Set a specifc message on how to change the SSL setting, in the event that it's not set correctly.
     */
    public function set_ssl_setting_message($str)
    {
        $this->ssl_setting_message = $str;
    }
}
