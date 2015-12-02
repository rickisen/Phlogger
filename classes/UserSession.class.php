<?php

class UserSession {
	private $username, $password, $rank, $isLoggedIn = FALSE, $mysqli;

	function __construct ($username, $password) {
		$this->mysqli = new mysqli('localhost', 'root', '','Phlogger');
                // stripslashes: Returns a string with backslashes stripped off. 
                // (\' becomes ' and so on.) Double backslashes (\\) 
                // are made into a single backslash (\).
		/* $username = $this->mysqli->stripslashes($username); */  
		/* $password = $this->mysqli->stripslashes($password); */

		$this->username = $this->mysqli->real_escape_string($username);
		$this->password = $this->mysqli->real_escape_string($password);

		$this->login();
	}

	function login() {
		$query = 'SELECT Username, password, Rank FROM user';

		$result = $this->mysqli->query($query);
                                
                while ( $row = $result->fetch_assoc() ) {
                        if ($row['Username'] == $this->username && $row['password'] == $this->password){
                                $this->rank = $row['Rank'];
                                $this->isLoggedIn = TRUE;
                        } 
                }	
	}

        function __get($x){
          return $this->$x;
        }

}
