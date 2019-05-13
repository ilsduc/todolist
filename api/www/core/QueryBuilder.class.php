<?php

class QueryBuilder extends DB {

  private $statement='';
  //
  // private $table;
  private $where;
  private $data;
  // private $group_by;
  // private $having;
  // private $order_by;

  public function select(array $select) {
    $this->statement = 'select '.implode(', ', $select).' ';
    return $this;
  }

  public function from($from) {
    $this->statement = 'from '.$from.' ';
    return $this;
  }

  public function where($where) {
    $wherestr = $this->where?'where ':'and ';
    $filter = Session::get('filter')??null;
    if ($filter['where']??null) {

    }

    return $this;
  }

  public function clear() {
    $this->statement = '';
    // $this->$table = '';
    $this->$where = '';
  }

  private function getWhereClause($where) {
    $sqlWhere = [];
    $data = [];
    foreach ($where as $field => $value) {
      if (strpos($field, '_lesser_than') !== false) {
        $explodedWhere = explode('_', $field);
        $sqlWhere[] = $explodedWhere[0].'< :'.$explodedWhere[0];
        $data[$explodedWhere[0]] = $value;
        continue;
      }
      if (strpos($field, '_greater_than') !== false) {
        $explodedWhere = explode('_', $field);
        $sqlWhere[] = $explodedWhere[0].'> :'.$explodedWhere[0];
        $data[$explodedWhere[0]] = $value;
        continue;
      }
      $sqlWhere[] = $key.'= :'.$key;
      $data[$key] = $value;
    }
    $this->data += $data;
  }

  public function __destruct() {
    Session::unset('filter');
  }

}
