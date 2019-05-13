<?php
class UserController extends AppController {
  public static function register() {
    // Check for email and password
    if ( !isset($_REQUEST['email']) || !isset($_REQUEST['pwd']) )
      API::error('All fields are required');
    // Retrieve potential user

    $user = new User();
    $user->getOneByEmail($_REQUEST['email']);

    // eject if exists
    if ( $user->getId() )
      API::error('Email already exists.');
    // serialize from post data
    $user->hydrate($_REQUEST);
    // set password
    $user->setPwd(password_hash($_REQUEST['pwd'], PASSWORD_DEFAULT));
    // save in DB
    $user->save();
    // Controller
    return $user;
  }
  /*
    ** login
  */
  public static function login () {
    // Check that all field are filled
    if ( !isset($_REQUEST['email']) || !isset($_REQUEST['pwd']) )
      API::error('All fields are required');
    // retrieve a potential user
    $user = new User();
    $user->getOneByEmail($_REQUEST['email']);
    if (is_null($user->getId()))
      $user->getOneByUsername($_REQUEST['email']);
    // existence test
    if ( $user->getId() && password_verify($_REQUEST['pwd'], $user->getPwd()) ) {
      // create a token for the user
      $token = $user->generateToken();
      // store it in the session
      Session::store('user_token', $token);
      // dynamically set a user member called token
      $user->token = $token;
      // return user
      return $user;
    }
    API::error("Something went wrong with authentication.");
  }
  /*
    ** confirm method required a specific header
    **************************************
    ***     Key     ***       Value    ***
    **************************************
    ** Authorization    Bearer: <token> **
  */
  public static function confirm() {
    // retrieve token from Authorization header
    $token = Token::getBearerToken();
    // Compare with the token in session
    $valid = $token === Session::get('user_token');
    // is the token valid
    if ($valid && Token::isValid($token, Session::get('token_secret_key')))  {
      // get a refresh token
      $refreshToken = Token::refresh($token);
      // get payload
      $payload = Token::getPayload($token);
      // hydrate user
      $user = new User($payload['id']);
      // set the new token
      $user->token = $refreshToken;
      // Do the same for session
      Session::store('user_token', $refreshToken);
      //
      return $user;
    }

    die('Something went wrong with you fucking token.');
  }

  /*
    ** logout
  */
  public static function logout() {
    // Retrieve token from Header Authorization
    $token = Token::getBearerToken();
    // Check if it's valid to log out the session
    if ( Token::isValid($token, Session::get('token_secret_key')) ) {
      Session::destroy();
      API::send(200, [], "Successfully logged out.");
    }
    // Oups ..
    API::error("Something went wrong with loging out..");
  }

  public static function saveMedia() {
    // Retrieve the token
    $token = Session::get('user_token');
    // Retrieve the payload
    $payload = Token::getPayload($token);
    //
    $user = new User($payload['id']);
    $media = new Media($_REQUEST['id_media']);
    //
    if ( $user->getId() ) {
      if ( $_REQUEST['id_media'] ) {
        $user->setId_media($_REQUEST['id_media']);
        $user->save();
        return new Media($_REQUEST['id_media']);
      }
    }
  }
}
