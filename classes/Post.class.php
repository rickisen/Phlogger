<?php 
class Post{
  private $title, $content, $author, $timestamp, $id, $tags = array(), $comments = array();

  function __construct($title, $content, $author, $timestamp, $id, $tags, $comments){
    $this->title      = $title;
    $this->content    = $content;
    $this->author     = $author;
    $this->timestamp  = $timestamp;
    $this->id         = $id;
    $this->tags       = $tags;

    foreach ($comments as $comment)
      $this->comments[] = new Comment(
      $comment['content'],
      $comment['signature'],
      $comment['date']
      );
  }

  function __get($name){
    return $this->$name;
  }

  function __isset($name) {
    if ( isset ($this->$name) ) {
      return true; 
    } else {
      return false;
    }
  }

}
