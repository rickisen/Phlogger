<?php
class DataPuller{
  private $posts = array(), $statistics;

  // query to get all posts
  private $qAllPosts = 'SELECT * FROM post ORDER BY Timestamp';

  function __construct() { 
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // Construct all posts
    // Ignores tags for now
    $result = $database->query($this->qAllPosts);
    while ($row = $result->fetch_assoc()) {
      $this->posts[] = new Post($row['Title'], $row['Content'], $row['Author'], 
                                   $row['Timestamp'], $row['id']);
    }

    // Create a statistics object
    $this->statistics = new Statistics();
  }

  function __get($x){
    return $this->$x;
  }

  function __isset($x){
    return isset($this->$x);
  }

}
