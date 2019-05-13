<?php
class APIDocumentation {

  private $endpoint;
  private $description;
  private $method;
  private $private;
  private $requiredFields;
  private $returns;
  private $keyColumn;
  private $uniqueColumns;
  private $schema;
  private $URLParameters;
  private $HTMLid;

  public function __construct() {

  }

  public function generate() {
    //
    $routefile = "app/routes.yml";
    $docfile = "app/doc.yml";
    $routes = yaml_parse_file($routefile);
    $documentation = yaml_parse_file($docfile);
    $methods = ["get", "post", "put", "delete"];
    $doc = [];
    // loop through routes
    foreach ($routes as $route => $routeConfs) {
      $this->clear();
      if (strpos($route, '/documentation') !== false)
        { continue; }
      // loop through the methods
      foreach ($methods as $method) {
        // Does it available for current method
        if (!isset($routeConfs[$method]))
          { continue; }
        //
        $documentationKey =  $routeConfs[$method]["doc"]??"";
        //
        if (isset($documentation[$documentationKey]))
        {
            $this->description = $documentation[$documentationKey]["description"]??null;
            $this->returns = $documentation[$documentationKey]["returns"]??null;
        }
        $matches = [];
        if (preg_match_all('#[:]+[a-zA-Z-]*#', $route, $matches)) {
          $this->URLParameters = $matches[0];
        }
        //
        $this->endpoint = $route;
        $this->private = $routeConfs[$method]['private']??false;
        $this->method = $method;
        //
        $schema_obj = $routeConfs["controller"];
        $this->schema = new $schema_obj;
        //
        $this->requiredFields = $routeConfs['requiredFields']??[];
        $schema = new Schema($routeConfs["controller"]);
        if ($routeConfs[$method]['action'] === $method
                    && ($method==='post' || $method==='put')) {
           // Case of basic CRUD
           if ($method === 'put') {
            $this->requiredFields = array_merge(['id'], $this->requiredFields);
           }
           $this->requiredFields = array_merge($this->requiredFields, $schema->getRequiredFields());
         }
         // keyColumn
         $this->keyColumn = $schema->getKeyColumn();
         $this->uniqueColumns = $schema->getUniqueColumns();

         //
         $this->HTMLid = strtolower(str_replace([':', '/', '\''], '-', $documentationKey));

          $result[$routeConfs["controller"]][] = [
            "endpoint" => $this->endpoint,
            "description" => $this->description,
            "method" => $this->method,
            "private" => $this->private,
            "requiredFields" => $this->requiredFields,
            "returns" => $this->returns,
            "keyColumn" => $this->keyColumn,
            "uniqueColumns" => $this->uniqueColumns,
            "schema" => $this->schema,
            "URLParameters" => $this->URLParameters,
            "HTMLid" => $this->HTMLid,
          ];
      }
    };

    return $result;
  }

  public static function toJSON($arr, $withKeys = false) {
    $json = "{\n";
    if ($withKeys) {
      foreach ($arr as $key => $value) {
        $json .= "\t\"$key\": \"\",\n";
      }
    }else {
      foreach ($arr as $key) {
        $json .= "\t\"$key\": \"\",\n";
      }
    }
    $json .= "}";
    return $json;
  }

  private function clear() {
    $this->endpoint = null;
    $this->description = null;
    $this->method = null;
    $this->private = null;
    $this->requiredFields = null;
    $this->returns = null;
    $this->keyColumn = null;
    $this->uniqueColumns = null;
    $this->schema = null;
    $this->URLParameters = null;
    $this->HTMLid = null;
  }

  private function hasRequiredFields($array) {
    if (!isset($array['description'])) { return false; }
    // if (!isset($array['required'])) { return false; }
    if (!isset($array['returns'])) { return false; }
    return true;
  }
}
