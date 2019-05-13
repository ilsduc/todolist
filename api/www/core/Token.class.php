<?php
/*
  ** Token class for generating and validation JsonWebToken
*/
class Token {
  /*
    ** Create the JWT
  */
  public static $alg = 'sha256';
  public static $not_before = 0; // not before (time)
  public static $expires = 30; //

  public static function create($payload, $secret=null) {
    // set secret key
    if ( is_null($secret) ) {
        // create random secret key
        $secret = 'My_64_Secret_16_Key' . date('Y:m:d:h:i:s:u') .rand(1,100000);
        // let's put it in session
        Session::store('token_secret_key', $secret);
    }
    /*
      give payload some default information
    */
    $defaultpayload = [
      'iat' => time(),
      'nbf' => self::$not_before,
      'exp' => self::$expires,
      'is_first' => true
    ];
    $newpayload = array_merge($payload, $defaultpayload);
    /*
      ** Header configs for JWT
    */
    $header = ["typ" => "JWT", "alg" => self::$alg];
    /*
      ** Encode in BASE64url the header JSONstring
    */
    $UrlEncodedHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    /*
      ** Encode in BASE64url the payload JSON string
    */
    $UrlEncodedPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($newpayload)));
    /*
      ** create the signature
    */
    $signatureEncoded = hash_hmac(self::$alg, $UrlEncodedHeader.$UrlEncodedPayload, $secret);
    /*
      ** Return JWT
    */
    $jwt = $UrlEncodedHeader . '.' . $UrlEncodedPayload . '.' . $signatureEncoded;
    // store in Session
    return $jwt;
  }
  /*
    ** Valid the JWT
  */
  public static function isValid($token, $secret) {
    /*
      **
    */
    $isValid = false;
    /*
      ** explode the token into 3 parts
    */
    $explodedToken = explode('.', $token);
    /*
      ** extract header information
    */
    $header = self::getHeader($token);
    /*
      ** extract alg used from header
    */
    if (!key_exists('alg', $header))
      $isValid = false; // header is unlis
    /*
      ** if signature match, so $token = true
    */
    $signature = hash_hmac($header['alg'], $explodedToken[0].$explodedToken[1], $secret);
    if ($signature === $explodedToken[2])
      $isValid = true;

    $payload = self::getPayload($token);
    /*
      verify expiration date
    */
    if (time() > ($payload['iat'] + $payload['exp']))
      $isValid = false;
    return $isValid;
    // TODO: add expires date and control it
  }
  /*
    ** get payload from a given JWT
  */
  public static function getPayload($token) {
    $explodedToken = explode('.', $token);
    $decodedPayload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $explodedToken[1])));
    return (array) $decodedPayload;
  }
  /*
    ** get Header from a given JWT
  */
  public static function getHeader($token) {
    $explodedToken = explode('.', $token);
    $decodedHeader = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $explodedToken[0])));
    return (array) $decodedHeader;
  }
  /*
    ** Retrieve the token from Authorization header
  */
  public static function getBearerToken() {
    $headers = apache_request_headers();
    $authorization = $headers['Authorization']??null;
    if (!$authorization)
      return null;
    $explodedHeader = explode(' ', $authorization);
    $token = $explodedHeader[1];
    return $token;
  }
  /*
    ** Refresh the token with the current payload
  */
  public static function refresh($token) {
    // retrieve the payload from give token
    $payload = self::getPayload($token);
    // create a new one with the payload
    return self::create((array) $payload);
  }
}
