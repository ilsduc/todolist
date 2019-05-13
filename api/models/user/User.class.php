<?php
class User extends SuperModel {

    public function __construct($exclude=null){
        parent::__construct($exclude);
    }

    public function generateToken() {
      $token = new Token();
      $this->setToken($token->id);
      $this->save();
    }
}
