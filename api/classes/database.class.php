<?php

require_once('./inc/config.php');

class Database
{
	protected function connect () {
		global $CONFIG;
		try {
			$mysqli = new mysqli($CONFIG->host, $CONFIG->username, $CONFIG->password, $CONFIG->database);
			$mysqli->set_charset("utf8mb4");
		} catch (Exception $e) {
			error_log($e->getMessage());
			header('HTTP/1.1 500 Internal Server Error');
			exit('Error connecting to database'); //Should be a message a typical user could understand
		}

		return $mysqli;
	}
}
