<?php
/* Router Class*/
class Routing{

	public static $routeFile = "app/routes.yml";
	public static $configFile = "conf.yml";

	public static function getRoute($slug){
		/*
			** keep rootpath
		*/
		$slug = $slug===''?'/':$slug;
		/* Explode the slug into an array */
		$explodedSlug = explode('/', $slug);
		/*
			* Get all the params we need to define what to do
		*/
		$request = strtolower($_SERVER["REQUEST_METHOD"]);		// request type (GET, POST, PUT, DELETE)

		// var_dump($request);
		if ($request === 'options') {
			header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
			echo json_encode([]);
			exit;
		}

		$request = $request === 'options' ? 'post' : $request;

		// var_dump($request); die();

		if (($request === 'put')) {
			self::getPutParameters();
		}
		/* Error variable: set to true if an error occured */
		$error = false;

		/* Set to true if the requested ressources has a restricted access */
		$isRestricted = false;
		/* Validation of access_token variable */
		$isTokenValid = false;
		/* Set to true if the requested ressource is restricted to the ressource's owner */
		$isRestrictedToOwner = false;
		/* Set to true if the request is sending by the owner of the requested ressource */
		$isOwner = false;

		/* Variable that corresponding to the parameter given for all routes matches with /api/.../{parameter} */
		$parameter = null;

		$DBconfs = yaml_parse_file(self::$configFile);
			if (isset($DBconfs[$_SERVER['SERVER_NAME']]))
				Config::init($DBconfs[$_SERVER['SERVER_NAME']]);		// Set the application configuration
			else
				$error = true;

		/* Parsing routes.yml file ton an array */
		$routes = yaml_parse_file(self::$routeFile);

		/* Traitement of routes which includes parameters /api/.../{parameter} */
		if(!$error && !isset($routes[$slug])){
			/* Is that route matches any route that accepts parameters? */
			foreach ($routes as $slugRoute => $params) {
				$explodedSlugRoute = explode('/', $slugRoute);
				if (count($explodedSlug) != count($explodedSlugRoute))
					continue;

				$pattern = '#[:]+[a-zA-Z-]*#';
				$matches = [];
				$newExplodedSlug = [];
				$getParams = [];

				if (!(preg_match($pattern, $slugRoute)))
					continue;

				foreach ($explodedSlugRoute as $index => $routeEl) {
					$find = false;
					if (!(preg_match($pattern, $routeEl, $matches)))
						if (!($explodedSlug[$index] === $explodedSlugRoute[$index]))
							break;

					/*  */
					// var_dump($matches);
					if (empty($matches)) {
						$routeElRealName = $explodedSlug[$index];
					}
					else{
						$routeElRealName = $matches[0];
						$getParams[str_replace(':', '', $routeElRealName)] = $explodedSlug[$index];
						$parameter = $explodedSlug[$index];
					}

					$newExplodedSlug[] = $routeElRealName;
					$find = true;
				}

				if ($find) {
					$slug = implode('/', $newExplodedSlug);
					array_merge($_GET, $getParams);
					break;
				}

			}
			$find = $find??false;
			if (!$find)
				$error = true;
		}

		/* Does the request type is defined ? */
		if(!$error && !isset($routes[$slug][$request])) {
			if (strtolower($request) === 'get'
				&& isset($routes[$slug]['controller'])
				&& isset($routes[$slug]['action']) ) {
					$routes[$slug][$request] = $routes[$slug];
			}
		}

		if (isset($_REQUEST['filter'])) {
			if (isset($routes[$slug][$request]["filters"])
							&& isset($routes[$slug][$request]["filters"][$_REQUEST['filter']]))
							$routes[$slug][$request]["action"] = $routes[$slug][$request]["filters"][$_REQUEST['filter']]['action']??$routes[$slug][$request]["action"];
		}

		/* Does the Controller, and the Action (method) are filled */
		if(!$error && !isset($routes[$slug][$request]["controller"]) && !isset($routes[$slug][$request]["action"]))
			$error = true;

		/* if an error occured, send a 404 response */
		if($error)
			API::send(404);

		/* if the access is reserved, send a 401 response */
		if (($isRestrictedToOwner && !$isOwner) || ($isRestricted && !$isTokenValid))
			API::send(401);

		/* Set variables for index.php  */
		$c = ucfirst($routes[$slug]["controller"])."Controller";
		$a = $routes[$slug][$request]["action"];
		$cPath = "app/controllers/".$c.".class.php";

		return ["c"=>$c, "a"=>$a,"cPath"=>$cPath, "parameter" => $parameter];
	}

	/*
	** @params $c : Controller Name
	** @params $a : Action Name
	** @return : Return the slug that matches the Controller and the Action
	*/
	public static function getSlug($c, $a){
		$routes = yaml_parse_file(self::$routeFile);

		foreach ($routes as $slug => $cAndA) {
			if( !empty($cAndA["controller"]) &&
				!empty($cAndA["action"]) &&
				$cAndA["controller"] == $c &&
				$cAndA["action"] == $a){
					return $slug;
				}
		}
		return null;
	}

	/*******************
	**	TOOLS METHOD	**
	*******************/
	/*
	** Method that returns true if the requested ressource has restriction access
	*/
	public static function isRestricted($acl) {
		if (isset($acl['isRestricted']))
			return $acl['isRestricted'];

		return false;
	}

	public static function getPutParameters() {
		parse_str(file_get_contents("php://input"), $_PUT);
		foreach ($_PUT as $key => $value)
		{
			unset($_PUT[$key]);

			$_PUT[str_replace('amp;', '', $key)] = $value;
		}

		$_REQUEST = array_merge($_REQUEST, $_PUT);
	}

}
