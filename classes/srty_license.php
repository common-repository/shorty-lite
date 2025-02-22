<?php

class Srty_license {

    private $_local_encryption_key = '850590a229d2e0fc8604f698178f081d00631f5c';

    // successful code
    const OK = 'ok';
    const CONNECTION_ERROR = 'connection_error';
    // license error codes
    const LICENSE_EMPTY = 'license_empty';
    const LICENSE_NOT_FOUND = 'license_not_found';
    const LICENSE_DISABLED = 'license_disabled';
    const LICENSE_EXPIRED = 'license_expired';
    const LICENSE_SERVER_ERROR = 'license_server_error';
    // activation/deactivation problem codes
    const ACTIVATION_SERVER_ERROR = 'activation_server_error';
    const ERROR_INVALID_INPUT = 'invalid_input';
    const ERROR_NO_SPARE_ACTIVATIONS = 'no_spare_activations';
    const ERROR_NO_ACTIVATION_FOUND = 'no_activation_found';
    const ERROR_NO_REACTIVATION_ALLOWED = 'no_reactivation_allowed';
    const ERROR_NO_RESPONSE = 'no_response';
    const ERROR_OTHER = 'other_error';

    public $messages = array(
        self::OK => 'License OK',
        self::CONNECTION_ERROR => 'Could not connect to licensing server - please try again later',
        self::LICENSE_EMPTY => 'Empty or invalid license key submitted',
        self::LICENSE_NOT_FOUND => 'License key not found on licensing server',
        self::LICENSE_DISABLED => 'License key has been disabled',
        self::LICENSE_EXPIRED => 'License key expired',
        self::LICENSE_SERVER_ERROR => 'License server is not available - please try again later',
        self::ACTIVATION_SERVER_ERROR => 'Activation server error',
        self::ERROR_INVALID_INPUT => 'Activation failed: invalid input',
        self::ERROR_NO_SPARE_ACTIVATIONS => 'You have exceeded the number of installations for this license',//'No more activations allowed',
        self::ERROR_NO_ACTIVATION_FOUND => 'No activation found for this installation',
        self::ERROR_NO_REACTIVATION_ALLOWED => 'Re-activation is not allowed',
        self::ERROR_NO_RESPONSE => 'Internal problem on activation server',
        self::ERROR_OTHER => 'Error returned from activation server',
    );
    protected $api_version = 1;

    /** @var int last code returned from */
    protected $code = self::OK;

    /** @var string last error message */
    protected $message;

    /** @var string license key */
    protected $key;

    /** @var string activation url */
    protected $url;

    /** @var int call home every .. days, 0 - disabled */
    protected $call_home_days = 3;

    /** @var int grace period .. hours, 24 - default. if "call home" failed, allow to continue */
    protected $grace_period = 24;

    /** @var array request_vars: set of 
     *          'ip' : 'Server IP' : detected automatically by getServerIp() method
     *          'url' : 'Installation URL' : you must override getRootUrl() method to return it
     *          'domain' : 'Domain' : detected automatically by getDomain() method
     *          'sdomain' : 'Secure Domain (if application can use 2 domains)': override getSdomain() method to return
     *          'hardware-id' : Hardware ID - it can be any info on your choice that identifies the installation - override getHardwareId() method to return
     *  */
    protected $request_vars = array(0 => 'domain', 1 => 'url');

    /** @var array() */
    public $openurl_callbacks = array(// fsockopen, curl, fopen
        array('this', 'openUrlCurl'),
        array('this', 'openUrlFsockopen'),
        array('this', 'openUrlFopen'),
    );

    /** @var stdclass */
    public $license_response;

    /** @var Am_LicenseChecker_ActivationResponse */
    public $activation_response;

    /** @var array cache */
    protected $_request;

    /**
     * Constructor
     * @param string $key license key value
     * @param string $url activation url
     * @param string|array $hash verification hash
     */
    public function __construct($key, $url) {
        $this->key = $key;
        $this->url = $url;
//        $this->hash = $hash;
    }

    function setError($code, $message = null) {
        $this->code = $code;
        $this->message = $message !== null ? $message : $this->messages[$code];
        return $this;
    }

    /**
     * Check license key against remote server
     * @return bool true of success
     * @see getCode()
     * @see getMessage()
     */
    function checkLicenseKey() {
        $body = $this->makeRequest('GET', 'check-license', array('key' => $this->key), self::LICENSE_SERVER_ERROR);
        if ($body !== FALSE) {
            $this->license_response = $body;
            $this->setError($body->code);

            return $this->code === self::OK;
        }
    }

    /**
     * Activate license for this installation
     * @param string $activation_cache must be stored between requests
     */
    function activate(& $activation_cache) {
        // get request vars, compare to specified in activation
        $request = $this->getRequest();

        $this->activation_response = $this->processActivationResponse(
                $this->makeRequest('POST', 'activate', array('key' => $this->key, 'request' => $request), self::ACTIVATION_SERVER_ERROR));
        $activation_cache = $this->encodeResponse($this->activation_response);
        return ($this->code === self::OK);
    }

    /**
     * Check license activation
     * you script need to store $activation_code string somewhere in database
     * or text file - this variable contains encoded activation and "call home" 
     * status
     * @return bool
     * @see getCode()
     * @see getMessage()
     */
    function checkActivation(& $activation_cache) {
        $this->activation_response = $this->decodeResponse($activation_cache);
        if (!empty($this->activation_response->next_check)) {
            $request = $this->getRequest();
            if ($this->activation_response->request == $request) {
                $this->code = $this->activation_response->code;
                $this->message = $this->activation_response->message;
                if ($this->activation_response->next_check > $this->time())
                    return $this->activation_response->return;
            }
        }
        // get request vars, compare to specified in activation
        $request = $this->getRequest();
        $ret = false;
        $this->activation_response = $this->processActivationResponse(
                $this->makeRequest('POST', 'check-activation', array('key' => $this->key, 'request' => $request), self::ACTIVATION_SERVER_ERROR));
        $activation_cache = $this->encodeResponse($this->activation_response);
        return $this->activation_response->return;
    }

    /**
     * De-activates current installation - frees up license activation
     * to make new activation somewhere else 
     * 
     * Server will check if "reactivations" limit is not over
     * 
     * Software will stop working on current location
     */
    function deactivate(& $activation_cache) {
        $request = $this->getRequest();

        $body = $this->makeRequest('POST', 'deactivate', array('key' => $this->key, 'request' => $request,), self::ACTIVATION_SERVER_ERROR);
        $activation_cache = null;
        return ($this->code === self::OK);
    }

    /** @return string last error message */
    function getMessage() {
        return $this->message;
    }

    /** @return code last error code */
    function getCode() {
        return $this->code;
    }

    ////////////////// INTERNAL FUNCTIONS ////////////////////////////////////
    function makeRequest($method, $action, $params, $errorCode, $responseClass = 'stdclass') {
        // check local key expiration time - if expired, fetch it again
        list($body, $status, $error) = $this->openUrl($method, $this->url . '/' . $action, $params);
        if ($status != 200) {
            $this->setError(self::CONNECTION_ERROR);
            return false;
        }
        $body = json_decode($body, false);
        if (!is_object($body) || empty($body->code)) {
            $this->setError($errorCode);
            return false;
        }
        $this->code = $body->code;
        $this->message = $body->message;
        return $body;
    }

    /**
     * @param stdclass $resp decoded response from the server
     * @param bool $ret return code to set : true if OK
     * @return \Am_LicenseChecker_ActivationResponse
     */
    protected function processActivationResponse($resp) {
        $response = new Am_LicenseChecker_ActivationResponse($resp);
        $response->request = $this->getRequest();
        $response->return = (isset($resp->code) ? $resp->code : '') == self::OK;
        if ($this->call_home_days > 0)
            $response->next_check = min($response->next_check, $this->call_home_days * 3600 * 24);
        switch ($response->code) {
            case self::OK:
                break;
            case self::ACTIVATION_SERVER_ERROR:;
            case self::CONNECTION_ERROR:;
                $response->first_failed = empty($this->activation_response->first_failed) ?
                        $this->time() : $this->activation_response->first_failed;
                if ($response->first_failed < $this->grace_period * 3600) {
                    $response->next_check = $this->time() + 120; // next check after 2 minutes
                    $response->return = true;
                }
                break;
            case self::LICENSE_EXPIRED:
                if ((string) $response->grace_period == 'true' ||
                        (string) $response->grace_period == '1')
                    $response->return = true;
                break;
        }
        if ($response->next_check <= 0)
            $response->next_check = 120;
        $response->next_check += $this->time();
        return $response;
    }

    /** @return array($body,$status,$errormessage) */
    function openUrl($method, $url, array $params = array()) {
        $params['api_version'] = $this->api_version;
        foreach ($this->openurl_callbacks as $func) {
            if (is_array($func) && $func[0] == 'this')
                $func[0] = $this;
            $a = call_user_func($func, $method, $url, $params);
            if (is_array($a) && count($a) == 3)
                return $a;
        }
        return array(null, 600, 'Could not fetch URL');
    }

    private function decodeResponse($response) {
        $ret = new Am_LicenseChecker_ActivationResponse;
        if (empty($response))
            return $ret;
        $c = new Am_Crypt_Blowfish($this->_local_encryption_key);
        try {
            $ret = $c->decrypt(base64_decode($response));
        } catch (Exception $e) {
            trigger_error("Decryption problem: " . $e->getClass() . ":" . $e->getMessage(), E_USER_NOTICE);
            return $ret;
        }
        return @unserialize($ret);
    }

    private function encodeResponse($response) {
        if (!is_object($response))
            return null;
        $response = serialize($response);
        $c = new Am_Crypt_Blowfish($this->_local_encryption_key);
        try {
            $ret = $c->encrypt($response);
        } catch (Exception $e) {
            trigger_error("Encryption problem: " . $e->getClass() . ":" . $e->getMessage(), E_USER_NOTICE);
            return null;
        }
        return base64_encode($ret);
    }

    public function time() {
        return time();
    }

    public function getRequest() {
        if (!empty($this->_request))
            return $this->_request;
        $ret = array();
        foreach ($this->request_vars as $k)
            switch ($k) {
                case 'ip' : $ret[$k] = $this->getServerIp();
                    break;
                case 'domain' : $ret[$k] = $this->getDomain();
                    break;
                case 'sdomain' : $ret[$k] = $this->getSdomain();
                    break;
                case 'url' : $ret[$k] = $this->getRootUrl();
                    break;
                case 'hardware-id': $ret[$k] = $this->getHardwareId();
                    break;
            }
        ksort($ret);
        $this->_request = $ret;
        return $ret;
    }

    public function getHardwareId() {
        throw new Exception(__METHOD__ . " not implemented - must be implemented by software author");
    }

    public function getRootUrl() {
        require_once( ABSPATH . 'wp-includes/link-template.php' );
        return home_url();
//        return $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
//        throw new Exception(__METHOD__ . " not implemented - must be implemented by software author");
    }

    public function getSdomain() {
        throw new Exception(__METHOD__ . " not implemented - must be implemented by software author");
    }

    public function getServerIp() {
        return @$_SERVER['SERVER_ADDR'];
    }

    public function getDomain() {
        return $_SERVER['HTTP_HOST'];
    }

    function openUrlFsockopen($method, $url, $params) {
        $status = 500;
        $errormessage = "connection failed";
        $body = "";
        $url_parts = parse_url($url);
        if ($url_parts['scheme'] == 'https')
            $f = fsockopen('ssl://' . $url_parts['host'], 443);
        else
            $f = fsockopen("tcp://" . $url_parts['host'], 80);
        if (!$f)
            return array($body, $status, $errormessage);
        if (strcasecmp('post', $method)) {
            $query = '?' . http_build_query($params);
        } else {
            $query = "";
        }
        fwrite($f, strtoupper($method) . " " . $url_parts['path'] . $query . " HTTP/1.1\r\n");
        if (!strcasecmp('post', $method)) {
            $data = http_build_query($params) . "\r\n";
            $len = strlen($data);
            fwrite($f, "Content-type: application/x-www-form-urlencoded\r\nContent-length: $len\r\n");
        } else {
            $data = "";
        }
        fwrite($f, "Host: " . $url_parts['host'] . "\r\nConnection: Close\r\n\r\n" . $data);
        $body = stream_get_contents($f);
        $body = preg_replace('/\r$/m', '', $body);
        list($headers, $body) = preg_split('/^$/ms', $body, 2);
        $headers = trim($headers);
        $body = trim($body);
        if (preg_match('/^Transfer-Encoding:\s+chunked/m', $headers)) {
            $body = preg_replace('/[a-eA-E0-9]+\n(.+)0(\n)?/ms', '\\1', $body);
        }
        if (preg_match('/^HTTP\/1\.. (\d\d\d) (.+)$/m', $headers, $regs)) {
            $status = $regs[1];
            $errormessage = $regs[2];
        }
        fclose($f);
        return array($body, $status, $errormessage);
    }

    function openUrlCurl($method, $url, $params) {
        $status = 500;
        $errormessage = "connection failed";
        $body = "";
        if (!function_exists('curl_init'))
            return
                    array($body, $status, $errormessage);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //in second
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.001 (windows; U; NT4.0; en-US; rv:1.0) Gecko/25250101');
        if (!strcasecmp('post', $method)) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        }
        if (($body = curl_exec($ch)) === false) {
            return array($status, $body, curl_errno($ch) . ':' . curl_error($ch));
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($body, $status, '');
    }

    function openUrlFopen($method, $url, $params) {
        $status = 500;
        $errormessage = "connection failed";
        $body = "";
        $options = array();
        if (!strcasecmp($method, 'post')) {
            $options['http'] = array(
                'method' => 'POST',
                'header' => "Connection: close\r\n" .
                "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($params),
            );
        } else {
            $url .= '?' . http_build_query($params);
        }
        $context = stream_context_create($options);
        $f = @fopen($url, 'r', false, $context);
        if (!empty($http_response_header)) {
            if (preg_match('/^HTTP\/1\.. (\d\d\d) (.+)$/', $http_response_header[0], $regs)) {
                $status = $regs[1];
                $errormessage = $regs[2];
                if ($status != 200)
                    return array($body, $status, $errormessage);
            }
        }
        if (!$f)
            return array($body, $status, $errormessage);
        $body = stream_get_contents($f);
        fclose($f);
        return array($body, $status, $errormessage);
    }

}

class Am_LicenseChecker_ActivationResponse {

    public $code;
    public $message;
    public $activation_code;
    public $scheme_id;
    public $license_expires;
    public $next_check;
    public $return;
    public $first_failed;

    function __construct($response = null) {
        if ($response)
            foreach (get_object_vars($response) as $k => $v)
                $this->$k = $v;
    }

}

/**
 * Crypt_Blowfish allows for encryption and decryption on the fly using
 * the Blowfish algorithm. Crypt_Blowfish does not require the mcrypt
 * PHP extension, it uses only PHP.
 * Crypt_Blowfish support encryption/decryption with or without a secret key.
 *
 * Modified by alex@cgi-central.net : class renamed to avoid conflicts,
 *   PEAR error handling changed to exceptions
 * 
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Encryption
 * @package    Crypt_Blowfish
 * @author     Matthew Fonda <mfonda@php.net>
 * @copyright  2005 Matthew Fonda
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Blowfish.php,v 1.81 2005/05/30 18:40:36 mfonda Exp $
 * @link       http://pear.php.net/package/Crypt_Blowfish
 */
class Am_Crypt_Blowfish {

    var $_P = array();
    var $_S = array();
    var $_td = null;
    var $_iv = null;

    function __construct($key) {
        if (extension_loaded('mcrypt')) {
            $this->_td = mcrypt_module_open(MCRYPT_BLOWFISH, '', 'ecb', '');
            $this->_iv = mcrypt_create_iv(8, MCRYPT_RAND);
        }
        $this->setKey($key);
    }

    function isReady() {
        return true;
    }

    function init() {
        $this->_init();
    }

    function _init() {
        $defaults = new Am_Crypt_Blowfish_DefaultKey();
        $this->_P = $defaults->P;
        $this->_S = $defaults->S;
    }

    function _encipher(&$Xl, &$Xr) {
        for ($i = 0; $i < 16; $i++) {
            $temp = $Xl ^ $this->_P[$i];
            $Xl = ((($this->_S[0][($temp >> 24) & 255] +
                    $this->_S[1][($temp >> 16) & 255]) ^
                    $this->_S[2][($temp >> 8) & 255]) +
                    $this->_S[3][$temp & 255]) ^ $Xr;
            $Xr = $temp;
        }
        $Xr = $Xl ^ $this->_P[16];
        $Xl = $temp ^ $this->_P[17];
    }

    function _decipher(&$Xl, &$Xr) {
        for ($i = 17; $i > 1; $i--) {
            $temp = $Xl ^ $this->_P[$i];
            $Xl = ((($this->_S[0][($temp >> 24) & 255] +
                    $this->_S[1][($temp >> 16) & 255]) ^
                    $this->_S[2][($temp >> 8) & 255]) +
                    $this->_S[3][$temp & 255]) ^ $Xr;
            $Xr = $temp;
        }
        $Xr = $Xl ^ $this->_P[1];
        $Xl = $temp ^ $this->_P[0];
    }

    /**
     * Encrypts a string
     *
     * @param string $plainText
     * @return string Returns cipher text on success, PEAR_Error on failure
     * @access public
     */
    function encrypt($plainText) {
        if (!is_string($plainText)) {
            $this->raiseError('Plain text must be a string');
        }

        if (extension_loaded('mcrypt')) {
            return mcrypt_generic($this->_td, $plainText);
        }

        $cipherText = '';
        $len = strlen($plainText);
        $plainText .= str_repeat(chr(0), (8 - ($len % 8)) % 8);
        for ($i = 0; $i < $len; $i += 8) {
            list(, $Xl, $Xr) = unpack("N2", substr($plainText, $i, 8));
            $this->_encipher($Xl, $Xr);
            $cipherText .= pack("N2", $Xl, $Xr);
        }
        return $cipherText;
    }

    function decrypt($cipherText) {
        if (!is_string($cipherText)) {
            $this->raiseError('Chiper text must be a string');
        }

        if (extension_loaded('mcrypt')) {
            return mdecrypt_generic($this->_td, $cipherText);
        }

        $plainText = '';
        $len = strlen($cipherText);
        $cipherText .= str_repeat(chr(0), (8 - ($len % 8)) % 8);
        for ($i = 0; $i < $len; $i += 8) {
            list(, $Xl, $Xr) = unpack("N2", substr($cipherText, $i, 8));
            $this->_decipher($Xl, $Xr);
            $plainText .= pack("N2", $Xl, $Xr);
        }
        return $plainText;
    }

    function setKey($key) {
        if (!is_string($key)) {
            $this->raiseError('Key must be a string');
        }

        $len = strlen($key);

        if ($len > 56 || $len == 0) {
            $this->raiseError('Key must be less than 56 characters and non-zero. Supplied key length: ' . $len);
        }

        if (extension_loaded('mcrypt')) {
            mcrypt_generic_init($this->_td, $key, $this->_iv);
            return true;
        }

        $this->_init();

        $k = 0;
        $data = 0;
        $datal = 0;
        $datar = 0;

        for ($i = 0; $i < 18; $i++) {
            $data = 0;
            for ($j = 4; $j > 0; $j--) {
                $data = $data << 8 | ord($key{$k});
                $k = ($k + 1) % $len;
            }
            $this->_P[$i] ^= $data;
        }

        for ($i = 0; $i <= 16; $i += 2) {
            $this->_encipher($datal, $datar);
            $this->_P[$i] = $datal;
            $this->_P[$i + 1] = $datar;
        }
        for ($i = 0; $i < 256; $i += 2) {
            $this->_encipher($datal, $datar);
            $this->_S[0][$i] = $datal;
            $this->_S[0][$i + 1] = $datar;
        }
        for ($i = 0; $i < 256; $i += 2) {
            $this->_encipher($datal, $datar);
            $this->_S[1][$i] = $datal;
            $this->_S[1][$i + 1] = $datar;
        }
        for ($i = 0; $i < 256; $i += 2) {
            $this->_encipher($datal, $datar);
            $this->_S[2][$i] = $datal;
            $this->_S[2][$i + 1] = $datar;
        }
        for ($i = 0; $i < 256; $i += 2) {
            $this->_encipher($datal, $datar);
            $this->_S[3][$i] = $datal;
            $this->_S[3][$i + 1] = $datar;
        }
        return true;
    }

    function raiseError($message) {
        throw new Exception("Encryption: $message");
    }

}

class Am_Crypt_Blowfish_DefaultKey {

    var $P = array();
    var $S = array();

    function __construct() {
        $this->P = array(
            0x243F6A88, 0x85A308D3, 0x13198A2E, 0x03707344,
            0xA4093822, 0x299F31D0, 0x082EFA98, 0xEC4E6C89,
            0x452821E6, 0x38D01377, 0xBE5466CF, 0x34E90C6C,
            0xC0AC29B7, 0xC97C50DD, 0x3F84D5B5, 0xB5470917,
            0x9216D5D9, 0x8979FB1B
        );

        $this->S = array(
            array(
                0xD1310BA6, 0x98DFB5AC, 0x2FFD72DB, 0xD01ADFB7,
                0xB8E1AFED, 0x6A267E96, 0xBA7C9045, 0xF12C7F99,
                0x24A19947, 0xB3916CF7, 0x0801F2E2, 0x858EFC16,
                0x636920D8, 0x71574E69, 0xA458FEA3, 0xF4933D7E,
                0x0D95748F, 0x728EB658, 0x718BCD58, 0x82154AEE,
                0x7B54A41D, 0xC25A59B5, 0x9C30D539, 0x2AF26013,
                0xC5D1B023, 0x286085F0, 0xCA417918, 0xB8DB38EF,
                0x8E79DCB0, 0x603A180E, 0x6C9E0E8B, 0xB01E8A3E,
                0xD71577C1, 0xBD314B27, 0x78AF2FDA, 0x55605C60,
                0xE65525F3, 0xAA55AB94, 0x57489862, 0x63E81440,
                0x55CA396A, 0x2AAB10B6, 0xB4CC5C34, 0x1141E8CE,
                0xA15486AF, 0x7C72E993, 0xB3EE1411, 0x636FBC2A,
                0x2BA9C55D, 0x741831F6, 0xCE5C3E16, 0x9B87931E,
                0xAFD6BA33, 0x6C24CF5C, 0x7A325381, 0x28958677,
                0x3B8F4898, 0x6B4BB9AF, 0xC4BFE81B, 0x66282193,
                0x61D809CC, 0xFB21A991, 0x487CAC60, 0x5DEC8032,
                0xEF845D5D, 0xE98575B1, 0xDC262302, 0xEB651B88,
                0x23893E81, 0xD396ACC5, 0x0F6D6FF3, 0x83F44239,
                0x2E0B4482, 0xA4842004, 0x69C8F04A, 0x9E1F9B5E,
                0x21C66842, 0xF6E96C9A, 0x670C9C61, 0xABD388F0,
                0x6A51A0D2, 0xD8542F68, 0x960FA728, 0xAB5133A3,
                0x6EEF0B6C, 0x137A3BE4, 0xBA3BF050, 0x7EFB2A98,
                0xA1F1651D, 0x39AF0176, 0x66CA593E, 0x82430E88,
                0x8CEE8619, 0x456F9FB4, 0x7D84A5C3, 0x3B8B5EBE,
                0xE06F75D8, 0x85C12073, 0x401A449F, 0x56C16AA6,
                0x4ED3AA62, 0x363F7706, 0x1BFEDF72, 0x429B023D,
                0x37D0D724, 0xD00A1248, 0xDB0FEAD3, 0x49F1C09B,
                0x075372C9, 0x80991B7B, 0x25D479D8, 0xF6E8DEF7,
                0xE3FE501A, 0xB6794C3B, 0x976CE0BD, 0x04C006BA,
                0xC1A94FB6, 0x409F60C4, 0x5E5C9EC2, 0x196A2463,
                0x68FB6FAF, 0x3E6C53B5, 0x1339B2EB, 0x3B52EC6F,
                0x6DFC511F, 0x9B30952C, 0xCC814544, 0xAF5EBD09,
                0xBEE3D004, 0xDE334AFD, 0x660F2807, 0x192E4BB3,
                0xC0CBA857, 0x45C8740F, 0xD20B5F39, 0xB9D3FBDB,
                0x5579C0BD, 0x1A60320A, 0xD6A100C6, 0x402C7279,
                0x679F25FE, 0xFB1FA3CC, 0x8EA5E9F8, 0xDB3222F8,
                0x3C7516DF, 0xFD616B15, 0x2F501EC8, 0xAD0552AB,
                0x323DB5FA, 0xFD238760, 0x53317B48, 0x3E00DF82,
                0x9E5C57BB, 0xCA6F8CA0, 0x1A87562E, 0xDF1769DB,
                0xD542A8F6, 0x287EFFC3, 0xAC6732C6, 0x8C4F5573,
                0x695B27B0, 0xBBCA58C8, 0xE1FFA35D, 0xB8F011A0,
                0x10FA3D98, 0xFD2183B8, 0x4AFCB56C, 0x2DD1D35B,
                0x9A53E479, 0xB6F84565, 0xD28E49BC, 0x4BFB9790,
                0xE1DDF2DA, 0xA4CB7E33, 0x62FB1341, 0xCEE4C6E8,
                0xEF20CADA, 0x36774C01, 0xD07E9EFE, 0x2BF11FB4,
                0x95DBDA4D, 0xAE909198, 0xEAAD8E71, 0x6B93D5A0,
                0xD08ED1D0, 0xAFC725E0, 0x8E3C5B2F, 0x8E7594B7,
                0x8FF6E2FB, 0xF2122B64, 0x8888B812, 0x900DF01C,
                0x4FAD5EA0, 0x688FC31C, 0xD1CFF191, 0xB3A8C1AD,
                0x2F2F2218, 0xBE0E1777, 0xEA752DFE, 0x8B021FA1,
                0xE5A0CC0F, 0xB56F74E8, 0x18ACF3D6, 0xCE89E299,
                0xB4A84FE0, 0xFD13E0B7, 0x7CC43B81, 0xD2ADA8D9,
                0x165FA266, 0x80957705, 0x93CC7314, 0x211A1477,
                0xE6AD2065, 0x77B5FA86, 0xC75442F5, 0xFB9D35CF,
                0xEBCDAF0C, 0x7B3E89A0, 0xD6411BD3, 0xAE1E7E49,
                0x00250E2D, 0x2071B35E, 0x226800BB, 0x57B8E0AF,
                0x2464369B, 0xF009B91E, 0x5563911D, 0x59DFA6AA,
                0x78C14389, 0xD95A537F, 0x207D5BA2, 0x02E5B9C5,
                0x83260376, 0x6295CFA9, 0x11C81968, 0x4E734A41,
                0xB3472DCA, 0x7B14A94A, 0x1B510052, 0x9A532915,
                0xD60F573F, 0xBC9BC6E4, 0x2B60A476, 0x81E67400,
                0x08BA6FB5, 0x571BE91F, 0xF296EC6B, 0x2A0DD915,
                0xB6636521, 0xE7B9F9B6, 0xFF34052E, 0xC5855664,
                0x53B02D5D, 0xA99F8FA1, 0x08BA4799, 0x6E85076A
            ),
            array(
                0x4B7A70E9, 0xB5B32944, 0xDB75092E, 0xC4192623,
                0xAD6EA6B0, 0x49A7DF7D, 0x9CEE60B8, 0x8FEDB266,
                0xECAA8C71, 0x699A17FF, 0x5664526C, 0xC2B19EE1,
                0x193602A5, 0x75094C29, 0xA0591340, 0xE4183A3E,
                0x3F54989A, 0x5B429D65, 0x6B8FE4D6, 0x99F73FD6,
                0xA1D29C07, 0xEFE830F5, 0x4D2D38E6, 0xF0255DC1,
                0x4CDD2086, 0x8470EB26, 0x6382E9C6, 0x021ECC5E,
                0x09686B3F, 0x3EBAEFC9, 0x3C971814, 0x6B6A70A1,
                0x687F3584, 0x52A0E286, 0xB79C5305, 0xAA500737,
                0x3E07841C, 0x7FDEAE5C, 0x8E7D44EC, 0x5716F2B8,
                0xB03ADA37, 0xF0500C0D, 0xF01C1F04, 0x0200B3FF,
                0xAE0CF51A, 0x3CB574B2, 0x25837A58, 0xDC0921BD,
                0xD19113F9, 0x7CA92FF6, 0x94324773, 0x22F54701,
                0x3AE5E581, 0x37C2DADC, 0xC8B57634, 0x9AF3DDA7,
                0xA9446146, 0x0FD0030E, 0xECC8C73E, 0xA4751E41,
                0xE238CD99, 0x3BEA0E2F, 0x3280BBA1, 0x183EB331,
                0x4E548B38, 0x4F6DB908, 0x6F420D03, 0xF60A04BF,
                0x2CB81290, 0x24977C79, 0x5679B072, 0xBCAF89AF,
                0xDE9A771F, 0xD9930810, 0xB38BAE12, 0xDCCF3F2E,
                0x5512721F, 0x2E6B7124, 0x501ADDE6, 0x9F84CD87,
                0x7A584718, 0x7408DA17, 0xBC9F9ABC, 0xE94B7D8C,
                0xEC7AEC3A, 0xDB851DFA, 0x63094366, 0xC464C3D2,
                0xEF1C1847, 0x3215D908, 0xDD433B37, 0x24C2BA16,
                0x12A14D43, 0x2A65C451, 0x50940002, 0x133AE4DD,
                0x71DFF89E, 0x10314E55, 0x81AC77D6, 0x5F11199B,
                0x043556F1, 0xD7A3C76B, 0x3C11183B, 0x5924A509,
                0xF28FE6ED, 0x97F1FBFA, 0x9EBABF2C, 0x1E153C6E,
                0x86E34570, 0xEAE96FB1, 0x860E5E0A, 0x5A3E2AB3,
                0x771FE71C, 0x4E3D06FA, 0x2965DCB9, 0x99E71D0F,
                0x803E89D6, 0x5266C825, 0x2E4CC978, 0x9C10B36A,
                0xC6150EBA, 0x94E2EA78, 0xA5FC3C53, 0x1E0A2DF4,
                0xF2F74EA7, 0x361D2B3D, 0x1939260F, 0x19C27960,
                0x5223A708, 0xF71312B6, 0xEBADFE6E, 0xEAC31F66,
                0xE3BC4595, 0xA67BC883, 0xB17F37D1, 0x018CFF28,
                0xC332DDEF, 0xBE6C5AA5, 0x65582185, 0x68AB9802,
                0xEECEA50F, 0xDB2F953B, 0x2AEF7DAD, 0x5B6E2F84,
                0x1521B628, 0x29076170, 0xECDD4775, 0x619F1510,
                0x13CCA830, 0xEB61BD96, 0x0334FE1E, 0xAA0363CF,
                0xB5735C90, 0x4C70A239, 0xD59E9E0B, 0xCBAADE14,
                0xEECC86BC, 0x60622CA7, 0x9CAB5CAB, 0xB2F3846E,
                0x648B1EAF, 0x19BDF0CA, 0xA02369B9, 0x655ABB50,
                0x40685A32, 0x3C2AB4B3, 0x319EE9D5, 0xC021B8F7,
                0x9B540B19, 0x875FA099, 0x95F7997E, 0x623D7DA8,
                0xF837889A, 0x97E32D77, 0x11ED935F, 0x16681281,
                0x0E358829, 0xC7E61FD6, 0x96DEDFA1, 0x7858BA99,
                0x57F584A5, 0x1B227263, 0x9B83C3FF, 0x1AC24696,
                0xCDB30AEB, 0x532E3054, 0x8FD948E4, 0x6DBC3128,
                0x58EBF2EF, 0x34C6FFEA, 0xFE28ED61, 0xEE7C3C73,
                0x5D4A14D9, 0xE864B7E3, 0x42105D14, 0x203E13E0,
                0x45EEE2B6, 0xA3AAABEA, 0xDB6C4F15, 0xFACB4FD0,
                0xC742F442, 0xEF6ABBB5, 0x654F3B1D, 0x41CD2105,
                0xD81E799E, 0x86854DC7, 0xE44B476A, 0x3D816250,
                0xCF62A1F2, 0x5B8D2646, 0xFC8883A0, 0xC1C7B6A3,
                0x7F1524C3, 0x69CB7492, 0x47848A0B, 0x5692B285,
                0x095BBF00, 0xAD19489D, 0x1462B174, 0x23820E00,
                0x58428D2A, 0x0C55F5EA, 0x1DADF43E, 0x233F7061,
                0x3372F092, 0x8D937E41, 0xD65FECF1, 0x6C223BDB,
                0x7CDE3759, 0xCBEE7460, 0x4085F2A7, 0xCE77326E,
                0xA6078084, 0x19F8509E, 0xE8EFD855, 0x61D99735,
                0xA969A7AA, 0xC50C06C2, 0x5A04ABFC, 0x800BCADC,
                0x9E447A2E, 0xC3453484, 0xFDD56705, 0x0E1E9EC9,
                0xDB73DBD3, 0x105588CD, 0x675FDA79, 0xE3674340,
                0xC5C43465, 0x713E38D8, 0x3D28F89E, 0xF16DFF20,
                0x153E21E7, 0x8FB03D4A, 0xE6E39F2B, 0xDB83ADF7
            ),
            array(
                0xE93D5A68, 0x948140F7, 0xF64C261C, 0x94692934,
                0x411520F7, 0x7602D4F7, 0xBCF46B2E, 0xD4A20068,
                0xD4082471, 0x3320F46A, 0x43B7D4B7, 0x500061AF,
                0x1E39F62E, 0x97244546, 0x14214F74, 0xBF8B8840,
                0x4D95FC1D, 0x96B591AF, 0x70F4DDD3, 0x66A02F45,
                0xBFBC09EC, 0x03BD9785, 0x7FAC6DD0, 0x31CB8504,
                0x96EB27B3, 0x55FD3941, 0xDA2547E6, 0xABCA0A9A,
                0x28507825, 0x530429F4, 0x0A2C86DA, 0xE9B66DFB,
                0x68DC1462, 0xD7486900, 0x680EC0A4, 0x27A18DEE,
                0x4F3FFEA2, 0xE887AD8C, 0xB58CE006, 0x7AF4D6B6,
                0xAACE1E7C, 0xD3375FEC, 0xCE78A399, 0x406B2A42,
                0x20FE9E35, 0xD9F385B9, 0xEE39D7AB, 0x3B124E8B,
                0x1DC9FAF7, 0x4B6D1856, 0x26A36631, 0xEAE397B2,
                0x3A6EFA74, 0xDD5B4332, 0x6841E7F7, 0xCA7820FB,
                0xFB0AF54E, 0xD8FEB397, 0x454056AC, 0xBA489527,
                0x55533A3A, 0x20838D87, 0xFE6BA9B7, 0xD096954B,
                0x55A867BC, 0xA1159A58, 0xCCA92963, 0x99E1DB33,
                0xA62A4A56, 0x3F3125F9, 0x5EF47E1C, 0x9029317C,
                0xFDF8E802, 0x04272F70, 0x80BB155C, 0x05282CE3,
                0x95C11548, 0xE4C66D22, 0x48C1133F, 0xC70F86DC,
                0x07F9C9EE, 0x41041F0F, 0x404779A4, 0x5D886E17,
                0x325F51EB, 0xD59BC0D1, 0xF2BCC18F, 0x41113564,
                0x257B7834, 0x602A9C60, 0xDFF8E8A3, 0x1F636C1B,
                0x0E12B4C2, 0x02E1329E, 0xAF664FD1, 0xCAD18115,
                0x6B2395E0, 0x333E92E1, 0x3B240B62, 0xEEBEB922,
                0x85B2A20E, 0xE6BA0D99, 0xDE720C8C, 0x2DA2F728,
                0xD0127845, 0x95B794FD, 0x647D0862, 0xE7CCF5F0,
                0x5449A36F, 0x877D48FA, 0xC39DFD27, 0xF33E8D1E,
                0x0A476341, 0x992EFF74, 0x3A6F6EAB, 0xF4F8FD37,
                0xA812DC60, 0xA1EBDDF8, 0x991BE14C, 0xDB6E6B0D,
                0xC67B5510, 0x6D672C37, 0x2765D43B, 0xDCD0E804,
                0xF1290DC7, 0xCC00FFA3, 0xB5390F92, 0x690FED0B,
                0x667B9FFB, 0xCEDB7D9C, 0xA091CF0B, 0xD9155EA3,
                0xBB132F88, 0x515BAD24, 0x7B9479BF, 0x763BD6EB,
                0x37392EB3, 0xCC115979, 0x8026E297, 0xF42E312D,
                0x6842ADA7, 0xC66A2B3B, 0x12754CCC, 0x782EF11C,
                0x6A124237, 0xB79251E7, 0x06A1BBE6, 0x4BFB6350,
                0x1A6B1018, 0x11CAEDFA, 0x3D25BDD8, 0xE2E1C3C9,
                0x44421659, 0x0A121386, 0xD90CEC6E, 0xD5ABEA2A,
                0x64AF674E, 0xDA86A85F, 0xBEBFE988, 0x64E4C3FE,
                0x9DBC8057, 0xF0F7C086, 0x60787BF8, 0x6003604D,
                0xD1FD8346, 0xF6381FB0, 0x7745AE04, 0xD736FCCC,
                0x83426B33, 0xF01EAB71, 0xB0804187, 0x3C005E5F,
                0x77A057BE, 0xBDE8AE24, 0x55464299, 0xBF582E61,
                0x4E58F48F, 0xF2DDFDA2, 0xF474EF38, 0x8789BDC2,
                0x5366F9C3, 0xC8B38E74, 0xB475F255, 0x46FCD9B9,
                0x7AEB2661, 0x8B1DDF84, 0x846A0E79, 0x915F95E2,
                0x466E598E, 0x20B45770, 0x8CD55591, 0xC902DE4C,
                0xB90BACE1, 0xBB8205D0, 0x11A86248, 0x7574A99E,
                0xB77F19B6, 0xE0A9DC09, 0x662D09A1, 0xC4324633,
                0xE85A1F02, 0x09F0BE8C, 0x4A99A025, 0x1D6EFE10,
                0x1AB93D1D, 0x0BA5A4DF, 0xA186F20F, 0x2868F169,
                0xDCB7DA83, 0x573906FE, 0xA1E2CE9B, 0x4FCD7F52,
                0x50115E01, 0xA70683FA, 0xA002B5C4, 0x0DE6D027,
                0x9AF88C27, 0x773F8641, 0xC3604C06, 0x61A806B5,
                0xF0177A28, 0xC0F586E0, 0x006058AA, 0x30DC7D62,
                0x11E69ED7, 0x2338EA63, 0x53C2DD94, 0xC2C21634,
                0xBBCBEE56, 0x90BCB6DE, 0xEBFC7DA1, 0xCE591D76,
                0x6F05E409, 0x4B7C0188, 0x39720A3D, 0x7C927C24,
                0x86E3725F, 0x724D9DB9, 0x1AC15BB4, 0xD39EB8FC,
                0xED545578, 0x08FCA5B5, 0xD83D7CD3, 0x4DAD0FC4,
                0x1E50EF5E, 0xB161E6F8, 0xA28514D9, 0x6C51133C,
                0x6FD5C7E7, 0x56E14EC4, 0x362ABFCE, 0xDDC6C837,
                0xD79A3234, 0x92638212, 0x670EFA8E, 0x406000E0
            ),
            array(
                0x3A39CE37, 0xD3FAF5CF, 0xABC27737, 0x5AC52D1B,
                0x5CB0679E, 0x4FA33742, 0xD3822740, 0x99BC9BBE,
                0xD5118E9D, 0xBF0F7315, 0xD62D1C7E, 0xC700C47B,
                0xB78C1B6B, 0x21A19045, 0xB26EB1BE, 0x6A366EB4,
                0x5748AB2F, 0xBC946E79, 0xC6A376D2, 0x6549C2C8,
                0x530FF8EE, 0x468DDE7D, 0xD5730A1D, 0x4CD04DC6,
                0x2939BBDB, 0xA9BA4650, 0xAC9526E8, 0xBE5EE304,
                0xA1FAD5F0, 0x6A2D519A, 0x63EF8CE2, 0x9A86EE22,
                0xC089C2B8, 0x43242EF6, 0xA51E03AA, 0x9CF2D0A4,
                0x83C061BA, 0x9BE96A4D, 0x8FE51550, 0xBA645BD6,
                0x2826A2F9, 0xA73A3AE1, 0x4BA99586, 0xEF5562E9,
                0xC72FEFD3, 0xF752F7DA, 0x3F046F69, 0x77FA0A59,
                0x80E4A915, 0x87B08601, 0x9B09E6AD, 0x3B3EE593,
                0xE990FD5A, 0x9E34D797, 0x2CF0B7D9, 0x022B8B51,
                0x96D5AC3A, 0x017DA67D, 0xD1CF3ED6, 0x7C7D2D28,
                0x1F9F25CF, 0xADF2B89B, 0x5AD6B472, 0x5A88F54C,
                0xE029AC71, 0xE019A5E6, 0x47B0ACFD, 0xED93FA9B,
                0xE8D3C48D, 0x283B57CC, 0xF8D56629, 0x79132E28,
                0x785F0191, 0xED756055, 0xF7960E44, 0xE3D35E8C,
                0x15056DD4, 0x88F46DBA, 0x03A16125, 0x0564F0BD,
                0xC3EB9E15, 0x3C9057A2, 0x97271AEC, 0xA93A072A,
                0x1B3F6D9B, 0x1E6321F5, 0xF59C66FB, 0x26DCF319,
                0x7533D928, 0xB155FDF5, 0x03563482, 0x8ABA3CBB,
                0x28517711, 0xC20AD9F8, 0xABCC5167, 0xCCAD925F,
                0x4DE81751, 0x3830DC8E, 0x379D5862, 0x9320F991,
                0xEA7A90C2, 0xFB3E7BCE, 0x5121CE64, 0x774FBE32,
                0xA8B6E37E, 0xC3293D46, 0x48DE5369, 0x6413E680,
                0xA2AE0810, 0xDD6DB224, 0x69852DFD, 0x09072166,
                0xB39A460A, 0x6445C0DD, 0x586CDECF, 0x1C20C8AE,
                0x5BBEF7DD, 0x1B588D40, 0xCCD2017F, 0x6BB4E3BB,
                0xDDA26A7E, 0x3A59FF45, 0x3E350A44, 0xBCB4CDD5,
                0x72EACEA8, 0xFA6484BB, 0x8D6612AE, 0xBF3C6F47,
                0xD29BE463, 0x542F5D9E, 0xAEC2771B, 0xF64E6370,
                0x740E0D8D, 0xE75B1357, 0xF8721671, 0xAF537D5D,
                0x4040CB08, 0x4EB4E2CC, 0x34D2466A, 0x0115AF84,
                0xE1B00428, 0x95983A1D, 0x06B89FB4, 0xCE6EA048,
                0x6F3F3B82, 0x3520AB82, 0x011A1D4B, 0x277227F8,
                0x611560B1, 0xE7933FDC, 0xBB3A792B, 0x344525BD,
                0xA08839E1, 0x51CE794B, 0x2F32C9B7, 0xA01FBAC9,
                0xE01CC87E, 0xBCC7D1F6, 0xCF0111C3, 0xA1E8AAC7,
                0x1A908749, 0xD44FBD9A, 0xD0DADECB, 0xD50ADA38,
                0x0339C32A, 0xC6913667, 0x8DF9317C, 0xE0B12B4F,
                0xF79E59B7, 0x43F5BB3A, 0xF2D519FF, 0x27D9459C,
                0xBF97222C, 0x15E6FC2A, 0x0F91FC71, 0x9B941525,
                0xFAE59361, 0xCEB69CEB, 0xC2A86459, 0x12BAA8D1,
                0xB6C1075E, 0xE3056A0C, 0x10D25065, 0xCB03A442,
                0xE0EC6E0E, 0x1698DB3B, 0x4C98A0BE, 0x3278E964,
                0x9F1F9532, 0xE0D392DF, 0xD3A0342B, 0x8971F21E,
                0x1B0A7441, 0x4BA3348C, 0xC5BE7120, 0xC37632D8,
                0xDF359F8D, 0x9B992F2E, 0xE60B6F47, 0x0FE3F11D,
                0xE54CDA54, 0x1EDAD891, 0xCE6279CF, 0xCD3E7E6F,
                0x1618B166, 0xFD2C1D05, 0x848FD2C5, 0xF6FB2299,
                0xF523F357, 0xA6327623, 0x93A83531, 0x56CCCD02,
                0xACF08162, 0x5A75EBB5, 0x6E163697, 0x88D273CC,
                0xDE966292, 0x81B949D0, 0x4C50901B, 0x71C65614,
                0xE6C6C7BD, 0x327A140A, 0x45E1D006, 0xC3F27B9A,
                0xC9AA53FD, 0x62A80F00, 0xBB25BFE2, 0x35BDD2F6,
                0x71126905, 0xB2040222, 0xB6CBCF7C, 0xCD769C2B,
                0x53113EC0, 0x1640E3D3, 0x38ABBD60, 0x2547ADF0,
                0xBA38209C, 0xF746CE76, 0x77AFA1C5, 0x20756060,
                0x85CBFE4E, 0x8AE88DD8, 0x7AAAF9B0, 0x4CF9AA7E,
                0x1948C25C, 0x02FB8A8C, 0x01C36AE4, 0xD6EBE1F9,
                0x90D4F869, 0xA65CDEA0, 0x3F09252D, 0xC208E69F,
                0xB74E6132, 0xCE77E25B, 0x578FDFE3, 0x3AC372E6
            )
        );
    }

}
