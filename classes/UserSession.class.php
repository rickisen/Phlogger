<?php

class UserSession {
	private $username, $password, $rank, $mysqli;

	function __construct ($username, $password) {
		$this->mysqli = new mysqli('localhost', 'root', '','Phlogger');

		$this->username = $this->mysqli->stripslashes($username); // stripslashes: Returns a string with backslashes stripped off. (\' becomes ' and so on.) Double backslashes (\\) are made into a single backslash (\). 
		$this->password = $this->mysqli->stripslashes($password);
		$this->username = $this->mysqli->real_escape_string($username);
		$this->password = $this->mysqli->real_escape_string($password);

		$this->login();
	}

	function login() {
		$query = 'SELECT Username, Password
		FROM user';

		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();

		while ( $row = $result->fetch_assoc() ) {
			$this->username[] = $row['username'];
			$this->password[] = $row['password'];
			$this->rank[] = $row['rank'];
		}	

		if($row['username'] == $this->username && $row[password] == $this->password)
			$IsLoggedIn = true;
		else
			$IsLoggedIn = false;
	}
}