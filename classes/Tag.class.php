<?php
class Tag{
  private $name, $id ;

  function __construct($name, $id){
    $this->name = $name;
    $this->id = $id;
  }

  function __isset($x){
    return isset($this->$x);
  }

  function __get($x){
    return $this->$x;
  }

  function connectTag($parrentID){
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qConnectThisTagToParrent = 'INSERT INTO p_Has_t (tagID, postID) VALUES ("'.$this->id.'","'.$parrentID.'")' ;
    if( !$results = $database->query($qConnectThisTagToParrent)){
      echo 'Something went wrong when trying to connect a tag to a post'.$database->error;
    }

  }
}
