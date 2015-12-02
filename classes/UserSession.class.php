<?php

class UserSession {
	private $username, $password, $rank, $mysqli;

	function __construct ($username, $password) {
		$this->mysqli = new mysqli('localhost', 'root', '','Phlogger');

		$this->username = $this->mysqli->real_escape_string($username);
		$this->password = $this->mysqli->real_escape_string($password);

		$this->login();
	}

	function login() {
		$query = 'SELECT * 
		FROM user 
		WHERE user.username = '.$this->username.'
		AND user.password = '.$this->password;

		$result = $this->mysqli->query($query);
		$row = $result->fetch_assoc();

		while ( $row = $result->fetch_assoc() ) {
			$this->user[] = new user(
				$row['id'],
				$row['Username'], 
				$row['Password'], 
				$row['Rank']);
		}	

		if ( isset ($this->user) ){
			$IsLoggedIn = true;
		} else {
			$IsLoggedIn = false;
		}
	}
}