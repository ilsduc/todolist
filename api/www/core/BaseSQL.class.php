<?php
class BaseSQL {

	protected $pdo;
	protected $table;

	public function __construct($app=[]){
		if($app !== []) {
			$DBconfs = yaml_parse_file("conf.yml");
			if(isset($DBconfs[$app]))
				Config::init($DBconfs[$app]);
		}
		try{
			$this->pdo = new PDO(Config::$driver.":host=".Config::$host.";dbname=".Config::$dbname.";charset=UTF8",Config::$user,Config::$pwd);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}catch(Exception $e){
			die("Erreur SQL : ".$e->getMessage());
		}

		$this->table = lcfirst(get_called_class());
	}

	public function serialize($post) {
		$class = get_called_class();

		foreach ($post as $key => $value) {
			$setter = 'set'.ucfirst($key);
			if(property_exists($class, $key))
				$this->$setter($value);
		}
	}

	public function save(){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$fields = [];
		$injValues = [];
		$datas = [];
		$updateValues = [];

		foreach ($dataChild as $field => $value) {
			$fields[] = $field;
			$injValues[] = ':'.self::clean($field);
			$datas[':'.self::clean($field)] = $value;
			$updateValues[] = $field.'='.':'.self::clean($field);
		}

		$id = $dataChild['id']??null;
		if(is_null($id)){
			// INSERT
			$strFields = implode(',', $fields);
			$strInjValues = implode(',', $injValues);
			$this->setId(Utils::create_hash());

			$stmt = "insert into ".$this->table." (".$strFields.") values (".$strInjValues.")";
			$req = $this->pdo->prepare($stmt);
			$req->execute($datas);


		}else{
			//UPDATE
			$strUpdate = implode(',', $updateValues);
			$stmt = "update ".$this->table." set ".$strUpdate." where id=:id";

			$req = $this->pdo->prepare($stmt);
			$req->execute($datas);
		}
	}

	public function savePCSOFT(){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		if(is_null($dataChild['row_idfixe'])){
			$dataChild['row_idfixe'] = Utils::create_hash();
			$this->row_idfixe = $dataChild['row_idfixe'];
			$stmt = "insert into ".$this->table." (".implode(',', array_keys($dataChild)).") values (:".implode(', :', array_keys($dataChild)).")";
			$req = $this->pdo->prepare($stmt);
			$req->execute($dataChild);

		}else{
			//UPDATE
			$this->row_id = $dataChild['row_id'];
			unset($dataChild['row_id']);

			foreach ($dataChild as $key => $value) {
				if($key !== 'row_idfixe')
					$update[] = $key.'=:'.$key;
			}

			$stmt = "update ".$this->table." set ".implode(', ', $update)." where row_idfixe=:row_idfixe";
			// var_dump($stmt); die();
			$req = $this->pdo->prepare($stmt);
			$req->execute($dataChild);
		}
	}

	public function getOneBy_PCSOFT(array $where, $object = false){
		// $where = ["id"=>$id, "email"=>"y.skrzypczyk@gmail.com"];
		$sqlWhere = [];
		foreach ($where as $key => $value) {
			$sqlWhere[]=$key."=:".$key;
		}
		$sql = " SELECT * FROM ".$this->table." WHERE  ".implode(" AND ", $sqlWhere).";";
		$query = $this->pdo->prepare($sql);

		if($object){
			//modifier l'objet $this avec le contenu de la bdd
			$query->setFetchMode( PDO::FETCH_INTO, $this);
		}else{
			//on retourne un simple table php
			$query->setFetchMode( PDO::FETCH_ASSOC);
		}

		$query->execute( $where );
		return $query->fetch();

	}

	public function insert(){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$fields = [];
		$injValues = [];
		$datas = [];

		foreach ($dataChild as $field => $value) {
			$fields[] = $field;
			$injValues[] = ':'.self::clean($field);
			$datas[':'.self::clean($field)] = $value;
		}

			// INSERT
			$strFields = implode(',', $fields);
			$strInjValues = implode(',', $injValues);
			$stmt = "insert into ".$this->table." (".$strFields.") values (".$strInjValues.")";

			$req = $this->pdo->prepare($stmt);
			$req->execute($datas);
	}

	public function delete($tabWhere){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$where = [];
		$data = [];

		foreach ($tabWhere as $field => $value) {
			$where[] = $field.'=:'.self::clean($field);
			$data[':'.self::clean($field)] = $value;
		}

		$strWhere = implode(' and ', $where);

		$stmt = " delete from ".$this->table." where " . $strWhere;
		$req = $this->pdo->prepare($stmt);
		$req->execute($data);

	}

	public function getAll() {

		$stmt = "select * from ".$this->table;
		$req = $this->pdo->prepare($stmt);
		$req->execute();

		$res = $req->fetchAll(PDO::FETCH_ASSOC);
		return $res;
	}

	public function setId($id){
		$this->id = $id;
		//va récupérer en base de données les élements pour alimenter l'objet
		$this->getOneBy(["id"=>$id], true);

	}
	// $where -> tableau pour créer notre requête sql
	// $object -> si vrai aliment l'objet $this sinon retourn un tableau
	public function getOneBy(array $where, $object = false){
		// $where = ["id"=>$id, "email"=>"y.skrzypczyk@gmail.com"];
		$sqlWhere = [];
		$data = [];
		foreach ($where as $key => $value) {
			$sqlWhere[]=$key."=:".$key;
			$data[':'.$key] = $value;
		}
		$sql = " SELECT * FROM ".$this->table." WHERE  ".implode(" AND ", $sqlWhere).";";
		$query = $this->pdo->prepare($sql);

		if($object){
			//modifier l'objet $this avec le contenu de la bdd
			$query->setFetchMode( PDO::FETCH_INTO, $this);
		}else{
			//on retourne un simple table php
			$query->setFetchMode( PDO::FETCH_ASSOC);
		}
		$query->execute( $data );
		$res = $query->fetch();
		return $res;
	}

	public function getOne($id) {
		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$fields = [];

		foreach ($dataChild as $field => $value) {
			$fields[] = $field;
		}

		$strFields = implode(',', $fields);

		$stmt = " select ".$strFields." from ".$this->table." where id=:id";
		$req = $this->pdo->prepare($stmt);
		$req->execute([':id' => $id]);
		$res = $req->fetch(PDO::FETCH_ASSOC);
		return $res;
	}

	public function selectAllFields($tabFields) {

		$strFields = implode(',', $tabFields);

		$stmt = " select ".$strFields." from ".$this->table;
		$req = $this->pdo->prepare($stmt);
		$req->execute();
		$res = $req->fetchAll(PDO::FETCH_ASSOC);
		return $res;

	}

	public function selectAllFieldsWhere($tabFields, $tabWhere, $caseSensitive = false) {

		$fields = [];
		$where = [];
		$data = [];
		$operator = $caseSensitive === true?' LIKE BINARY :':' = :';
		foreach ($tabFields as $field) {
			$fields[] = $field;
		}

		foreach ($tabWhere as $field => $value) {
			$where[] = $field. $operator .self::clean($field);
			$data[':'.self::clean($field)] = $value;
		}

		$strFields = implode(', ', $fields);
		$strWhere = implode(' and ', $where);

		$stmt = " select ".$strFields." from ".$this->table." where " . $strWhere;
		// die($stmt);
		$req = $this->pdo->prepare($stmt);
		$req->execute($data);
		$res = $req->fetchAll(PDO::FETCH_ASSOC);
		return $res;

	}

	public function selectOneFieldsWhere($tabFields, $tabWhere, $caseSensitive = false) {

		$fields = [];
		$where = [];
		$data = [];
		$operator = $caseSensitive === true?' LIKE BINARY :':' = :';
		foreach ($tabFields as $field) {
			$fields[] = $field;
		}

		foreach ($tabWhere as $field => $value) {
			$where[] = $field. $operator .self::clean($field);
			$data[':'.self::clean($field)] = $value;
		}

		$strFields = implode(', ', $fields);
		$strWhere = implode(' and ', $where);

		$stmt = " select ".$strFields." from ".$this->table." where " . $strWhere;
		// die($stmt);
		$req = $this->pdo->prepare($stmt);
		$req->execute($data);
		$res = $req->fetch(PDO::FETCH_ASSOC);
		return $res;

	}

	public function selectAllWhere($tabWhere, $orderBy=null) {

		$where = [];
		$data = [];

		foreach ($tabWhere as $field => $value) {
			$where[] = $field.'=:'.self::clean($field);
			$data[':'.self::clean($field)] = $value;
		}

		$strWhere = implode(' and ', $where);

		$stmt = " select * from ".$this->table." where " . $strWhere ." ".$orderBy;
		$req = $this->pdo->prepare($stmt);
		$req->execute($data);
		$res = $req->fetchAll(PDO::FETCH_ASSOC);
		return $res;
	}

	public function selectOneWhere($tabWhere) {

		$where = [];
		$data = [];

		foreach ($tabWhere as $field => $value) {
			$where[] = $field.'=:'.self::clean($field);
			$data[':'.self::clean($field)] = $value;
		}

		$strWhere = implode(' and ', $where);

		$stmt = " select * from ".$this->table." where " . $strWhere;
		$req = $this->pdo->prepare($stmt);
		$req->execute($data);
		$res = $req->fetch(PDO::FETCH_ASSOC);
		return $res;
	}

	public function executeRequest($request) {

		$req = $this->pdo->prepare($request);
		$req->execute();
		$res = $req->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) > 1)
			return $res;

		return $res[0];
	}

	public function executeRequestWithData($request, $data) {
		$req = $this->pdo->prepare($request);
		$req->execute($data);
		$res = $req->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) > 0)
			return $res;

		return false;
	}

	public function count($tabWhere) {

		$where = [];
		$data = [];

		foreach ($tabWhere as $field => $value) {
			$where[] = $field.'=:'.self::clean($field);
			$data[':'.self::clean($field)] = $value;
		}

		$strWhere = implode(' and ', $where);

		$stmt = " select count(*) as result from ".$this->table." where " . $strWhere;

		$req = $this->pdo->prepare($stmt);
		$req->execute($data);
		$res = $req->fetch();
		return $res[0];

	}

	/* SETTER */
	public function setTable($table) {
		$this->table = strtolower($table);
	}

	public static function clean($str) {
			$parenthesis = ['(', ')'];
			return str_replace($parenthesis, '', $str);
	}

	public function saveWithGeneratingId(){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$id = $dataChild['id']??null;
		if(is_null($id)){
			// insert
			$dataChild['id'] = Utils::create_hash();
			$stmt = "insert into ".$this->table." (".implode(', ',array_keys($dataChild)).") values (:".implode(', :', array_keys($dataChild)).")";
			$req = $this->pdo->prepare($stmt);
			$req->execute($dataChild);
		}else{
			$sqlWhere = [];
			//update
			foreach ($dataChild as $field => $value) {
				if (!$dataChild['id'])
					$sqlUpdate[] = $key.'=:'.$key;
			}
			$stmt = "update ".$this->table." set ".implode(', ', $sqlUpdate)." where id=:id";

			$req = $this->pdo->prepare($stmt);
			$req->execute($dataChild);
		}
	}

	public function insertFields(){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$stmt = "insert into ".$this->table." (".implode(', ',array_keys($dataChild)).") values (:".implode(', :', array_keys($dataChild)).")";
		// var_dump($stmt); die();
		$req = $this->pdo->prepare($stmt);
		$req->execute($dataChild);

	}

	public function updateFields(){

		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));

		$sqlWhere = [];
		//update
		foreach ($dataChild as $field => $value) {
			if (!$dataChild['id'])
				$sqlUpdate[] = $key.'=:'.$key;
		}
		$stmt = "update ".$this->table." set ".implode(', ', $sqlUpdate)." where id=:id";

		$req = $this->pdo->prepare($stmt);
		$req->execute($dataChild);

	}

	public function getPureObject() {
		$dataObject = get_object_vars($this);
		return array_diff_key($dataObject, get_class_vars(get_class()));
	}

}
