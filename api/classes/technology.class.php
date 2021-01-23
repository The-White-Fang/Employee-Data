<?php

require_once('database.class.php');

class Technology extends Database{
	
	public function create ($name) {
		// store the object in database
		$mysqli = $this->connect();
		
		$sql = 'INSERT INTO Technologies (name) VALUES (?)';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('s', $name);
		$res = $stmt->execute();
		
		// check if object is added, return 500 if failed
		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to insert new data in database');
		}
		
		// return inserted id
		return $mysqli->insert_id;
	}
	
	public function delete ($name) {
		$mysqli = $this->connect();
		
		$sql = 'DELETE FROM Technologies WHERE name = (?)';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('s', $name);
		$res = $stmt->execute();
		
		// check if object is deleted, return 500 if failed
		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to delete entry from database');
		}
		
		// return true if deleted
		return true;
	}

	public function getAll () {
		$mysqli = $this->connect();
		
		$sql = 'SELECT * FROM Technologies';
		$stmt = $mysqli->prepare($sql);
		$res = $stmt->execute();
		
		// check if object is added, return 500 if failed
		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			exit('Unable to insert employee in database');
		}
		
		// get result
		$arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

		$res = array();

		for ($i = 0; $i < count($arr); $i++) {
			$res[$i] = $arr[$i]['name'];
		}

		return $res;
	}
}