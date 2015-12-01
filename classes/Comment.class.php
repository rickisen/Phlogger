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
}
