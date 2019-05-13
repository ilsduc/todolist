<?php
class UserController {

    /*
      ** User authentification
    */
    public static function login() {
      if (empty($_POST['email']) || empty($_POST['pwd']))
        API::error("All fields are required");
      /*
        **
      */
      $user->getOneBy(['email' => $_POST['email']]);

      if (isset($user->id)) {
        // TODO: check for password
      }
      $user->generateToken();
    }
    /*
      ** User registration
    */
    public static function register() {
      if (empty($_POST['email']) || empty($_POST['pwd']))
        $user = new User();
        $user->serialize($_POST);
        $user->save();
        return $user;
      /*
        **
      */
    }



}
