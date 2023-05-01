<?php

class Jwt
{

    public function __construct()
    {
        session_start();
    }

    public $leeway = 0;
    public $token = null;

    /**
     * Allow the current timestamp to be specified.
     * Useful for fixing a value within unit testing.
     *
     * Will default to PHP time() value if null.
     */
    public $timestamp = null;

    public $supported_algs = array(
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'HS384' => array('hash_hmac', 'SHA384'),
        'RS256' => array('openssl', 'SHA256'),
        'RS384' => array('openssl', 'SHA384'),
        'RS512' => array('openssl', 'SHA512'),
    );

    public function generateToken($payload){
        $payload["time"] = time();
        $_SESSION["token"] = $this->encode($payload);
    }

    public function destroy(){
        session_destroy();
    }

    public function getUserId($jwt = null, $key="36HGtd13fsLk551AX2qfg4fp==")
    {
        if(!$jwt){
            $jwt = $this->getToken();
        }
        if(!$jwt){
            return -1;
        }

        $this->token = $jwt;
        $verifyData = $this->decode($jwt);

        if($verifyData == NULL)
            return -1;
        else if($verifyData->time + LOGIN_EXPIRE_TIME < time())
            return -1;
        else
            return $verifyData->userId;
    }


    public function getExpireTime($jwt = null)
    {
        if(!$jwt){
            $jwt = $this->getToken();
        }
        if(!$jwt){
            return -1;
        }

        $this->token = $jwt;
        $verifyData = $this->decode($jwt);

        if($verifyData == NULL)
            return -1;
        else if($verifyData->time + LOGIN_EXPIRE_TIME < time())
            return -1;
        else
            return LOGIN_EXPIRE_TIME - (time() - $verifyData->time) ;
    }

    private function getToken(){
        return $_SESSION["token"] ?? null;
    }


    /**
     * Decodes a Jwt string into a PHP object.
     *
     * @param string        $jwt            The Jwt
     * @param string|array  $key            The key, or map of keys.
     *                                      If the algorithm used is asymmetric, this is the public key
     * @param array         $allowed_algs   List of supported verification algorithms
     *                                      Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     *
     * @return object The Jwt's payload as a PHP object
     *
     * @throws UnexpectedValueException     Provided Jwt was invalid
     * @throws SignatureInvalidException    Provided Jwt was invalid because the signature verification failed
     * @throws BeforeValidException         Provided Jwt is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException         Provided Jwt is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException             Provided Jwt has since expired, as defined by the 'exp' claim
     *
     * @uses jsonDecode
     * @uses urlsafeB64Decode
     */
    public function decode($jwt, $key="36HGtd13fsLk551AX2qfg4fp==", array $allowed_algs = array('HS384'))
    {
        $timestamp = is_null($this->timestamp) ? time() : $this->timestamp;

        if (empty($key)) {
            throw new InvalidArgumentException('Key may not be empty');
        }
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
        	return null;
            //throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = $this->jsonDecode($this->urlsafeB64Decode($headb64)))) {
			return null;
			//throw new UnexpectedValueException('Invalid header encoding');
        }
        if (null === $payload = $this->jsonDecode($this->urlsafeB64Decode($bodyb64))) {
			return null;
			//throw new UnexpectedValueException('Invalid claims encoding');
        }
        if (false === ($sig = $this->urlsafeB64Decode($cryptob64))) {
			return null;
			//throw new UnexpectedValueException('Invalid signature encoding');
        }
        if (empty($header->alg)) {
			return null;
			//throw new UnexpectedValueException('Empty algorithm');
        }
        if (empty($this->supported_algs[$header->alg])) {
			return null;
			//throw new UnexpectedValueException('Algorithm not supported');
        }
        if (!in_array($header->alg, $allowed_algs)) {
			return null;
			//throw new UnexpectedValueException('Algorithm not allowed');
        }
        if (is_array($key) || $key instanceof \ArrayAccess) {
            if (isset($header->kid)) {
                if (!isset($key[$header->kid])) {
					return null;
					//throw new UnexpectedValueException('"kid" invalid, unable to lookup correct key');
                }
                $key = $key[$header->kid];
            } else {
				return null;
				//throw new UnexpectedValueException('"kid" empty, unable to lookup correct key');
            }
        }

        // Check the signature
        if (!$this->verify("$headb64.$bodyb64", $sig, $key, $header->alg)) {
			return null;
			//throw new SignatureInvalidException('Signature verification failed');
        }

        // Check if the nbf if it is defined. This is the time that the
        // token can actually be used. If it's not yet that time, abort.
        if (isset($payload->nbf) && $payload->nbf > ($timestamp + $this->leeway)) {
			return null;
			//throw new BeforeValidException('Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->nbf));
        }

        // Check that this token has been created before 'now'. This prevents
        // using tokens that have been created for later use (and haven't
        // correctly used the nbf claim).
        if (isset($payload->iat) && $payload->iat > ($timestamp + $this->leeway)) {
			return null;
			//throw new BeforeValidException('Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->iat));
        }

        // Check if this token has expired.
        if (isset($payload->exp) && ($timestamp - $this->leeway) >= $payload->exp) {
			return null;
			//throw new ExpiredException('Expired token');
        }

        return $payload;
    }

    /**
     * Converts and signs a PHP object or array into a Jwt string.
     *
     * @param object|array  $payload    PHP object or array
     * @param string        $key        The secret key.
     *                                  If the algorithm used is asymmetric, this is the private key
     * @param string        $alg        The signing algorithm.
     *                                  Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     * @param mixed         $keyId
     * @param array         $head       An array with header elements to attach
     *
     * @return string A signed Jwt
     *
     * @uses jsonEncode
     * @uses urlsafeB64Encode
     */
    private function encode($payload, $key = "36HGtd13fsLk551AX2qfg4fp==", $alg = 'HS384', $keyId = null, $head = null)
    {
        $header = array('typ' => 'Jwt', 'alg' => $alg);
        if ($keyId !== null) {
            $header['kid'] = $keyId;
        }
        if ( isset($head) && is_array($head) ) {
            $header = array_merge($head, $header);
        }
        $segments = array();
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
        $signing_input = implode('.', $segments);

        $signature = $this->sign($signing_input, $key, $alg);
        $segments[] = $this->urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    /**
     * Sign a string with a given key and algorithm.
     *
     * @param string            $msg    The message to sign
     * @param string|resource   $key    The secret key
     * @param string            $alg    The signing algorithm.
     *                                  Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
     *
     * @return string An encrypted message
     *
     * @throws DomainException Unsupported algorithm was specified
     */
    private function sign($msg, $key, $alg = 'HS256')
    {
        if (empty($this->supported_algs[$alg])) {
            throw new DomainException('Algorithm not supported');
        }
        list($function, $algorithm) = $this->supported_algs[$alg];
        switch($function) {
            case 'hash_hmac':
                return hash_hmac($algorithm, $msg, $key, true);
            case 'openssl':
                $signature = '';
                $success = openssl_sign($msg, $signature, $key, $algorithm);
                if (!$success) {
                    throw new DomainException("OpenSSL unable to sign data");
                } else {
                    return $signature;
                }
        }
    }

    /**
     * Verify a signature with the message, key and method. Not all methods
     * are symmetric, so we must have a separate verify and sign method.
     *
     * @param string            $msg        The original message (header and body)
     * @param string            $signature  The original signature
     * @param string|resource   $key        For HS*, a string key works. for RS*, must be a resource of an openssl public key
     * @param string            $alg        The algorithm
     *
     * @return bool
     *
     * @throws DomainException Invalid Algorithm or OpenSSL failure
     */
    private function verify($msg, $signature, $key, $alg='HS256')
    {
        if (empty($this->supported_algs[$alg])) {
            throw new DomainException('Algorithm not supported');
        }

        list($function, $algorithm) = $this->supported_algs[$alg];
        switch($function) {
            case 'openssl':
                $success = openssl_verify($msg, $signature, $key, $algorithm);
                if ($success === 1) {
                    return true;
                } elseif ($success === 0) {
                    return false;
                }
                // returns 1 on success, 0 on failure, -1 on error.
                throw new DomainException(
                    'OpenSSL error: ' . openssl_error_string()
                );
            case 'hash_hmac':
            default:
                $hash = hash_hmac($algorithm, $msg, $key, true);
                if (function_exists('hash_equals')) {
                    return hash_equals($signature, $hash);
                }
                $len = min($this->safeStrlen($signature), $this->safeStrlen($hash));

                $status = 0;
                for ($i = 0; $i < $len; $i++) {
                    $status |= (ord($signature[$i]) ^ ord($hash[$i]));
                }
                $status |= ($this->safeStrlen($signature) ^ $this->safeStrlen($hash));

                return ($status === 0);
        }
    }

    /**
     * Decode a JSON string into a PHP object.
     *
     * @param string $input JSON string
     *
     * @return object Object representation of JSON string
     *
     * @throws DomainException Provided string was invalid JSON
     */
    private function jsonDecode($input)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            /** In PHP >=5.4.0, json_decode() accepts an options parameter, that allows you
             * to specify that large ints (like Steam Transaction IDs) should be treated as
             * strings, rather than the PHP default behaviour of converting them to floats.
             */
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            /** Not all servers will support that, however, so for older versions we must
             * manually detect large ints in the JSON string and quote them (thus converting
             *them to strings) before decoding, hence the preg_replace() call.
             */
            $max_int_length = strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }

        if (function_exists('json_last_error') && $errno = json_last_error()) {
            return null;
            //$this->handleJsonError($errno);
        } elseif ($obj === null && $input !== 'null') {
            return null;
            //throw new DomainException('Null result with non-null input');
        }
        return $obj;
    }

    /**
     * Encode a PHP object into a JSON string.
     *
     * @param object|array $input A PHP object or array
     *
     * @return string JSON representation of the PHP object or array
     *
     * @throws DomainException Provided object could not be encoded to valid JSON
     */
    private function jsonEncode($input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            $this->handleJsonError($errno);
        } elseif ($json === 'null' && $input !== null) {
            throw new DomainException('Null result with non-null input');
        }
        return $json;
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string A decoded string
     */
    private function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input The string you want encoded
     *
     * @return string The base64 encode of what you passed in
     */
    private function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * Helper method to create a JSON error.
     *
     * @param int $errno An error number from json_last_error()
     *
     * @return void
     */
    private function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters' //PHP >= 5.3.3
        );
        throw new DomainException(
            isset($messages[$errno])
            ? $messages[$errno]
            : 'Unknown JSON error: ' . $errno
        );
    }

    /**
     * Get the number of bytes in cryptographic strings.
     *
     * @param string
     *
     * @return int
     */
    private function safeStrlen($str)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, '8bit');
        }
        return strlen($str);
    }
}
