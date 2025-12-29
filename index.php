<?php

    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

	$route 	= $_SERVER['REQUEST_URI'];
	$method = $_SERVER['REQUEST_METHOD'];
	$route = substr($route, 1);
	$route = explode("?", $route);
	$route = explode("/", $route[0]);
	$route = array_diff($route, array('API_Restful', 'api'));
	$route = array_values($route);
	$arr_json = null;
	include('client.class.php');

	switch (true) {
		case ($method == "GET"):

			$client = new Client(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
			$arr_json = $client->verifyMethod($method,$route,null);
			break;
		case ($method == "POST" || $method == "DELETE"):

			if(empty($_FILES)){

			  $data = trim(file_get_contents("php://input"));
			  $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
			  $decoded = json_decode($data, true);
 
			  if(strcasecmp($contentType, 'application/json') != 0){
				  return array('status' => 'Formato JSON inválido!');
			  }
			  if(!is_array($decoded)){
				  return array('status' => 'Conteúdo JSON inválido!');
			  }
			  $client = new Client();
			  $arr_json = $client->verifyMethod($method,$route,$decoded);
		    }else{
			  $client = new Client();
			  $arr_json = $client->verifyMethod($method,$route,file_get_contents("php://input"));
		    }
			break;
			default:
			return array('status' => 405);
      		break;
		}
             	
	echo json_encode($arr_json);
?>