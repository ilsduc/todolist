<?php

class Chatbot {

  public $response;

  public function ChatbotM () {
    $this->reponse['messages'] = [];
  }

  public function addText($text) {
    $this->response['messages'][] = ['text' => $text];
  }

  public function answer() {
    // set headers
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    // send response
    echo json_encode($this->response, JSON_UNESCAPED_UNICODE);
    exit;
  }

}
