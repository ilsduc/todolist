<?php

class Autoloader {

  static function register() {
    spl_autoload_register(["Autoloader", "autoload"]);
  }

  static function autoload($class){
    $classPath = "core/".$class.".class.php";
    $classModel = "app/entity/".$class.".class.php";
    $classController = "app/controllers/".$class.".class.php";
    if(file_exists($classPath)){
      include $classPath;
    }else if(file_exists($classModel)){
      include $classModel;
    }else if(file_exists($classController)){
      include $classController;
    }
  }

}
