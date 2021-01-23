<?php

require_once('./classes/technology.class.php');

function validateName ($name) {
	if (!gettype($name) == 'string') {
		return false;
	}

	$name = trim($name);

	if (empty($name) or !preg_match('/^[a-zA-Z ]{2,}$/i', $name)) {
		return false;
	}

	return $name;
}

function validateDOB ($dob) {
	if (!gettype($dob) == 'string') {
		return false;
	}
	
	$dob = trim($dob);
	
	if (empty($dob) or !preg_match('/^\d{2}-\d{2}-\d{4}$/', $dob)) {
		return false;
	}
	
	return $dob;
}

function validateCtc ($ctc) {
	if (!is_numeric($ctc)) {
		return false;
	}

	$ctc = intval($ctc);

	if ($ctc <= 0) {
		return false;
	}

	return $ctc;
}

function validateTech ($tech) {
	if(gettype($tech) != 'array'){
		return false;
	}
	
	$technology = new Technology();

	$arr = $technology->getAll();

	foreach ($tech as $tec => $exp){
		if (!in_array($tec, $arr) or !is_numeric($exp)){
			return false;
		}

		$tech[$tec] = intval($exp);
	}

	return $tech;
}

function validateId($id) {
	if (!is_numeric($id)){
		return false;
	}

	$id = intval($id);

	if ($id <= 0){
		return false;
	}

	return $id;
}