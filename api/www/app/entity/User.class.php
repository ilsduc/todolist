<?php
class User extends Entity {

  public function generateToken() {
    // create payload for token
    $payload = [
      'id' => $this->getId(),
      'firstname' => $this->getFirstname(),
      'lastname' => $this->getLastname(),
      'email' => $this->getEmail(),
    ];
    // Token expires in 7 days
    Token::$expires = 60 * 60 * 24 * 7; // seconds
    // create and return the token
    return Token::create($payload);
  }
}
