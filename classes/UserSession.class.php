<?php

class UserSession {
	private $username, $password, $rank;

	function __construct ($username, $password) {
		$this->username = $username; 
		$this->password = $password;

		$this->login();
	}

	function login() {
		
	}
}