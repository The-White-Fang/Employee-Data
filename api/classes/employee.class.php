<?php

require_once('database.class.php');
require_once('./inc/helpers.php');

class Employee extends Database {
	public function create($employeeArr) {
		// change date format to mysql date format
		$employeeArr['dob'] = date("Y-m-d", strtotime($employeeArr['dob']));
		// convert object to string to be stored
		$employeeArr['tech'] = json_encode($employeeArr['tech']);
		
		// store the object in database
		$mysqli = $this->connect();
		
		$stmt = $mysqli->prepare('INSERT INTO Employees (name, dob, ctc, tech) VALUES (?, ?, ?, ?)');
		$stmt->bind_param('ssis', 
							$employeeArr['name'], 
							$employeeArr['dob'], 
							$employeeArr['ctc'], 
							$employeeArr['tech']
						);
		$res = $stmt->execute();
		
		// check if object is added, return 500 if failed
		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to insert employee in database');
		}
		
		// return inserted id
		return $mysqli->insert_id;
	}

	public function getAll() {
		$mysqli = $this->connect();

		$sql = 'SELECT * FROM Employees';
		$stmt = $mysqli->prepare($sql);
		$res = $stmt->execute();

		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to read data from database');
		}

		$arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

		$arr = array_map('decode_tech', $arr);
		$arr = array_map('adaptDate', $arr);

		return $arr;
	}

	public function get($id) {
		$mysqli = $this->connect();

		$sql = 'SELECT * FROM Employees WHERE id = ?';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('i', $id);
		$res = $stmt->execute();

		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to read data from database');
		}

		$arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

		if(!$arr) {
			return $arr;
		}

		$arr[0]['tech'] = json_decode($arr[0]['tech']);
		$arr[0]['dob'] = dateMysqltoPHP($arr[0]['dob']);

		return $arr[0];
	}
	
	public function update($employeeArr) {
		// change date format to mysql date format
		$employeeArr['dob'] = date("Y-m-d", strtotime($employeeArr['dob']));
		// convert object to string to be stored
		$employeeArr['tech'] = json_encode($employeeArr['tech']);

		$mysqli = $this->connect();

		$sql = 'UPDATE Employees SET name = ?, dob = ?, ctc = ?, tech = ? WHERE id = ?';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('ssisi', 
							$employeeArr['name'], 
							$employeeArr['dob'], 
							$employeeArr['ctc'], 
							$employeeArr['tech'],
							$employeeArr['id']
						);
		$res = $stmt->execute();
		
		// check if object is added, return 500 if failed
		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to update employee data');
		}

		return true;
	}

	public function delete ($id) {
		$mysqli = $this->connect();

		$sql = 'DELETE FROM Employees WHERE id = ?';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('i', $id);
		$res = $stmt->execute();

		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to delete data from database');
		}

		return true;
	}
}