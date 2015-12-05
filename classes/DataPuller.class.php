<?php
class DataPuller{
  private $threePosts, $groupedPosts, $tags, $posts, $statistics, $searchResults ;
  private $database ; // where our connection to the database will live

  function __get($x){
    return $this->$x;
  }

  function __isset($x){
    return isset($this->$x);
  }

  function __construct() { 
    // Create the connection to our db
    $this->database = new mysqli('localhost', 'root', '','Phlogger');
    
    $this->getTags();
    $this->getAllPosts();
    $this->getGroupedPosts();
    $this->getThreePosts();

    // Create a statistics object
    $this->statistics = new Statistics();
  }

  // function that returns the posts made this month or x months back in time
  function getMonthsPosts($monthsBack = 0){
    if ($monthsBack < 1){
      $qOnePost = ' 
        SELECT *
        FROM   post
        WHERE  post.Timestamp BETWEEN 
                 DATE_FORMAT(NOW()) AND 
                 DATE_FORMAT(LAST_DAY(NOW() - INTERVAL '.$monthsBack.' MONTH), "%Y-%m-%d 23:59:59")
        ORDER BY post.Timestamp DESC
      ';
    } else {
      $qOnePost = ' 
        SELECT *
        FROM   post
        WHERE  post.Timestamp BETWEEN 
                 DATE_FORMAT(NOW() - INTERVAL '.$monthsBack.' MONTH, "%Y-%m-01 00:00:00") AND 
                 DATE_FORMAT(LAST_DAY(NOW() - INTERVAL '.$monthsBack.' MONTH), "%Y-%m-%d 23:59:59")
        ORDER BY post.Timestamp DESC
      ';
    }

    $ret = array();
    // Construct the posts
    if( $result = $this->database->query($qOnePost)){
      while ($row = $result->fetch_assoc()) {
        $ret[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                          $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get posts from month $tagID: ".$this->database->error;
    }
    return $ret;
  }

  function getPostsFromTag($tagID){
    $qOnePost = ' 
      SELECT post.* 
      FROM   post LEFT JOIN p_Has_t
        ON   p_Has_t.tagID = '.$tagID.'
      ORDER BY post.Timestamp DESC
    ';
    $ret = array();
    // Construct the posts
    if( $result = $this->database->query($qOnePost)){
      while ($row = $result->fetch_assoc()) {
        $ret[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                        $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get posts from tag $tagID: ".$this->database->error;
    }
    return $ret;
  }

  function getPost($postID){
    $qOnePost = ' SELECT * FROM post WHERE post.id = '.$postID.' LIMIT 1';

    // Construct all posts
    if( $result = $this->database->query($qOnePost)){
      while ($row = $result->fetch_assoc()) {
        $ret = new Post($row['Title'],    $row['Content'], $row['Image'], 
                        $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get Post $postID: ".$this->database->error;
    }
    return $ret;
  }

  function getAllTags(){
    $qAllTags = 'SELECT * FROM Tag ORDER BY id DESC';

    // Get all tags
    if( $result = $this->database->query($qAllTags)){
      while ($row = $result->fetch_assoc()) {
        $this->tags[] = new Tag( $row['Name'], $row['id'] );
      }
    } else {
      echo "Failed to get Tags: ".$this->database->error;
    }
  }

  function getAllPosts(){
    // query to get all posts
    $qAllPosts = 'SELECT * FROM post join user on post.Author = user.id ORDER BY Timestamp DESC';

    // Construct all posts
    if( $result = $this->database->query($qAllPosts)){
      while ($row = $result->fetch_assoc()) {
        $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                  $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get Posts: ".$this->database->error;
    }
  }

  function getGroupedPosts(){
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
    return $this->groupedPosts;
  }

  function getThreePosts(){
    // put the 3 latest posts into an easily obtainable array
    $i = 0;
    while ($i < 3){
      $this->threePosts[] = $this->posts[$i++];
    }
    return $this->threePosts;
  }

  function search($searchFor){
    $searchQuery = '
      SELECT * FROM post join user on post.Author = user.id
      WHERE  content LIKE "%'.$searchFor.'%" OR content LIKE "%'.$searchFor.'" 
          OR content LIKE "'.$searchFor.'%"  OR content LIKE "'.$searchFor.'" 
          OR title   LIKE "%'.$searchFor.'%" OR title   LIKE "%'.$searchFor.'" 
          OR title   LIKE "'.$searchFor.'%"  OR title   LIKE "'.$searchFor.'" 
      ORDER BY Timestamp DESC 
    ';

    if ( $result = $this->database->query($searchQuery)){
      while ($row = $result->fetch_assoc()) {
        $this->searchResults[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                          $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo 'Something went wrong with the search: '.$this->database->error ;
      return FALSE;
    }
    return TRUE;
  }
}
