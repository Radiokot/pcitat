<?php

ini_set('display_errors', 1);

class DBManager {
	static function connect() {
		$db = new PDO("sqlite:".dirname(__FILE__)."/.quoter.sqlite");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	}

	static function fetch($statement) {
		$results = [];
		while ($row = $statement->fetch(PDO::FETCH_ASSOC))
		{
			$results[] = $row;
		}
		return $results;
	}
}

?>