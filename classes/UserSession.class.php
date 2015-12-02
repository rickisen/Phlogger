<?php

class UserSession {
	private $username, $password, $rank;

	function __construct ($username, $password, $rank) {
		$this->username = $username; 
		$this->password = $password;
		$this->rank 	= $rank; 
	}
}