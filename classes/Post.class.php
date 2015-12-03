<?php 
class Post{
  private $title, $content, $author, $timestamp, $id, $tags = array(), $comments = array();


  function __construct($title, $content, $author, $timestamp, $id, $tags = array(), $comments = array()){
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
    if (isset($this->$name)){
        return true;
      } else {
        return false;
      }
    }

  // goes through all the characters in 
  // the content and returns a maximum of 150 characters
  function summary(){
    $ret = "";
    for ($i = 0 ; $i < 150 && isset($this->content[$i])  ; $i++){
      $ret .= $this->content[$i];  
    }
    
    // add epilepsy
    if ( strlen($ret) > 149 )
      $ret .= "...";

    return $ret;
  }

  function getComments(){
    $database = new mysqli('localhost', 'root', '','Phlogger');
    $qAllComments = '
      SELECT *
      FROM comment 
      WHERE comment.post = '.$id.'
    ';

    $result = $database->query($qAllComments);
    while ($row = $result->fetch_assoc()) {
      $this->comments[] = new Comment($row['Content'], $row['Signature'], $row['Date'] );
    }

    return $this->comments;
  }
}
