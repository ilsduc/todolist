<?php

class Config  {

  static $driver;
  static $host;
  static $port="3306";
  static $dbname;
  static $user;
  static $pwd;

  public static function init($app) {
    /* Set the Database Configuration from the app */
    self::$driver  = $app['driver'];
    self::$host    = $app['host'];
    self::$port    = isset($app['port']) ? $app['port'] : null;
    self::$dbname  = $app['dbname'];
    self::$user    = $app['user'];
    self::$pwd     = $app['pwd'];
    define('URL_GED', $app['gedUrl']);


    /* Does the data must be Crypted ?*/
    if (isset($app['options']['crypted']) && $app['options']['crypted'] === true)
      define('__CRYPTED__', true);
    else
      define('__CRYPTED__', false);

    /* Put the owner App in session
     *
     *	[ owner => [ 'name' => 'owner_name', 'value' => 'owner_code' ] ]
     *
    */
    if (!isset($_SESSION['owner']['name']))
      $_SESSION['owner']['name'] = $app['appOwner'];
  }
}
