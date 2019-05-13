<?php
class SuperModel extends BaseSQL {
    public function __construct($exclude=null){
        parent::__construct();
        $this->getDynamicObject($exclude);
    }

    function __call($m,$p) {
        $v = strtolower(substr($m,3));
        if (!strncasecmp($m,'get',3)) return $this->$v;
        if (!strncasecmp($m,'set',3)) $this->$v = $p[0];
    }

    /*
      ** retrieve object from sql description
    */
    public function getDynamicObject($exclude=null) {
      /*
        ** define setter fonction
      */
      $setter = 'set'.ucfirst($value['Field']);
      /*
        ** get sql description of given class
      */
      $stmt = "desc ".$this->table." ;";
      $req = $this->pdo->prepare($stmt);
      $req->execute();
      $res = $req->fetchAll(PDO::FETCH_ASSOC);

      /*
        ** mapping
      */
      foreach ($res as $key => $value) {
        /*
          ** gesture of $exclude property
        */
        if (!is_null($exclude))
          if (in_array($value['Field'], $exclude)
            continue;
        /*
          let's give object, mapped property
        */
        $this->$setter(null);
      }
    }

  public function getOneBy ($by, $object=false) {
    $arr = parent::getOneBy($by);
    if(!$obj)
      return $arr;
    else
      $this->serialize(Utils::MinimizeKeys($arr));
  }

  public function buildSQLScript() {
    $className = strtolower(get_called_class());
    $sqlFile = '../models/'.$className.'/'.$className.'.sql';
  }

}
