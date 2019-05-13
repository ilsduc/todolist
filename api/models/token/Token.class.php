<?php
/*
  ** token gesture class [ Find it in models ]
*/
class Token extends BaseSQL {

  /*
    ** parameters
  */
  public $id;
  public $valid_days_length; // acceptes int parameters as "days"

  /*
    ** constructor
  */
  public function __construct($id=null) {
    /* fired parent __construct method */
    parent::__construct();
    /* what within $id */
    if (!is_null($id))
      $this->getOneBy(['id' => $id]);
    else
      $this->id = substr(sha1(time().'435ew(='),rand(0, 15), 16);
  }

}
