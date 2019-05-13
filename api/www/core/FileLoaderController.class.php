<?php
/*
  **  FILE LOADER CONTROLLER
  *
  *   This Controller needs Media table in MySQL to work fine.
  *
*/
class FileLoaderController {

  /*
    ** Member
  */
  public static $__FILES__;

  public static $maxsize = 10000000;                   // 10Mo
  public static $pathFile = 'documents/';            // default value
  public static $baseUrl = 'http://localhost:4000/';  // Default Url base
  public static $authorizedExtensions = ['jpg', 'jpeg', 'png', 'xls', 'docx', 'doc', 'csv', 'xlsx', 'pdf'];

  private static $extensionFile;

  /*
    ** Upload function that is accessible from external classes
  */
  public static function upload($media=null) {
    // Get the full path
    if (trim($_REQUEST['rename']??'') !== '') {
      $target_file = self::$pathFile . '/' . trim($_REQUEST['rename']).'.'.Utils::getExtFromMime(self::$__FILES__['type']);
    } else {
      $target_file = self::$pathFile . '/' . str_replace(' ', '_', basename(self::$__FILES__["name"]));
    }

    self::$extensionFile = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if (!is_null($media)) {
      if (trim($_REQUEST['rename']??'') !== '') {
        $media->setName(trim($_REQUEST['rename']).'.'.Utils::getExtFromMime(self::$__FILES__['type']));
      } else {
        $media->setName(str_replace(' ', '_', basename(self::$__FILES__["name"])));
      }
      $media->setUrl( self::$baseUrl . $target_file);
      $media->setPath($target_file);
    }
    // Check if image file is a actual image or fake image
    // if (!self::isRealImage())
    //   API::error("File is not an image.");
    // Check if size is oversize
    if (!self::checkSize())
      API::error("File too large for uploading. Max size: ".(self::$maxsize/1000000)." Mo");

    // Allow certain file formats
    if (!self::extensionIsAuthorized())
      API::error("Only ".implode(',',self::$authorizedExtensions). " are allowed.");

    // Does the path already exists ?
    if (!(is_dir(self::$pathFile)))
      self::createPath();   // he doesn't, so we create it

      if (move_uploaded_file(self::$__FILES__["tmp_name"], $target_file)) {
        if (!is_null($media)) {
          $media->save();
          chmod(__DIR__.'/../'.$media->getPath(), 0666);
          return true;
        }
      } else {
          return "An error occured while uploading " . $media->getName();
      }
  }

  /*
    ** Private method
  */
  /* Check MIME type of file */
  private static function isRealImage() {
    $check = getimagesize(self::$__FILES__["tmp_name"]);
    if($check !== false)
      return true;

    return false;
  }

  /* Check that file size not upper than Maxsize member */
  private static function checkSize() {
    if (!(self::$__FILES__["size"] > self::$maxsize))
      return true;

    return false;
  }
  /* Check that file extension is accepcted */
  private static function extensionIsAuthorized() {
    foreach (self::$authorizedExtensions as $extension) {
      if (self::$extensionFile === $extension)
        return true;
    }
    // no extension
    return false;
  }

  private static function createPath() {
    $newPath = './';
    $explodedPath = explode('/', self::$pathFile);
    foreach ($explodedPath as $pieceOfPath) {
      $newPath .= $pieceOfPath . '/';
      if (!is_dir($newPath)) {
        mkdir($newPath, 0700);
      }
    }
  }


  /*
    ** Simply access to getters and setters
  */
  public static function setFiles($files) {
    self::$__FILES__ = $files;
  }
  public static function setMaxsize($maxsize) {
    self::$maxsize = $maxsize;
  }
  public static function setPathFile($pathFile) {
    self::$pathFile = $pathFile;
  }
  public static function setBaseUrl($baseUrl) {
    self::$baseUrl = $baseUrl;
  }
  public static function setAuthorizedExtensions($authorizedExtensions) {
    self::$authorizedExtensions = $authorizedExtensions;
  }

  /*
    ** add an Authorized extension
    *
    * $extension can be either array or a simple string
  */
  public static function addAuthorizedExtension($extension) {
    if (is_array($extension))
      self::setAuthorizedExtensions(array_merge(self::$authorizedExtensions, $extension));
    else
      self::$authorizedExtensions[] = $extension;
  }

  /*
    ** Uploading API route: for 1 file only.
    *
    * data must looks like as follow:

      $_FILES['file']
      route : "/upload/:path*to*file/:rename"

      parameter 1: ':path*to*file'  [ optionel ]
      all * characters will be replace whith /

      parameter 2 : :rename *  [ optionel ]

  */
  public static function uploadFile() {

  }

}
