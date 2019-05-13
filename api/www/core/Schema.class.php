<?php
class Schema extends DB {

  private $schemaFile = "app/schema.yml";

  public function __construct($table) {
    $this->tableName = ucfirst($table);
  }

  public function getRequiredFields() {
    $schema = $this->readFile();
    if (isset($schema[$this->tableName]["requiredFields"])) {
      return $schema[$this->tableName]["requiredFields"];
    }
    return [];
  }

  public function getUniqueColumns() {
    $schema = $this->readFile();
    if (isset($schema[$this->tableName]["uniqueColumns"])) {
      if ($this->getKeyColumn() !== "") {
        $schema[$this->tableName]["uniqueColumns"] = array_merge([$this->getKeyColumn()], $schema[$this->tableName]["uniqueColumns"]);
      }
      return $schema[$this->tableName]["uniqueColumns"];
    }
    return [];
  }

  public function getKeyColumn() {
    $schema = $this->readFile();
    if (isset($schema[$this->tableName]["keyColumn"])) {
      return $schema[$this->tableName]["keyColumn"];
    }
    return "";
  }

  public function getRelations() {
    $schema = $this->readFile();
    if (isset($schema[$this->tableName]["relations"])) {
      return $schema[$this->tableName]["relations"];
    }
    return [];
  }

  public function getStructure() {
    $schema = $this->readFile();
    if (isset($schema[$this->tableName]["structure"])) {
      return $schema[$this->tableName]["structure"];
    }
    return [];
  }

  public function readFile() {
    return yaml_parse_file($this->schemaFile);
  }
  
}
