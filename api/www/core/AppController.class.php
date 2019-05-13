<?php
class AppController {
  private $model;
  public function __construct() {
    $this->model = get_called_class();
    $this->model = str_replace('Controller', '', $this->model);
  }
  // render method for view
  public function render($view, $template, $confs):void {
    $v = new View(strtolower($this->model).'/'.$view, $template, $confs);
  }
  // BasicCRUD
  // post method --> create
  public function post() {
    $model = new $this->model;
    $model->post();
    return $model;
  }
  // put method: full update
  public function put() {
    // TODO: must do the stuff in other way --> it's a full update
    $model = new $this->model;
    $model->put();
    return $model;
  }

  // patch method: partial update
  public function patch() {
    // TODO: patch must works as put works
  }

  // get method --> fetch or fetch all
  public function get($id) {
    $model = new $this->model;
    $models = $model->find($id);
    if (is_array($models)){
      return $models;
    }
    return ($model->id?$model:[]);
  }

  // delete method
  public function delete($id) {
    $model = new $this->model;
    $model->find($id);
    //
    $model->delete();
    return $model;
  }

}
