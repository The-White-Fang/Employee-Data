<?php

require_once('./classes/employee.class.php');
require_once('./inc/validators.php');

// load post data from input stream
$jsonPost = file_get_contents('php://input');

// assign $jsonPost to post super-global if it is empty
$_POST = empty($_POST) ? json_decode($jsonPost, true) : $_POST;

// if put request
if (empty($_POST)) {
	parse_str($jsonPost, $_POST);
}

// set json header
header('content-type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {

	case 'POST':
		$empArr = array();
		// assigned cleaned values and false if value is invalid
		// ?? for undefined keys
		$empArr['name'] = validateName($_POST['name'] ?? '');
		$empArr['dob'] = validateDOB($_POST['dob'] ?? '');
		$empArr['ctc'] = validateCtc($_POST['ctc'] ?? '');
		$empArr['tech'] = validateTech($_POST['tech'] ?? '');

		// check if all fields are supplied and valid
		$invalid = '';
		if (!$empArr['name']){
			$invalid = 'name';
		} else if (!$empArr['dob']){
			$invalid = 'dob';
		} else if (!$empArr['ctc']){
			$invalid = 'ctc';
		} else if (!$empArr['tech']){
			$invalid = 'tech';
		}

		if ($invalid) {
			http_response_code(400);
			exit('Invalid or missing value for ' . $invalid);
		}

		// add new employee to DB
		$employee = new Employee();
		$empArr['id'] = $employee->create($empArr);

		echo json_encode($empArr);
		break;
	
	case 'GET':
		if (!isset($_GET['id'])) {
			$employee = new Employee();
			echo json_encode($employee->getAll());
		} else {
			$id = validateId($_GET['id']);
			if (!$id) {
				http_response_code(400);
				exit('Invalid value for \'id\'');
			}

			$employee = new Employee();
			$empArr = $employee->get($id);

			if (!$empArr) {
				http_response_code(404);
				exit('Employee not found');
			}

			echo json_encode($empArr);
		}
		break;

	case 'PUT':
		$empArr = array();
		// assigned cleaned values and false if value is invalid
		// ?? for undefined keys
		$empArr['id'] = validateId($_POST['id'] ?? '');
		$empArr['name'] = validateName($_POST['name'] ?? '');
		$empArr['dob'] = validateDOB($_POST['dob'] ?? '');
		$empArr['ctc'] = validateCtc($_POST['ctc'] ?? '');
		$empArr['tech'] = validateTech($_POST['tech'] ?? '');

		// check if all fields are supplied and valid
		$invalid = '';
		if (!$empArr['id']){
			$invalid = 'id';
		} else if (!$empArr['name']){
			$invalid = 'name';
		} else if (!$empArr['dob']){
			$invalid = 'dob';
		} else if (!$empArr['ctc']){
			$invalid = 'ctc';
		} else if (!$empArr['tech']){
			$invalid = 'tech';
		}

		if ($invalid) {
			http_response_code(400);
			exit('Invalid or missing value for ' . $invalid);
		}

		// add new employee to DB
		$employee = new Employee();
		// check if user exists
		if(!$employee->get($empArr['id'])){
			http_response_code(400);
			exit('Employee not found');
		}
		$employee->update($empArr);

		echo json_encode($empArr);
		break;

	case 'DELETE':
		$id = validateId($_GET['id']);
		if (!$id) {
			http_response_code(400);
			exit('Invalid value for \'id\'');
		}

		$employee = new Employee();
		$empArr = $employee->get($id);

		if (!$empArr) {
			http_response_code(400);
			exit('Employee not found');
		}

		$employee->delete($id);

		echo json_encode($empArr);
		break;

	default:
		http_response_code(405);
		exit('Method is not allowed');

}

?>