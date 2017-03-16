<?php

namespace Curl;

class CaseInsensitiveArray implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var mixed[] Data storage with lower-case keys
     *
     * @see offsetSet()
     * @see offsetExists()
     * @see offsetUnset()
     * @see offsetGet()
     * @see count()
     * @see current()
     * @see next()
     * @see key()
     */
    private $data = array();
    /**
     * @var string[] Case-Sensitive keys
     *
     * @see offsetSet()
     * @see offsetUnset()
     * @see key()
     */
    private $keys = array();
    /**
     * Construct.
     *
     * Allow creating either an empty Array, or convert an existing Array to a
     * Case-Insensitive Array.  (Caution: Data may be lost when converting Case-
     * Sensitive Arrays to Case-Insensitive Arrays)
     *
     * @param mixed[] $initial (optional) Existing Array to convert
     *
     * @return CaseInsensitiveArray
     */
    public function __construct(array $initial = null)
    {
        if ($initial !== null) {
            foreach ($initial as $key => $value) {
                $this->offsetSet($key, $value);
            }
        }
    }
    /**
     * Offset Set.
     *
     * Set data at a specified Offset.  Converts the offset to lower-case, and
     * stores the Case-Sensitive Offset and the Data at the lower-case indexes
     * in $this->keys and @this->data.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offseteset.php
     *
     * @param string $offset The offset to store the data at (case-insensitive)
     * @param mixed  $value  The data to store at the specified offset
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $offsetlower = strtolower($offset);
            $this->data[$offsetlower] = $value;
            $this->keys[$offsetlower] = $offset;
        }
    }
    /**
     * Offset Exists.
     *
     * Checks if the Offset exists in data storage.  The index is looked up with
     * the lower-case version of the provided offset.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $offset Offset to check
     *
     * @return bool If the offset exists
     */
    public function offsetExists($offset)
    {
        return (bool) array_key_exists(strtolower($offset), $this->data);
    }
    /**
     * Offset Unset.
     *
     * Unsets the specified offset. Converts the provided offset to lowercase,
     * and unsets the Case-Sensitive Key, as well as the stored data.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        $offsetlower = strtolower($offset);
        unset($this->data[$offsetlower]);
        unset($this->keys[$offsetlower]);
    }
    /**
     * Offset Get.
     *
     * Return the stored data at the provided offset. The offset is converted to
     * lowercase and the lookup is done on the Data store directly.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $offset Offset to lookup
     *
     * @return mixed The data stored at the offset
     */
    public function offsetGet($offset)
    {
        $offsetlower = strtolower($offset);

        return isset($this->data[$offsetlower]) ? $this->data[$offsetlower] : null;
    }
    /**
     * Count.
     *
     * @see https://secure.php.net/manual/en/countable.count.php
     *
     * @param void
     *
     * @return int The number of elements stored in the Array
     */
    public function count()
    {
        return (int) count($this->data);
    }
    /**
     * Current.
     *
     * @see https://secure.php.net/manual/en/iterator.current.php
     *
     * @param void
     *
     * @return mixed Data at the current position
     */
    public function current()
    {
        return current($this->data);
    }
    /**
     * Next.
     *
     * @see https://secure.php.net/manual/en/iterator.next.php
     *
     * @param void
     */
    public function next()
    {
        next($this->data);
    }
    /**
     * Key.
     *
     * @see https://secure.php.net/manual/en/iterator.key.php
     *
     * @param void
     *
     * @return mixed Case-Sensitive key at current position
     */
    public function key()
    {
        $key = key($this->data);

        return isset($this->keys[$key]) ? $this->keys[$key] : $key;
    }
    /**
     * Valid.
     *
     * @see https://secure.php.net/manual/en/iterator.valid.php
     *
     * @return bool If the current position is valid
     */
    public function valid()
    {
        return (bool) !(key($this->data) === null);
    }
    /**
     * Rewind.
     *
     * @see https://secure.php.net/manual/en/iterator.rewind.php
     *
     * @param void
     */
    public function rewind()
    {
        reset($this->data);
    }
}

class Curl
{
    const VERSION = '7.0.1';
    const DEFAULT_TIMEOUT = 30;
    public static $RFC2616 = array(
        // RFC2616: "any CHAR except CTLs or separators".
        // CHAR           = <any US-ASCII character (octets 0 - 127)>
        // CTL            = <any US-ASCII control character
        //                  (octets 0 - 31) and DEL (127)>
        // separators     = "(" | ")" | "<" | ">" | "@"
        //                | "," | ";" | ":" | "\" | <">
        //                | "/" | "[" | "]" | "?" | "="
        //                | "{" | "}" | SP | HT
        // SP             = <US-ASCII SP, space (32)>
        // HT             = <US-ASCII HT, horizontal-tab (9)>
        // <">            = <US-ASCII double-quote mark (34)>
        '!', '#', '$', '%', '&', "'", '*', '+', '-', '.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B',
        'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q',
        'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '|', '~',
    );
    public static $RFC6265 = array(
        // RFC6265: "US-ASCII characters excluding CTLs, whitespace DQUOTE, comma, semicolon, and backslash".
        // %x21
        '!',
        // %x23-2B
        '#', '$', '%', '&', "'", '(', ')', '*', '+',
        // %x2D-3A
        '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':',
        // %x3C-5B
        '<', '=', '>', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
        'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[',
        // %x5D-7E
        ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~',
    );
    public $curl;
    public $id = null;
    public $error = false;
    public $errorCode = 0;
    public $errorMessage = null;
    public $curlError = false;
    public $curlErrorCode = 0;
    public $curlErrorMessage = null;
    public $httpError = false;
    public $httpStatusCode = 0;
    public $httpErrorMessage = null;
    public $baseUrl = null;
    public $url = null;
    public $requestHeaders = null;
    public $responseHeaders = null;
    public $rawResponseHeaders = '';
    public $responseCookies = array();
    public $response = null;
    public $rawResponse = null;
    public $beforeSendFunction = null;
    public $downloadCompleteFunction = null;
    public $successFunction = null;
    public $errorFunction = null;
    public $completeFunction = null;
    public $fileHandle = null;
    private $cookies = array();
    private $headers = array();
    private $options = array();
    private $jsonDecoder = null;
    private $jsonPattern = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';
    private $xmlDecoder = null;
    private $xmlPattern = '~^(?:text/|application/(?:atom\+|rss\+)?)xml~i';
    private $defaultDecoder = null;
    private static $deferredProperties = array(
        'effectiveUrl',
        'totalTime',
    );
    /**
     * Construct.
     *
     * @param  $base_url
     *
     * @throws \ErrorException
     */
    public function __construct($base_url = null)
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }
        $this->curl = curl_init();
        $this->id = uniqid('', true);
        $this->setDefaultUserAgent();
        $this->setDefaultJsonDecoder();
        $this->setDefaultXmlDecoder();
        $this->setDefaultTimeout();
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADERFUNCTION, array($this, 'headerCallback'));
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->headers = new CaseInsensitiveArray();
        $this->setUrl($base_url);
        $this->rfc2616 = array_fill_keys(self::$RFC2616, true);
        $this->rfc6265 = array_fill_keys(self::$RFC6265, true);
    }
    /**
     * Before Send.
     *
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendFunction = $callback;
    }
    /**
     * Build Post Data.
     *
     * @param  $data
     *
     * @return array|string
     */
    public function buildPostData($data)
    {
        $binary_data = false;
        if (is_array($data)) {
            // Return JSON-encoded string when the request's content-type is JSON.
            if (isset($this->headers['Content-Type']) &&
                preg_match($this->jsonPattern, $this->headers['Content-Type'])) {
                $json_str = json_encode($data);
                if (!($json_str === false)) {
                    $data = $json_str;
                }
            } else {
                // Manually build a single-dimensional array from a multi-dimensional array as using curl_setopt($ch,
                // CURLOPT_POSTFIELDS, $data) doesn't correctly handle multi-dimensional arrays when files are
                // referenced.
                if (self::is_array_multidim($data)) {
                    $data = self::array_flatten_multidim($data);
                }
                // Modify array values to ensure any referenced files are properly handled depending on the support of
                // the @filename API or CURLFile usage. This also fixes the warning "curl_setopt(): The usage of the
                // @filename API for file uploading is deprecated. Please use the CURLFile class instead". Ignore
                // non-file values prefixed with the @ character.
                foreach ($data as $key => $value) {
                    if (is_string($value) && strpos($value, '@') === 0 && is_file(substr($value, 1))) {
                        $binary_data = true;
                        if (class_exists('CURLFile')) {
                            $data[$key] = new \CURLFile(substr($value, 1));
                        }
                    } elseif ($value instanceof \CURLFile) {
                        $binary_data = true;
                    }
                }
            }
        }
        if (!$binary_data && (is_array($data) || is_object($data))) {
            $data = http_build_query($data, '', '&');
        }

        return $data;
    }
    /**
     * Call.
     */
    public function call()
    {
        $args = func_get_args();
        $function = array_shift($args);
        if (is_callable($function)) {
            array_unshift($args, $this);
            call_user_func_array($function, $args);
        }
    }
    /**
     * Close.
     */
    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
        $this->options = null;
        $this->jsonDecoder = null;
        $this->xmlDecoder = null;
        $this->defaultDecoder = null;
    }
    /**
     * Complete.
     *
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->completeFunction = $callback;
    }
    /**
     * Progress.
     *
     * @param  $callback
     */
    public function progress($callback)
    {
        $this->setOpt(CURLOPT_PROGRESSFUNCTION, $callback);
        $this->setOpt(CURLOPT_NOPROGRESS, false);
    }
    /**
     * Delete.
     *
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return string
     */
    public function delete($url, $query_parameters = array(), $data = array())
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $query_parameters);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));

        return $this->exec();
    }
    /**
     * Download Complete.
     *
     * @param  $fh
     */
    private function downloadComplete($fh)
    {
        if (!$this->error && $this->downloadCompleteFunction) {
            rewind($fh);
            $this->call($this->downloadCompleteFunction, $fh);
            $this->downloadCompleteFunction = null;
        }
        if (is_resource($fh)) {
            fclose($fh);
        }
        // Fix "PHP Notice: Use of undefined constant STDOUT" when reading the
        // PHP script from stdin. Using null causes "Warning: curl_setopt():
        // supplied argument is not a valid File-Handle resource".
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }
        // Reset CURLOPT_FILE with STDOUT to avoid: "curl_exec(): CURLOPT_FILE
        // resource has gone away, resetting to default".
        $this->setOpt(CURLOPT_FILE, STDOUT);
        // Reset CURLOPT_RETURNTRANSFER to tell cURL to return subsequent
        // responses as the return value of curl_exec(). Without this,
        // curl_exec() will revert to returning boolean values.
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }
    /**
     * Download.
     *
     * @param  $url
     * @param  $mixed_filename
     *
     * @return bool
     */
    public function download($url, $mixed_filename)
    {
        if (is_callable($mixed_filename)) {
            $this->downloadCompleteFunction = $mixed_filename;
            $fh = tmpfile();
        } else {
            $filename = $mixed_filename;
            $fh = fopen($filename, 'wb');
        }
        $this->setOpt(CURLOPT_FILE, $fh);
        $this->get($url);
        $this->downloadComplete($fh);

        return !$this->error;
    }
    /**
     * Error.
     *
     * @param  $callback
     */
    public function error($callback)
    {
        $this->errorFunction = $callback;
    }
    /**
     * Exec.
     *
     * @param  $ch
     *
     * @return mixed Returns the value provided by parseResponse
     */
    public function exec($ch = null)
    {
        if ($ch === null) {
            $this->responseCookies = array();
            $this->call($this->beforeSendFunction);
            $this->rawResponse = curl_exec($this->curl);
            $this->curlErrorCode = curl_errno($this->curl);
            $this->curlErrorMessage = curl_error($this->curl);
        } else {
            $this->rawResponse = curl_multi_getcontent($ch);
            $this->curlErrorMessage = curl_error($ch);
        }
        $this->curlError = !($this->curlErrorCode === 0);
        // Include additional error code information in error message when possible.
        if ($this->curlError && function_exists('curl_strerror')) {
            $this->curlErrorMessage =
                curl_strerror($this->curlErrorCode).(
                    empty($this->curlErrorMessage) ? '' : ': '.$this->curlErrorMessage
                );
        }
        $this->httpStatusCode = $this->getInfo(CURLINFO_HTTP_CODE);
        $this->httpError = in_array(floor($this->httpStatusCode / 100), array(4, 5));
        $this->error = $this->curlError || $this->httpError;
        $this->errorCode = $this->error ? ($this->curlError ? $this->curlErrorCode : $this->httpStatusCode) : 0;
        // NOTE: CURLINFO_HEADER_OUT set to true is required for requestHeaders
        // to not be empty (e.g. $curl->setOpt(CURLINFO_HEADER_OUT, true);).
        if ($this->getOpt(CURLINFO_HEADER_OUT) === true) {
            $this->requestHeaders = $this->parseRequestHeaders($this->getInfo(CURLINFO_HEADER_OUT));
        }
        $this->responseHeaders = $this->parseResponseHeaders($this->rawResponseHeaders);
        $this->response = $this->parseResponse($this->responseHeaders, $this->rawResponse);
        $this->httpErrorMessage = '';
        if ($this->error) {
            if (isset($this->responseHeaders['Status-Line'])) {
                $this->httpErrorMessage = $this->responseHeaders['Status-Line'];
            }
        }
        $this->errorMessage = $this->curlError ? $this->curlErrorMessage : $this->httpErrorMessage;
        if (!$this->error) {
            $this->call($this->successFunction);
        } else {
            $this->call($this->errorFunction);
        }
        $this->call($this->completeFunction);
        // Close open file handles and reset the curl instance.
        if (!($this->fileHandle === null)) {
            $this->downloadComplete($this->fileHandle);
        }

        return $this->response;
    }
    /**
     * Get.
     *
     * @param  $url
     * @param  $data
     *
     * @return mixed Returns the value provided by exec
     */
    public function get($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $this->setOpt(CURLOPT_HTTPGET, true);

        return $this->exec();
    }
    /**
     * Get Info.
     *
     * @param  $opt
     *
     * @return mixed
     */
    public function getInfo($opt)
    {
        return curl_getinfo($this->curl, $opt);
    }
    /**
     * Get Opt.
     *
     * @param  $option
     *
     * @return mixed
     */
    public function getOpt($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }
    /**
     * Head.
     *
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function head($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $this->setOpt(CURLOPT_NOBODY, true);

        return $this->exec();
    }
    /**
     * Header Callback.
     *
     * @param  $ch
     * @param  $header
     *
     * @return int
     */
    public function headerCallback($ch, $header)
    {
        if (preg_match('/^Set-Cookie:\s*([^=]+)=([^;]+)/mi', $header, $cookie) === 1) {
            $this->responseCookies[$cookie[1]] = trim($cookie[2], " \n\r\t\0\x0B");
        }
        $this->rawResponseHeaders .= $header;

        return strlen($header);
    }
    /**
     * Options.
     *
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function options($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url, $data);
        $this->removeHeader('Content-Length');
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');

        return $this->exec();
    }
    /**
     * Patch.
     *
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function patch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        if (is_array($data) && empty($data)) {
            $this->removeHeader('Content-Length');
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));

        return $this->exec();
    }
    /**
     * Post.
     *
     * @param  $url
     * @param  $data
     * @param  $follow_303_with_post
     *     If true, will cause 303 redirections to be followed using a POST request (default: false).
     *     Notes:
     *       - Redirections are only followed if the CURLOPT_FOLLOWLOCATION option is set to true.
     *       - According to the HTTP specs (see [1]), a 303 redirection should be followed using
     *         the GET method. 301 and 302 must not.
     *       - In order to force a 303 redirection to be performed using the same method, the
     *         underlying cURL object must be set in a special state (the CURLOPT_CURSTOMREQUEST
     *         option must be set to the method to use after the redirection). Due to a limitation
     *         of the cURL extension of PHP < 5.5.11 ([2], [3]) and of HHVM, it is not possible
     *         to reset this option. Using these PHP engines, it is therefore impossible to
     *         restore this behavior on an existing php-curl-class Curl object
     *
     * @return string
     *
     * [1] https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.2
     * [2] https://github.com/php/php-src/pull/531
     * [3] http://php.net/ChangeLog-5.php#5.5.11
     */
    public function post($url, $data = array(), $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool) $data;
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url);
        if ($follow_303_with_post) {
            $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        } else {
            if (isset($this->options[CURLOPT_CUSTOMREQUEST])) {
                if ((version_compare(PHP_VERSION, '5.5.11') < 0) || defined('HHVM_VERSION')) {
                    trigger_error(
                        'Due to technical limitations of PHP <= 5.5.11 and HHVM, it is not possible to '
                        .'perform a post-redirect-get request using a php-curl-class Curl object that '
                        .'has already been used to perform other types of requests. Either use a new '
                        .'php-curl-class Curl object or upgrade your PHP engine.',
                        E_USER_ERROR
                    );
                } else {
                    $this->setOpt(CURLOPT_CUSTOMREQUEST, null);
                }
            }
        }
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));

        return $this->exec();
    }
    /**
     * Put.
     *
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function put($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            if (is_string($put_data)) {
                $this->setHeader('Content-Length', strlen($put_data));
            }
        }
        if (!empty($put_data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        }

        return $this->exec();
    }
    /**
     * Search.
     *
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    public function search($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'SEARCH');
        $put_data = $this->buildPostData($data);
        if (empty($this->options[CURLOPT_INFILE]) && empty($this->options[CURLOPT_INFILESIZE])) {
            if (is_string($put_data)) {
                $this->setHeader('Content-Length', strlen($put_data));
            }
        }
        if (!empty($put_data)) {
            $this->setOpt(CURLOPT_POSTFIELDS, $put_data);
        }

        return $this->exec();
    }
    /**
     * Set Basic Authentication.
     *
     * @param  $username
     * @param  $password
     */
    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);
    }
    /**
     * Set Digest Authentication.
     *
     * @param  $username
     * @param  $password
     */
    public function setDigestAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);
    }
    /**
     * Set Cookie.
     *
     * @param  $key
     * @param  $value
     */
    public function setCookie($key, $value)
    {
        $name_chars = array();
        foreach (str_split($key) as $name_char) {
            if (!isset($this->rfc2616[$name_char])) {
                $name_chars[] = rawurlencode($name_char);
            } else {
                $name_chars[] = $name_char;
            }
        }
        $value_chars = array();
        foreach (str_split($value) as $value_char) {
            if (!isset($this->rfc6265[$value_char])) {
                $value_chars[] = rawurlencode($value_char);
            } else {
                $value_chars[] = $value_char;
            }
        }
        $this->cookies[implode('', $name_chars)] = implode('', $value_chars);
        $this->setOpt(CURLOPT_COOKIE, implode('; ', array_map(function ($k, $v) {
            return $k.'='.$v;
        }, array_keys($this->cookies), array_values($this->cookies))));
    }
    /**
     * Get Cookie.
     *
     * @param  $key
     *
     * @return mixed
     */
    public function getCookie($key)
    {
        return $this->getResponseCookie($key);
    }
    /**
     * Get Response Cookie.
     *
     * @param  $key
     *
     * @return mixed
     */
    public function getResponseCookie($key)
    {
        return isset($this->responseCookies[$key]) ? $this->responseCookies[$key] : null;
    }
    /**
     * Set Max Filesize.
     *
     * @param  $bytes
     */
    public function setMaxFilesize($bytes)
    {
        // Make compatible with PHP version both before and after 5.5.0. PHP 5.5.0 added the cURL resource as the first
        // argument to the CURLOPT_PROGRESSFUNCTION callback.
        $gte_v550 = version_compare(PHP_VERSION, '5.5.0') >= 0;
        if ($gte_v550) {
            $callback = function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($bytes) {
                // Abort the transfer when $downloaded bytes exceeds maximum $bytes by returning a non-zero value.
                return $downloaded > $bytes ? 1 : 0;
            };
        } else {
            $callback = function ($download_size, $downloaded, $upload_size, $uploaded) use ($bytes) {
                return $downloaded > $bytes ? 1 : 0;
            };
        }
        $this->progress($callback);
    }
    /**
     * Set Port.
     *
     * @param  $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, intval($port));
    }
    /**
     * Set Connect Timeout.
     *
     * @param  $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
    }
    /**
     * Set Cookie String.
     *
     * @param  $string
     *
     * @return bool
     */
    public function setCookieString($string)
    {
        return $this->setOpt(CURLOPT_COOKIE, $string);
    }
    /**
     * Set Cookie File.
     *
     * @param  $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }
    /**
     * Set Cookie Jar.
     *
     * @param  $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }
    /**
     * Set Default JSON Decoder.
     *
     * @param  $assoc
     * @param  $depth
     * @param  $options
     */
    public function setDefaultJsonDecoder()
    {
        $args = func_get_args();
        $this->jsonDecoder = function ($response) use ($args) {
            array_unshift($args, $response);
            // Call json_decode() without the $options parameter in PHP
            // versions less than 5.4.0 as the $options parameter was added in
            // PHP version 5.4.0.
            if (version_compare(PHP_VERSION, '5.4.0', '<')) {
                $args = array_slice($args, 0, 3);
            }
            $json_obj = call_user_func_array('json_decode', $args);
            if (!($json_obj === null)) {
                $response = $json_obj;
            }

            return $response;
        };
    }
    /**
     * Set Default XML Decoder.
     */
    public function setDefaultXmlDecoder()
    {
        $this->xmlDecoder = function ($response) {
            $xml_obj = @simplexml_load_string($response);
            if (!($xml_obj === false)) {
                $response = $xml_obj;
            }

            return $response;
        };
    }
    /**
     * Set Default Decoder.
     *
     * @param  $decoder string|callable
     */
    public function setDefaultDecoder($decoder = 'json')
    {
        if (is_callable($decoder)) {
            $this->defaultDecoder = $decoder;
        } else {
            if ($decoder === 'json') {
                $this->defaultDecoder = $this->jsonDecoder;
            } elseif ($decoder === 'xml') {
                $this->defaultDecoder = $this->xmlDecoder;
            }
        }
    }
    /**
     * Set Default Timeout.
     */
    public function setDefaultTimeout()
    {
        $this->setTimeout(self::DEFAULT_TIMEOUT);
    }
    /**
     * Set Default User Agent.
     */
    public function setDefaultUserAgent()
    {
        $user_agent = 'PHP-Curl-Class/'.self::VERSION.' (+https://github.com/php-curl-class/php-curl-class)';
        $user_agent .= ' PHP/'.PHP_VERSION;
        $curl_version = curl_version();
        $user_agent .= ' curl/'.$curl_version['version'];
        $this->setUserAgent($user_agent);
    }
    /**
     * Set Header.
     *
     * Add extra header to include in the request.
     *
     * @param  $key
     * @param  $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key.': '.$value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }
    /**
     * Set Headers.
     *
     * Add extra headers to include in the request.
     *
     * @param  $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key.': '.$value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }
    /**
     * Set JSON Decoder.
     *
     * @param  $function
     */
    public function setJsonDecoder($function)
    {
        if (is_callable($function)) {
            $this->jsonDecoder = $function;
        }
    }
    /**
     * Set XML Decoder.
     *
     * @param  $function
     */
    public function setXmlDecoder($function)
    {
        if (is_callable($function)) {
            $this->xmlDecoder = $function;
        }
    }
    /**
     * Set Opt.
     *
     * @param  $option
     * @param  $value
     *
     * @return bool
     */
    public function setOpt($option, $value)
    {
        $required_options = array(
            CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
        );
        if (in_array($option, array_keys($required_options), true) && !($value === true)) {
            trigger_error($required_options[$option].' is a required option', E_USER_WARNING);
        }
        $success = curl_setopt($this->curl, $option, $value);
        if ($success) {
            $this->options[$option] = $value;
        }

        return $success;
    }
    /**
     * Set Opts.
     *
     * @param  $options
     *
     * @return bool
     *              Returns true if all options were successfully set. If an option could not be successfully set, false is
     *              immediately returned, ignoring any future options in the options array. Similar to curl_setopt_array()
     */
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            if (!$this->setOpt($option, $value)) {
                return false;
            }
        }

        return true;
    }
    /**
     * Set Referer.
     *
     * @param  $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }
    /**
     * Set Referrer.
     *
     * @param  $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }
    /**
     * Set Timeout.
     *
     * @param  $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }
    /**
     * Set Url.
     *
     * @param  $url
     * @param  $data
     */
    public function setUrl($url, $data = array())
    {
        $this->baseUrl = $url;
        $this->url = $this->buildURL($url, $data);
        $this->setOpt(CURLOPT_URL, $this->url);
    }
    /**
     * Set User Agent.
     *
     * @param  $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }
    /**
     * Success.
     *
     * @param  $callback
     */
    public function success($callback)
    {
        $this->successFunction = $callback;
    }
    /**
     * Unset Header.
     *
     * Remove extra header previously set using Curl::setHeader().
     *
     * @param  $key
     */
    public function unsetHeader($key)
    {
        unset($this->headers[$key]);
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key.': '.$value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }
    /**
     * Remove Header.
     *
     * Remove an internal header from the request.
     * Using `curl -H "Host:" ...' is equivalent to $curl->removeHeader('Host');.
     *
     * @param  $key
     */
    public function removeHeader($key)
    {
        $this->setHeader($key, '');
    }
    /**
     * Verbose.
     *
     * @param bool     $on
     * @param resource $output
     */
    public function verbose($on = true, $output = STDERR)
    {
        // Turn off CURLINFO_HEADER_OUT for verbose to work. This has the side
        // effect of causing Curl::requestHeaders to be empty.
        if ($on) {
            $this->setOpt(CURLINFO_HEADER_OUT, false);
        }
        $this->setOpt(CURLOPT_VERBOSE, $on);
        $this->setOpt(CURLOPT_STDERR, $output);
    }
    /**
     * Destruct.
     */
    public function __destruct()
    {
        $this->close();
    }
    public function __get($name)
    {
        $return = null;
        if (in_array($name, self::$deferredProperties) && is_callable(array($this, $getter = '__get_'.$name))) {
            $return = $this->$name = $this->$getter();
        }

        return $return;
    }
    /**
     * Get Effective Url.
     */
    private function __get_effectiveUrl()
    {
        return $this->getInfo(CURLINFO_EFFECTIVE_URL);
    }
    /**
     * Get Total Time.
     */
    private function __get_totalTime()
    {
        return $this->getInfo(CURLINFO_TOTAL_TIME);
    }
    /**
     * Build Url.
     *
     * @param  $url
     * @param  $data
     *
     * @return string
     */
    private function buildURL($url, $data = array())
    {
        return $url.(empty($data) ? '' : '?'.http_build_query($data, '', '&'));
    }
    /**
     * Parse Headers.
     *
     * @param  $raw_headers
     *
     * @return array
     */
    private function parseHeaders($raw_headers)
    {
        $raw_headers = preg_split('/\r\n/', $raw_headers, null, PREG_SPLIT_NO_EMPTY);
        $http_headers = new CaseInsensitiveArray();
        $raw_headers_count = count($raw_headers);
        for ($i = 1; $i < $raw_headers_count; ++$i) {
            list($key, $value) = explode(':', $raw_headers[$i], 2);
            $key = trim($key);
            $value = trim($value);
            // Use isset() as array_key_exists() and ArrayAccess are not compatible.
            if (isset($http_headers[$key])) {
                $http_headers[$key] .= ','.$value;
            } else {
                $http_headers[$key] = $value;
            }
        }

        return array(isset($raw_headers['0']) ? $raw_headers['0'] : '', $http_headers);
    }
    /**
     * Parse Request Headers.
     *
     * @param  $raw_headers
     *
     * @return array
     */
    private function parseRequestHeaders($raw_headers)
    {
        $request_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($raw_headers);
        $request_headers['Request-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $request_headers[$key] = $value;
        }

        return $request_headers;
    }
    /**
     * Parse Response.
     *
     * @param  $response_headers
     * @param  $raw_response
     *
     * @return mixed
     *               Provided the content-type is determined to be json or xml:
     *               Returns stdClass object when the default json decoder is used and the content-type is json.
     *               Returns SimpleXMLElement object when the default xml decoder is used and the content-type is xml
     */
    private function parseResponse($response_headers, $raw_response)
    {
        $response = $raw_response;
        if (isset($response_headers['Content-Type'])) {
            if (preg_match($this->jsonPattern, $response_headers['Content-Type'])) {
                $json_decoder = $this->jsonDecoder;
                if (is_callable($json_decoder)) {
                    $response = $json_decoder($response);
                }
            } elseif (preg_match($this->xmlPattern, $response_headers['Content-Type'])) {
                $xml_decoder = $this->xmlDecoder;
                if (is_callable($xml_decoder)) {
                    $response = $xml_decoder($response);
                }
            } else {
                $decoder = $this->defaultDecoder;
                if (is_callable($decoder)) {
                    $response = $decoder($response);
                }
            }
        }

        return $response;
    }
    /**
     * Parse Response Headers.
     *
     * @param  $raw_response_headers
     *
     * @return array
     */
    private function parseResponseHeaders($raw_response_headers)
    {
        $response_header_array = explode("\r\n\r\n", $raw_response_headers);
        $response_header = '';
        for ($i = count($response_header_array) - 1; $i >= 0; --$i) {
            if (stripos($response_header_array[$i], 'HTTP/') === 0) {
                $response_header = $response_header_array[$i];
                break;
            }
        }
        $response_headers = new CaseInsensitiveArray();
        list($first_line, $headers) = $this->parseHeaders($response_header);
        $response_headers['Status-Line'] = $first_line;
        foreach ($headers as $key => $value) {
            $response_headers[$key] = $value;
        }

        return $response_headers;
    }
    /**
     * Is Array Assoc.
     *
     * @param  $array
     *
     * @return bool
     */
    public static function is_array_assoc($array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
    /**
     * Is Array Multidim.
     *
     * @param  $array
     *
     * @return bool
     */
    public static function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return (bool) count(array_filter($array, 'is_array'));
    }
    /**
     * Array Flatten Multidim.
     *
     * @param  $array
     * @param  $prefix
     *
     * @return array
     */
    public static function array_flatten_multidim($array, $prefix = false)
    {
        $return = array();
        if (is_array($array) || is_object($array)) {
            if (empty($array)) {
                $return[$prefix] = '';
            } else {
                foreach ($array as $key => $value) {
                    if (is_scalar($value)) {
                        if ($prefix) {
                            $return[$prefix.'['.$key.']'] = $value;
                        } else {
                            $return[$key] = $value;
                        }
                    } else {
                        if ($value instanceof \CURLFile) {
                            $return[$key] = $value;
                        } else {
                            $return = array_merge(
                                $return,
                                self::array_flatten_multidim(
                                    $value,
                                    $prefix ? $prefix.'['.$key.']' : $key
                                )
                            );
                        }
                    }
                }
            }
        } elseif ($array === null) {
            $return[$prefix] = $array;
        }

        return $return;
    }
}
class MultiCurl
{
    public $baseUrl = null;
    public $multiCurl;
    private $curls = array();
    private $activeCurls = array();
    private $isStarted = false;
    private $concurrency = 25;
    private $nextCurlId = 0;
    private $beforeSendFunction = null;
    private $successFunction = null;
    private $errorFunction = null;
    private $completeFunction = null;
    private $cookies = array();
    private $headers = array();
    private $options = array();
    private $jsonDecoder = null;
    private $xmlDecoder = null;
    /**
     * Construct.
     *
     * @param  $base_url
     */
    public function __construct($base_url = null)
    {
        $this->multiCurl = curl_multi_init();
        $this->headers = new CaseInsensitiveArray();
        $this->setUrl($base_url);
    }
    /**
     * Add Delete.
     *
     * @param  $url
     * @param  $query_parameters
     * @param  $data
     *
     * @return object
     */
    public function addDelete($url, $query_parameters = array(), $data = array())
    {
        if (is_array($url)) {
            $data = $query_parameters;
            $query_parameters = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $query_parameters);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Download.
     *
     * @param  $url
     * @param  $mixed_filename
     *
     * @return object
     */
    public function addDownload($url, $mixed_filename)
    {
        $curl = new Curl();
        $curl->setUrl($url);
        // Use tmpfile() or php://temp to avoid "Too many open files" error.
        if (is_callable($mixed_filename)) {
            $callback = $mixed_filename;
            $curl->downloadCompleteFunction = $callback;
            $curl->fileHandle = tmpfile();
        } else {
            $filename = $mixed_filename;
            $curl->downloadCompleteFunction = function ($instance, $fh) use ($filename) {
                file_put_contents($filename, stream_get_contents($fh));
            };
            $curl->fileHandle = fopen('php://temp', 'wb');
        }
        $curl->setOpt(CURLOPT_FILE, $curl->fileHandle);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Get.
     *
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addGet($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Head.
     *
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addHead($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $data);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
        $curl->setOpt(CURLOPT_NOBODY, true);
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Options.
     *
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addOptions($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url, $data);
        $curl->removeHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Patch.
     *
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addPatch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->removeHeader('Content-Length');
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $curl->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Post.
     *
     * @param  $url
     * @param  $data
     * @param  $follow_303_with_post
     *     If true, will cause 303 redirections to be followed using GET requests (default: false).
     *     Note: Redirections are only followed if the CURLOPT_FOLLOWLOCATION option is set to true
     *
     * @return object
     */
    public function addPost($url, $data = array(), $follow_303_with_post = false)
    {
        if (is_array($url)) {
            $follow_303_with_post = (bool) $data;
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        if (is_array($data) && empty($data)) {
            $curl->removeHeader('Content-Length');
        }
        $curl->setUrl($url);
        /*
         * For post-redirect-get requests, the CURLOPT_CUSTOMREQUEST option must not
         * be set, otherwise cURL will perform POST requests for redirections.
         */
        if (!$follow_303_with_post) {
            $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        }
        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $curl->buildPostData($data));
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Put.
     *
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addPut($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $put_data = $curl->buildPostData($data);
        if (is_string($put_data)) {
            $curl->setHeader('Content-Length', strlen($put_data));
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Search.
     *
     * @param  $url
     * @param  $data
     *
     * @return object
     */
    public function addSearch($url, $data = array())
    {
        if (is_array($url)) {
            $data = $url;
            $url = $this->baseUrl;
        }
        $curl = new Curl();
        $curl->setUrl($url);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'SEARCH');
        $put_data = $curl->buildPostData($data);
        if (is_string($put_data)) {
            $curl->setHeader('Content-Length', strlen($put_data));
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $put_data);
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Add Curl.
     *
     * Add a Curl instance to the handle queue.
     *
     * @param  $curl
     *
     * @return object
     */
    public function addCurl(Curl $curl)
    {
        $this->queueHandle($curl);

        return $curl;
    }
    /**
     * Before Send.
     *
     * @param  $callback
     */
    public function beforeSend($callback)
    {
        $this->beforeSendFunction = $callback;
    }
    /**
     * Close.
     */
    public function close()
    {
        foreach ($this->curls as $curl) {
            $curl->close();
        }
        if (is_resource($this->multiCurl)) {
            curl_multi_close($this->multiCurl);
        }
    }
    /**
     * Complete.
     *
     * @param  $callback
     */
    public function complete($callback)
    {
        $this->completeFunction = $callback;
    }
    /**
     * Error.
     *
     * @param  $callback
     */
    public function error($callback)
    {
        $this->errorFunction = $callback;
    }
    /**
     * Get Opt.
     *
     * @param  $option
     *
     * @return mixed
     */
    public function getOpt($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }
    /**
     * Set Basic Authentication.
     *
     * @param  $username
     * @param  $password
     */
    public function setBasicAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);
    }
    /**
     * Set Concurrency.
     *
     * @param  $concurrency
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
    }
    /**
     * Set Digest Authentication.
     *
     * @param  $username
     * @param  $password
     */
    public function setDigestAuthentication($username, $password = '')
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);
    }
    /**
     * Set Cookie.
     *
     * @param  $key
     * @param  $value
     */
    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
    }
    /**
     * Set Port.
     *
     * @param  $port
     */
    public function setPort($port)
    {
        $this->setOpt(CURLOPT_PORT, intval($port));
    }
    /**
     * Set Connect Timeout.
     *
     * @param  $seconds
     */
    public function setConnectTimeout($seconds)
    {
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $seconds);
    }
    /**
     * Set Cookie String.
     *
     * @param  $string
     *
     * @return bool
     */
    public function setCookieString($string)
    {
        return $this->setOpt(CURLOPT_COOKIE, $string);
    }
    /**
     * Set Cookie File.
     *
     * @param  $cookie_file
     */
    public function setCookieFile($cookie_file)
    {
        $this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
    }
    /**
     * Set Cookie Jar.
     *
     * @param  $cookie_jar
     */
    public function setCookieJar($cookie_jar)
    {
        $this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
    }
    /**
     * Set Header.
     *
     * Add extra header to include in the request.
     *
     * @param  $key
     * @param  $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
    /**
     * Set Headers.
     *
     * Add extra headers to include in the request.
     *
     * @param  $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
    }
    /**
     * Set JSON Decoder.
     *
     * @param  $function
     */
    public function setJsonDecoder($function)
    {
        if (is_callable($function)) {
            $this->jsonDecoder = $function;
        }
    }
    /**
     * Set XML Decoder.
     *
     * @param  $function
     */
    public function setXmlDecoder($function)
    {
        if (is_callable($function)) {
            $this->xmlDecoder = $function;
        }
    }
    /**
     * Set Opt.
     *
     * @param  $option
     * @param  $value
     */
    public function setOpt($option, $value)
    {
        $this->options[$option] = $value;
    }
    /**
     * Set Opts.
     *
     * @param  $options
     */
    public function setOpts($options)
    {
        foreach ($options as $option => $value) {
            $this->setOpt($option, $value);
        }
    }
    /**
     * Set Referer.
     *
     * @param  $referer
     */
    public function setReferer($referer)
    {
        $this->setReferrer($referer);
    }
    /**
     * Set Referrer.
     *
     * @param  $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }
    /**
     * Set Timeout.
     *
     * @param  $seconds
     */
    public function setTimeout($seconds)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $seconds);
    }
    /**
     * Set Url.
     *
     * @param  $url
     */
    public function setUrl($url)
    {
        $this->baseUrl = $url;
    }
    /**
     * Set User Agent.
     *
     * @param  $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }
    /**
     * Start.
     */
    public function start()
    {
        if ($this->isStarted) {
            return;
        }
        $this->isStarted = true;
        $concurrency = $this->concurrency;
        if ($concurrency > count($this->curls)) {
            $concurrency = count($this->curls);
        }
        for ($i = 0; $i < $concurrency; ++$i) {
            $this->initHandle(array_pop($this->curls));
        }
        do {
            curl_multi_select($this->multiCurl);
            curl_multi_exec($this->multiCurl, $active);
            while (!($info_array = curl_multi_info_read($this->multiCurl)) === false) {
                if ($info_array['msg'] === CURLMSG_DONE) {
                    foreach ($this->activeCurls as $key => $ch) {
                        if ($ch->curl === $info_array['handle']) {
                            // Set the error code for multi handles using the "result" key in the array returned by
                            // curl_multi_info_read(). Using curl_errno() on a multi handle will incorrectly return 0
                            // for errors.
                            $ch->curlErrorCode = $info_array['result'];
                            $ch->exec($ch->curl);
                            unset($this->activeCurls[$key]);
                            // Start a new request before removing the handle of the completed one.
                            if (count($this->curls) >= 1) {
                                $this->initHandle(array_pop($this->curls));
                            }
                            curl_multi_remove_handle($this->multiCurl, $ch->curl);
                            break;
                        }
                    }
                }
            }
            if (!$active) {
                $active = count($this->activeCurls);
            }
        } while ($active > 0);
        $this->isStarted = false;
    }
    /**
     * Success.
     *
     * @param  $callback
     */
    public function success($callback)
    {
        $this->successFunction = $callback;
    }
    /**
     * Unset Header.
     *
     * Remove extra header previously set using Curl::setHeader().
     *
     * @param  $key
     */
    public function unsetHeader($key)
    {
        unset($this->headers[$key]);
    }
    /**
     * Remove Header.
     *
     * Remove an internal header from the request.
     * Using `curl -H "Host:" ...' is equivalent to $curl->removeHeader('Host');.
     *
     * @param  $key
     */
    public function removeHeader($key)
    {
        $this->setHeader($key, '');
    }
    /**
     * Verbose.
     *
     * @param bool     $on
     * @param resource $output
     */
    public function verbose($on = true, $output = STDERR)
    {
        // Turn off CURLINFO_HEADER_OUT for verbose to work. This has the side
        // effect of causing Curl::requestHeaders to be empty.
        if ($on) {
            $this->setOpt(CURLINFO_HEADER_OUT, false);
        }
        $this->setOpt(CURLOPT_VERBOSE, $on);
        $this->setOpt(CURLOPT_STDERR, $output);
    }
    /**
     * Destruct.
     */
    public function __destruct()
    {
        $this->close();
    }
    /**
     * Queue Handle.
     *
     * @param  $curl
     */
    private function queueHandle($curl)
    {
        // Use sequential ids to allow for ordered post processing.
        $curl->id = $this->nextCurlId++;
        $this->curls[$curl->id] = $curl;
    }
    /**
     * Init Handle.
     *
     * @param  $curl
     *
     * @throws \ErrorException
     */
    private function initHandle($curl)
    {
        // Set callbacks if not already individually set.
        if ($curl->beforeSendFunction === null) {
            $curl->beforeSend($this->beforeSendFunction);
        }
        if ($curl->successFunction === null) {
            $curl->success($this->successFunction);
        }
        if ($curl->errorFunction === null) {
            $curl->error($this->errorFunction);
        }
        if ($curl->completeFunction === null) {
            $curl->complete($this->completeFunction);
        }
        $curl->setOpts($this->options);
        $curl->setHeaders($this->headers);
        foreach ($this->cookies as $key => $value) {
            $curl->setCookie($key, $value);
        }
        $curl->setJsonDecoder($this->jsonDecoder);
        $curl->setXmlDecoder($this->xmlDecoder);
        $curlm_error_code = curl_multi_add_handle($this->multiCurl, $curl->curl);
        if (!($curlm_error_code === CURLM_OK)) {
            throw new \ErrorException('cURL multi add handle error: '.curl_multi_strerror($curlm_error_code));
        }
        $this->activeCurls[$curl->id] = $curl;
        $this->responseCookies = array();
        $curl->call($curl->beforeSendFunction);
    }
}
