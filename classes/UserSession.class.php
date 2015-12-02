<?php

class UserSession {
	private $username, $password, $rank;

	function __construct ($username, $password) {
		$this->username = $username; 
		$this->password = $password;

		$this->login();
	}

	function login() {
		$mysqli = new mysqli('localhost', 'root', '','Phlogger');

		$query = 'SELECT * 
					FROM user
					WHERE user.username = "$username"
					and user.password = "$password"
				 ';

		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();

		while ( $row = $result->fetch_assoc() ) {
			$this->user[] = new user($row['id'], $row['Username'], $row['Password'], 
                                   $row['Rank']);
		}

	}
}