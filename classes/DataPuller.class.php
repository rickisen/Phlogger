<?php
class DataPuller{
  private  $threePosts = array(), $groupedPosts = array(), $posts = array(), $statistics, $searchResults ;

  // query to get all posts
  private $qAllPosts = 'SELECT * FROM post ORDER BY Timestamp DESC';

  function __construct() { 
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // Construct all posts
    // Ignores tags for now
    $result = $database->query($this->qAllPosts);
    while ($row = $result->fetch_assoc()) {
      $this->posts[] = new Post($row['Title'], $row['Content'], $row['Author'], 
                                   $row['Timestamp'], $row['id']);
    }

    // Divide the posts into groups of 12, so that 
    // we don't fill the landingpage
    $limit = 12 ;
    $step = 0 ;
    $group = 0 ;
    foreach ( $this->posts as $post )  {
      if ($step++ < $limit ){
        $this->groupedPosts[$group][] = $post;
      } else {
        $limit = 0;
        ++$group;
        $this->groupedPosts[$group][] = $post;
      }
    }

    // put the 3 latest posts into an easily obtainable array
    $i = 0;
    while ($i < 3){
      $threePosts[] = $posts[$i++];
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

  function search($searchFor){
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $searchQuery = '
      SELECT * FROM post 
      WHERE  content LIKE "%'.$searchFor.'%" OR content LIKE "%'.$searchFor.'" 
          OR content LIKE "'.$searchFor.'%"  OR content LIKE "'.$searchFor.'" 
          OR title   LIKE "%'.$searchFor.'%" OR title   LIKE "%'.$searchFor.'" 
          OR title   LIKE "'.$searchFor.'%"  OR title   LIKE "'.$searchFor.'" 
    ';

    if ( $result = $database->query($searchQuery)){
      while ($row = $result->fetch_assoc()) {
        $this->searchResults[] = new Post($row['Title'], $row['Content'], $row['Author'], $row['Timestamp'], $row['id'] );
      }
    } else {
      echo 'Something went wrong with the search: '.$database->error ;
      return FALSE;
    }
    return TRUE;
  }
}
