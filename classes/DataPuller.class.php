<?php
class DataPuller{
  private  $threePosts = array(), $groupedPosts = array(), $tags = array() ,$posts = array(), $statistics, $searchResults ;

  // query to get all posts
  private $qAllPosts = 'SELECT * FROM post join user on post.Author = user.id ORDER BY Timestamp DESC';

  // All tags
  private $qAllTags = 'SELECT * FROM Tag ORDER BY id DESC';

  function __construct() { 
    $database = new mysqli('localhost', 'root', '','Phlogger');
    
    // Get all tags
    $result = $database->query($this->qAllTags);
    while ($row = $result->fetch_assoc()) {
      $this->tags[] = new Tag( $row['Name'], $row['id'] );
    }

    // Construct all posts
    // Ignores tags for now
    $result = $database->query($this->qAllPosts);
    while ($row = $result->fetch_assoc()) {
      $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                $row['Username'], $row['id'],      $row['Timestamp'] );
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
      $threePosts[] = $this->posts[$i++];
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
      SELECT * FROM post join user on post.Author = user.id
      WHERE  content LIKE "%'.$searchFor.'%" OR content LIKE "%'.$searchFor.'" 
          OR content LIKE "'.$searchFor.'%"  OR content LIKE "'.$searchFor.'" 
          OR title   LIKE "%'.$searchFor.'%" OR title   LIKE "%'.$searchFor.'" 
          OR title   LIKE "'.$searchFor.'%"  OR title   LIKE "'.$searchFor.'" 
      ORDER BY Timestamp DESC 
    ';

    if ( $result = $database->query($searchQuery)){
      while ($row = $result->fetch_assoc()) {
        $this->searchResults[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                          $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo 'Something went wrong with the search: '.$database->error ;
      return FALSE;
    }
    return TRUE;
  }
}
