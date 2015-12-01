<?php 
class Post{
  private $title, $content, $author, $timestamp, $id, $tags = array() ;

  function __construct($title, $content, $author, $timestamp, $id, $tags){
    $this->title      = $title;
    $this->content    = $content;
    $this->author     = $author;
    $this->timestamp  = $timestamp;
    $this->id         = $id;
    $this->tags       = $tags;
  }

}
