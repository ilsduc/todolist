<?php
declare(strict_types=1);
/*
	** Connexion to database
*/
class DB {
	/*
		** Members
	*/
	protected $pdo;
	protected $table;
	/*
		** Constructor
	*/
	public function __construct(){
		try{
			/*
				** Database connexion
			*/
      $this->pdo = new PDO(Config::$driver.":host=".Config::$host.";dbname=".Config::$dbname.";charset=UTF8",Config::$user,Config::$pwd);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			/*
				** Showing error -- enabled in prod
			*/
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(Exception $e){
			/*
				** Errors
			*/
			die("Erreur SQL : ".$e->getMessage());
		}
		/*
			** Retrieve class Name --> SQL table name
		*/
		$this->table = strtolower(get_called_class());
	}
	/*
		** get ONE <something> by <Something>
		*
		* must be called by an ENTITY
	*/
	public function getOneBy(array $where) {
		$dataObject = get_object_vars($this);
		$query = $this->getOneBy_Query($where);
		$query->execute($where);
		$query->setFetchMode(PDO::FETCH_INTO, $this);
		$res = $query->fetch();
		if ($_GET['include']??false) {
			if ($this->relations??false) {
				$all = $this->getObjectRelations();
			}
		}
	}
	/*
		**
	*/
	protected function getOneBy_Query(array $where) :PDOStatement {
		/*
			** Construct array for where clause
		*/
		$sqlWhere = [];
		foreach ($where as $key => $value) {
			$sqlWhere[]=$key."=:".$key;
		}
		/*
			** SQL statement
		*/
		$stmt = " SELECT * FROM ".$this->table." WHERE  ".implode(" AND ", $sqlWhere);
		// $stmt = self::addPagination($stmt);
		/*
			** Prepare the request
		*/
		return $this->pdo->prepare($stmt);
	}
	/*
		** get ALL <something> by <Something>
		*
		* must be called by an ENTITY
	*/
	public function getAllBy(array $where) {
		$query = $this->getOneBy_Query($where);

		$query->execute( $where );
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		if ($res===false) {
			return [];
		}
		return $res;
	}
	/*
		** get ALL <something>
		*
		* must be called by an ENTITY
	*/
	public function getAll() {
		$sql = " SELECT * FROM ".$this->table;
		// $sql = self::addPagination($sql);

		$query = $this->pdo->prepare($sql);
		$query->execute();
		$all = $query->fetchAll(PDO::FETCH_ASSOC);
		if ($all==false)
			return [];
		if ($_GET['include']??false) {
			if ($this->relations??false)
				$all = $this->getArrRelations($all);
		}
		return $all;
	}

	public function getAllWhere($where) :?array{
		$sqlWhere = [];
		foreach ($where as $key => $value) {
			$sqlWhere[] = $key.'=:'.$key;
		}

		$sql = " SELECT * FROM ".$this->table . " WHERE " . implode(' and ', ($sqlWhere));

		$query = $this->pdo->prepare($sql);
		$query->execute($where);
		$all = $query->fetchAll(PDO::FETCH_ASSOC);
		if ($all==false)
			return [];
		if ($_GET['include']??false) {
			if ($this->relations??false)
				$all = $this->getArrRelations($all);
		}
		return $all;
	}

	public function save(){
		//
		$dataObject = get_object_vars($this);
		$dataChild = array_diff_key($dataObject, get_class_vars(get_class()));
		//
		if(is_null($dataChild["id"])){
			$this->setId(Utils::create_hash());
			$sql ="INSERT INTO ".$this->table." ( ".
			implode(",", array_keys($dataChild) ) .") VALUES ( :".
			implode(",:", array_keys($dataChild) ) .")";
			$query = $this->pdo->prepare($sql);
			$query->execute( $this->getRealObject() );
		}else{
			//UPDATE
			$sqlUpdate = [];
			foreach ($dataChild as $key => $value) {
				if( $key != "id")
				$sqlUpdate[]=$key."=:".$key;
			}
			$sql ="UPDATE ".$this->table." SET ".implode(",", $sqlUpdate)." WHERE id=:id";
			$query = $this->pdo->prepare($sql);
			$query->execute( $dataChild );
		}
		return $this;
	}
	public function getRealObject() :array{
		$dataObject = get_object_vars($this);
		return array_diff_key($dataObject, get_class_vars(get_class()));
	}
	public function makePure() :array{
		$dataObject = get_object_vars($this);
		return array_diff_key($dataObject, get_class_vars(get_class()));
	}
	/*
		** give the where clause depeds on $this->relations
	*/
	private function getObjectRelations() :void{
		foreach ($this->relations as $relation_name => $table) {
			$property = strtolower($table).'s';
			switch ($relation_name) {
				case 'hasMany':
					if (!(strtolower($table.'s')==strtolower($_GET['include']??"")))
						continue;
					$this->$property = $this->select("* from ".strtolower($table)." where id".ucfirst($this->table).'=:id', ['id' => $this->id]);
					break;
				case 'hasOne':
					if (!(strtolower($table)==strtolower($_GET['include']??'')))
						continue;
					$this->$property = $this->selectOne("* from ".strtolower($this->table)." where id".ucfirst($table).'=:id', ['id' => $this->id]);
					break;
				default:
					# code...
					break;
			}
		}
	}
	public function getArrRelations(array $all) :array{
		foreach ($all as $key => $value) {
			foreach ($this->relations as $relation_name => $table) {
				$entityTable = new $table();
				$property = strtolower($table);
				switch ($relation_name) {
					case 'hasMany':
						if (!(strtolower($table.'s')==strtolower($_GET['include']??"")))
							continue;
						$method = 'getAllById'.ucfirst(get_called_class());
						$entity = new $table;
						$all[$key][$property.'s'] = $entity->$method($value['id']);
						break;
					case 'hasOne':
						if (!(strtolower($table)==strtolower($_GET['include']??"")))
							continue;
						$entityTable->getOneById($value['id'.$table]);
						$all[$key][$property] = $entityTable;
						break;
					default:
						# code...
						break;
				}
			}
		}
		return $all;
	}
	public function select($stmt, $data) {
		$q = $this->pdo->prepare('select '.$stmt);
		$q->execute($data);
		return $q->fetchAll(PDO::FETCH_ASSOC);
	}

	public function selectOne($stmt, $data) {
		$q = $this->pdo->prepare('select '.$stmt);
		$q->execute($data);
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public function executeRequest($query, $data = []) {
		$q = $this->pdo->prepare($query);
		$q->execute($data);
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public function execFile($file_content) {
		$this->pdo->exec($file_content);
	}

	public function deleteBy($by) {
		$sqlWhere = [];
		foreach ($by as $key => $value) {
			$sqlWhere[]=$key."=:".$key;
		}

		$stmt = 'delete from '.$this->table.' where '.implode(' and ', $sqlWhere);
		$q = $this->pdo->prepare($stmt);

		$q->execute($by);
		return true;
	}
}
