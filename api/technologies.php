<?php

require_once('./classes/technology.class.php');

// load post data from input stream
$jsonPost = file_get_contents('php://input');
$jsonPost = json_decode($jsonPost, true);

// assign $jsonPost to post super-global if it is empty
$_POST = empty($_POST) ? $jsonPost : $_POST;

$respData = null;

switch ($_SERVER['REQUEST_METHOD']) {
	case 'POST':
		$name = $_POST['name'] ?? '';
		$name = trim($name);

		if (!$name or gettype($name) != 'string') {
			http_response_code(400);
			exit('Required field(s) or supplied with invalid values.');
		}

		$tech = new Technology();
		$id = $tech->create($name);
		
		$respData = array('id' => $id, 'name' => $name);
		break;
		
	case 'GET':
		$tech = new Technology();
		$respData = $tech->getAll();
		break;

	case 'DELETE':
		$name = $_GET['name'] ?? '';
		$name = trim($_POST['name']);

		if (!$name or gettype($name) != 'string') {
			http_response_code(400);
			exit('Required field(s) or supplied with invalid values.');
		}

		$tech = new Technology();
		$tech->delete($name);
		break;
	
	default:
		http_response_code(405);
		exit('Method is not allowed');
}

header('content-type: application/json');
if ($respData) {
	echo json_encode($respData);
}