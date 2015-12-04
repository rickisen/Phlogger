<?php

class UserSession {
  private $username, $password, $id, $rank, $isLoggedIn = FALSE, $mysqli;

  function __construct ($username, $password) {
          $this->mysqli = new mysqli('localhost', 'root', '','Phlogger');

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
                      $this->id = $row['id'];
                      $this->isLoggedIn = TRUE;
          } 
      }	
  }

  function __get($x){
    if ( $x != 'password' )
      return $this->$x;
    else {
      echo 'Hacking damages your health';
      return FALSE;
    }
  }

  function __isset($x){
    return isset($this->$x);
  }

}
