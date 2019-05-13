<?php
/* START THE SESSION PERMIT LOG in $_SESSION persistent variable */
session_start();
/* set cors */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
/* We need the Autoloader class to active the autoload */
require 'core/Autoloader.class.php';
/* Activation of autoloading */
Autoloader::register();

// die($token->id);
/* Get the SLUG : ex localhost:5000/api/app_name/ressource --> slug: /api/app_name/ressource */
$slug = $_SERVER["REQUEST_URI"];
/* Extract params from the slug and format the slug */
$slugExploded = explode("?", $slug);
$slug = $slugExploded[0];
$slug = rtrim($slug, '/');

/* get Route from the SLUG --> Wich Controller? Which Action (method)? Wich params */
$routes = Routing::getRoute($slug);
extract($routes);

/* Does the Controller exist? */
if( file_exists($cPath) ){
	include $cPath;
	if( class_exists($c)){
		/* Create the Controller object */
		$cObject = new $c();
		/* Does the Action (method) exist? */
		if( method_exists($cObject, $a) ){
			/* Call the method on the Controller Object and give it the params */
			/*
			* NOTE : The param is the argument is given in the slug like : /api/app_name/ressource/{params}
			*/
			$data = $cObject->$a($parameter);
			API::send(200, $data);
		}else{
			die("La methode ".$a." n'existe pas");
		}
	}else{
		die("La class controller ".$c." n'existe pas");
	}
}else{
	die("Le fichier controller ".$c." n'existe pas");
}
