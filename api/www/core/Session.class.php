<?php
/*
  ** Dialog with $_SESSION
*/
class Session {
  /*
    ** initialize session
  */
  public static function init() {
    /*
      ** is there already a session?
    */
    if (session_status() == PHP_SESSION_NONE)
      session_start();
  }
  /*
    ** store an item with a key
  */
  public static function store($key, $item) {
    /*
      ** store array
    */
    $_SESSION[$key]= $item;
  }
  /*
    ** get an item with a key
  */
  public static function get($key) {
    /*
      ** get arr
    */
    if (!empty($_SESSION[$key]))
      return $_SESSION[$key];
  }
  /*
  ** Unset a value in the session
  */
  public static function unset($key) {
    if (isset($_SESSION[$key]))
      unset($_SESSION[$key]);
  }
  /*
    ** Definely destroy the current session
  */
  public static function destroy() {
    if (session_status() !== PHP_SESSION_NONE)
      session_destroy();
  }
}
