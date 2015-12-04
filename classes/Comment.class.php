<?php

class Comment{
  private $content, $signature, $date;

  function __construct($content, $signature, $date){
    $this->content    = $content;
    $this->signature  = $signature;
    $this->date       = $date;
  }

  function __get($name){
    return $this->$name;
  }

  function __isset($name){
    return isset($this->$name);
  }

  function storeComment($postID){
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // escape the input before upping
    $content    = $database->real_escape_string($this->content);
    $signature  = $database->real_escape_string($this->signature);
    $postID     = $database->real_escape_string($postID);

    $upQuery = 'INSERT INTO comment (Content, Signature, post ) 
      VALUES (\''.$content.'\', \''.$signature.'\',\''.$postID.'\')';

    // send the dml query to the db and save the responce
    $response = $database->query($upQuery);

    // lazy error reporting
    if ($response) {
      return TRUE; // it worked!
    } else {
      echo "Error when trying to upload a Comment: ".$database->error;
      return FALSE;
    }
  }
}
