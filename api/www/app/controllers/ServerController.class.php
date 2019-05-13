<?php

class ServerController extends AppController {
  public function documentation() {

    $documentation = new APIDocumentation();
    $result = $documentation->generate();

    // Data
    $data = [
      'data' => [
        'documentation' => $result,
      ],
      'templateData' => [
        'js' => 'doc.ajax.js'
      ]
    ];

    $this->render('documentation', 'back', $data);

  }

  public function changeToken() {
    Session::store('api_doc_token', $_POST['api_doc_token']??"");
    header('Location: /documentation');
    exit();
  }

  public function createSchema() {
    $query = file_get_contents('./sql/api.sql');
    $db = new DB();
    $db->execFile($query);
    header('Location: /documentation');
    exit();
  }

}
