<?php
class Media extends SuperModel {
    public function __construct(){
        parent::__construct();
    }
    /*
      ** upload the media
    */
    public function upload($file) {
      /*
        ** is there some file to upload
      */
      if (isset($file))
        FileLoaderController::setFiles($file);
      else
        API::error('No file found.');

      /*
        ** Upload the current media
      */
      FileLoaderController::upload($this);

    }

}
