<?php
class API {

  public static function send($status, $datas=[], $msg=null) {
    $response['status'] = [];

    switch ($status) {
      case 200 :   // All is ok
        $msg = "OK";
        // $response['occurrences'] = count($datas);
        $response['data'] = $datas;
        break;
      case 401 :  // Non-Authentified User
        $msg = isset($msg) ? $msg : "Vous n'avez pas l'autorisation pour acceder à cette ressource";
        break;
      case 403 :  // Access Refused
        $msg = isset($msg) ? $msg : "Accès refusé";
        break;
      case 404 :  // NotFound
        $msg = isset($msg) ? $msg : "Ressource non trouvée ou inexistante";
        break;
    }
    // Format the response
    $response['status']['code'] = $status;
    $response['status']['msg'] = $msg;


    // set the response's header
    http_response_code($status);
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    /* Send the response with the following json format */
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    die();
  }

  public static function error($error=null) {
      http_response_code(404);
    if (is_null($error) || is_string($error))
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
    else
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    die();
  }

  public static function datamiss() {
    API::error('Some data is missing');
  }
}
