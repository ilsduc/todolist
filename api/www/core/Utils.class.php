<?php
class Utils {

  /* Method that returns the value depending on the crypted param app*/
  public static function getCryptedContextValue($value) {
    if(__CRYPTED__)
      return md5($value);

    return $value;
  }

  /* Method that returns the value depending on the crypted param app to inject into MySQL Request */
  public static function getMySQLCryptedContextValue($value) {
    if(__CRYPTED__)
      return 'md5('.$value.')';

    return $value;
  }

  /* Method that returns the data with and _ID_ for each row which is crypted in MD5 hash */
  public static function getCryptedContextData($data, $keyToCrypt) {
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        // $value = self::getCryptedContextData($value, $keyToCrypt);
        foreach ($value as $key1 => $value1) {
          // var_dump($key1, $value1);
          if (strcasecmp($key1, $keyToCrypt) === 0) {
            $value['_ID_'] = __CRYPTED__ ? md5($value1) : $value1;
          }
        }
        $data[$key] = $value;
      } else {
        if (strcasecmp($key, $keyToCrypt) === 0) {
          $data['_id_'] = __CRYPTED__ ? md5($value) : $value;
        }
      }
    }
    return $data;
  }

  public static function create_hash() {
      return substr(sha1(date("h:i:sa").rand(0,10000).'g°rgù:e'), 12, 16);
  }

  public static function minimizeKeys($array) {
    if (empty($array))
      return [];
    $newTab = [];
    foreach ($array as $key => $value) {
      if(is_array($array[$key]))
        $newTab[strtolower($key)] = self::MinimizeKeys($array[$key]);
      else
        $newTab[strtolower($key)] = $value;
    }
    return  $newTab;
  }
}
