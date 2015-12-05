<?php 
class Post{
  private $title, $content, $user, $image, $timestamp, $id, $tags, $comments;

  function __construct($title, $content, $image, $user = 0, $id = 0, $timestamp = "", $tags = array(), $comments = array()){
    $this->title      = $title;
    $this->content    = $content;
    $this->image      = $image;
    $this->user       = $user;
    $this->id         = $id;
    $this->timestamp  = $timestamp;
    $this->tags       = $tags;
  }

  function __get($name){
    return $this->$name;
  }

  function __isset($name) {
    return isset($this->$name);
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
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qAllComments = '
      SELECT *
      FROM comment 
      WHERE comment.post = '.$this->id.'
      ORDER BY Date ASC
    ';

    if( $result = $database->query($qAllComments) ) {
      while ($row = $result->fetch_assoc()) {
        $this->comments[] = new Comment($row['Content'], $row['Signature'], $row['Date'] );
      }
    } else {
      echo "Error when trying to get a comment: ".$database->error;
      return FALSE;
    }

    return $this->comments;
  }

  function storePost($dirtyUser){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // escape the input before upping
    $title     = $database->real_escape_string($this->title);
    $content   = $database->real_escape_string($this->content);
    $image     = $database->real_escape_string($this->image);
    $user      = $database->real_escape_string($dirtyUser);

    $upQuery = 'INSERT INTO post (Content, Image, Author, Title) 
      VALUES (\''.$content.'\', \''.$image.'\',\''.$user.'\', \''.$title.'\' )';

    // send the dml query to the db and save the response
    $response = $database->query($upQuery);

    // lazy error reporting
    if ($response) {
      return TRUE; // it worked!
    } else {
      echo "Error when trying to upload a post: ".$database->error;
      return FALSE;
    }
  }

  function getTags(){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qAllOurTags = '
      SELECT Tag.*
      FROM Tag LEFT JOIN p_Has_t
           ON p_Has_t.tagID = Tag.id
      WHERE p_Has_t.postID = '.$this->id.'
    ';

    if( $result = $database->query($qAllOurTags) ) {
      while ($row = $result->fetch_assoc()) {
        $this->tags[] = new Tag($row['Name'], $row['id']);
      }
    } else {
      echo "Error when trying to get a Tag: ".$database->error;
      return FALSE;
    }

    return $this->tags;
  }
}
