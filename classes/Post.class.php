<?php 
class Post{
  private $title, $content, $user, $image, $timestamp, $id, $tags, $comments;

  function __construct($title, $content, $image, $user = 0, $id = 0, $timestamp = ""){
    $this->title      = $title;
    $this->content    = $content;
    $this->image      = $image;
    $this->user       = $user;
    $this->id         = $id;
    $this->timestamp  = $timestamp;
  }

  function __get($x){
    return $this->$x;
  }

  function __isset($x) {
    return isset($this->$x);
  }

  // goes through all the characters in 
  // the content and returns a maximum of 150 characters
  function summary(){
    $ret = "";
    for ($i = 0 ; $i < 150 && isset($this->content[$i])  ; $i++){
      $ret .= $this->content[$i];  
    }
    
    // add dots
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

    $qInsQuery = 'INSERT INTO post (Content, Image, Author, Title) 
      VALUES (\''.$content.'\', \''.$image.'\',\''.$user.'\', \''.$title.'\' )';

    // send the dml query to the db, save the response, and the newly generated id
    $response = $database->query($qInsQuery);
    $this->id = $database->insert_id;

    // connect the tags to our post in the database
    foreach ($this->tags as $tag){
      $tag->connectTag($this->id);
    }

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

  function SetTagsFromString($tagString){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');
    // explode the input string into an array 
    // and loop through it and trim surrounding spaces 
    // and escape hostile input
    $tagNames = explode(',', $tagString);
    foreach ( $tagNames as $key => $tagName ){
      $tagNames[$key] = $database->real_escape_string(trim($tagName));
    } 

    // see if we already have a tag in the database with the same name
    // and if so put that tag into our this->tags array.
    foreach ( $tagNames as $key => $tagName ){
      $qTagIdFromName = ' SELECT Tag.* FROM Tag WHERE Tag.Name = "'.$tagName.'"';

      if($result = $database->query($qTagIdFromName) ) {
        if ( $row  = $result->fetch_assoc()) {
          $this->tags[] = new Tag($row['Name'], $row['id']); 
        } else { // Otherwise we upload the tag to the db
          // upload a new tag to db
          if(!$database->query('INSERT INTO Tag (Name) VALUES("'.$tagName.'")'))
            echo 'Something went wrong whe trying to upload a tag'.$database->error;
          
          // Get the new Tag, (and its newly created id 
          // which we need to construct a tag)
          if( $result = $database->query($qTagIdFromName)){
            if ($row  = $result->fetch_assoc()) {
              $this->tags[] = new Tag($row['Name'], $row['id']); 
              } else echo 'new tag not found';
          } else {
            echo 'Error when trying to get a newly made tag'.$database->error;
          }
        }
      }
    }
  }
}
