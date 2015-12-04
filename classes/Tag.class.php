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
}
