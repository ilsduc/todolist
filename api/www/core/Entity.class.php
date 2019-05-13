<?php
/*
  ** Model mapper for MySQL
*/
class Entity extends DB {
    /*
      **
    */
    public function __construct($id=null){
      // call parent construct to instanciate a DB
      parent::__construct();
      // hydrate object with null values
      $this->getDynamicObject();
      // hydrate with a given id
      if (!is_null($id)) { $this->getOneById($id); }
    }
    /*
      ** Magic function called at every method call doesnt match existing method
    */
    function __call($m,$p) {
      /*
        ** GetOneBy Match
      */
      if (preg_match('#^getOneBy#', $m)) {
        /*
          ** Retrieve column name */
        $prop = lcfirst(str_replace('getOneBy', '', $m));
        /*
          ** Call getOneBy whith the column name
        */
        $this->getOneBy([$prop=>$p[0]]);
        /* break */
        return;
      }
      if (preg_match('#^getOneBy[A-Za-z]Array$#', $m)) {
        /*
          ** Retrieve column name */
        $prop = lcfirst(str_replace(['getOneBy', 'Array'], '', $m));
        /*
          ** Call getOneBy whith the column name */
        return $this->getOneByArray([$prop=>$p[0]]);
      }
      /*
        ** getAllBy Match
      */
      if (preg_match('#^getAllBy#', $m)) {
          /*
            ** Retrieve column name */
          $prop = lcfirst(str_replace('getAllBy', '', $m));
          /*
            ** Call getOneBy whith the column name
          */
          return $this->getAllBy([$prop=>$p[0]]);
      }
      /*
        **
      */
      if (preg_match('#Like$#', $m)) {
          /*
            ** Retrieve column name */
          $prop = lcfirst(str_replace('Like', '', $m));
          /*
            ** Call getOneBy whith the column name
          */
          $this->like([$prop=>$p[0]], $p[1]??true);
          /* break */
          return;
      }
      /*
        ** simple getter or setter
      */
      if (preg_match('#get#', $m) || preg_match('#set#', $m)) {
        /*
          ** getting variable name */
        $attr = strtolower(substr($m,3));
        /*
          ** getter */
        if (!strncasecmp($m,'get',3))
          return $this->$attr;
        /*
          ** setter */
        if (!strncasecmp($m,'set',3))
          $this->$attr = $p[0];

        return;
      }
      /*
        ** dynamic form
      */
      if (preg_match('#getForm#', $m) || preg_match('#set#', $m)) {
        /*
          ** Calling getDynamicForm */
        $this->getDynamicForm();
      }
    }
    // find method
    public function find($id = null) {
      if (!is_null($id)) {
        $this->getOneById($id);
        return $this;
      }
      return $this->getAll();
    }

    // get dynamic object
    private function getDynamicObject() {
      $stmt = "desc ".$this->table." ;";
      $req = $this->pdo->prepare($stmt);
      $req->execute();
      $res = $req->fetchAll(PDO::FETCH_ASSOC);
      foreach ($res as $key => $value) {
        $property = strtolower($value['Field']);
        $this->$property = null;
      }
    }
    /*
      @Override
        from BaseSQL
    */
    public function getOneBy ( $by ) {
      /*
        ** Getting an array from original method
      */
      $arr = parent::getOneBy($by);
      /*
        ** Specific stuff
      */
      $this->hydrate($arr);

      return $this;
    }

    // serialize the object
  	private function serializeSM($post) {
      // return if empty
      if (empty($post))
        return [];
      // loop through array
  		foreach ($post as $key => $value) {
  			$setter = 'set'.ucfirst(strtolower($key));
				$this->$setter($value);
  		}
  	}

    // hydrate method
    public function hydrate($tab) {
      // return if empty
      if (empty($tab))
        return [];
      // loop through array
  		foreach ($tab as $key => $value) {
  			$setter = 'set'.ucfirst(strtolower($key));
        if ( property_exists($this, strtolower($key)) )
  				{ $this->$setter($value); }
  		}
  	}

    /*
      ** CRUD METHOD
    */
    public function post() {
      // retrieve constraints
      $schema = new Schema(get_called_class());
      $requiredFields = $schema->getRequiredFields();
      // hydrate object
      $this->hydrate($_REQUEST);
      if ($this->existsWith($schema->getUniqueColumns()))
        { API::send(403, [], "Some values are already exists. Please check ". implode(', ', $schema->getUniqueColumns()) ."."); }
      // check if all value required are filled
      if (!$this->hasAll($requiredFields))
        { API::send(403, [],  "Data are missing. Please refere to the documentation."); }
      // check if $keycolumn is filled
      $keyColumn = $schema->getKeyColumn();
      if ($this->$keyColumn)
        { API:: send(403, [], "Are you sure to do things right?"); }
      // save the user
      $this->save();
      // return it
      return $this->getOneById($this->id);
    }

    public function put() {
      // retrieve constraints
      $schema = new Schema(get_called_class());
      $keyColumn = $schema->getKeyColumn();
      $requiredFields = $schema->getRequiredFields();
      // hydrate object
      $getter = 'getOneBy'.ucfirst($keyColumn);
      $this->$getter($_REQUEST[$keyColumn]??null);
      //  check if given keyColumn matches with the model
      if (!$this->$keyColumn)
        { API::send(403, [], "No ".$this->table." found. Please check the given key."); }
      // hydrate object
      $this->hydrate($_REQUEST);
      // Check for empty value
      if (!empty($this->getMissingFields()))
        { API::send(403, [], "Some required fields are missing: ". implode(', ', $this->getMissingFields())); }
      // save the user
      $this->save();
      // return it
      return $this;
    }
    
    // Delete method
    public function delete() {
      $schema = new Schema(get_called_class());
      $keyColumn = $schema->getKeyColumn();
      if (!$this->$keyColumn)
        API::send(403, [], 'Nothing to delete');
      $stmt = 'delete from '.$this->table.' where id=:id';
      $q = $this->pdo->prepare($stmt);
      $hasSuccessed = $q->execute(['id' => $this->id]);
      return $this;
    }

    /*
      ** Control methods
    */
    // verify that and hydrate object has ALL specified fields
    public function hasAll(array $fields) {
      foreach ($fields as $field) {
        if (!isset($this->$field))
          { return false; }
        if (empty($this->$field))
          { return false; }
      }
      return true;
    }
    // verify that and hydrate object has the specified field
    public function has(string $field) {
      if (!isset($this->$field))
        { return false; }
      if (empty($this->$field))
        { return false; }
      return true;
    }
    // verify if an object exists in Database with specified fields and associated values
    public function existsWith($arr) {
      $sqlWhere = [];
      $data = [];
      foreach ($arr as $value) {
        if ($this->$value !== null) {
          $sqlWhere[] = $value.'=?';
          $data[] = $this->$value;
        }
      }
      if (empty($sqlWhere) || empty($data))
        { return false; }
      $stmt = 'select * from '.$this->table.' where '.implode(' or ', $sqlWhere);
      $q = $this->pdo->prepare($stmt);
      $q->execute($data);
      return $q->fetch();
    }
    // Return an array that contains Required that are not fill
    public function getMissingFields() {
      $schema = new Schema(get_called_class());
      $requiredFields = $schema->getRequiredFields();
      $missingFields = [];
      foreach ($requiredFields as $field) {
        if (!$this->has($field))
          $missingFields[] = $field;
      }
      return $missingFields;
    }

    /*
      ** Relations methods
    */
    // get the property for relation hasOne type
    public function hasOne($table) {
      $class = ucfirst($table);
      $object = new $class;
      $property = strtolower($table);
      $idRelation = 'id_'.strtolower($table);
      $object->getOneById($this->$idRelation);
      $this->$property = $object;
      return $this;
    }
    // get array for relation hasMany type
    public function hasMany($table) {
      $class = ucfirst($table);
      $object = new $class;
      $property = strtolower($table).'s';
      $method = "getAllById_".(strtolower(get_called_class()));
      $this->$property = $object->$method($this->id);
      return $this;
    }
}
