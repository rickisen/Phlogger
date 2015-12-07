<?php
class DataPuller{
  private $groupedPosts, $activeTag, $tags, $posts, $statistics, $searchInput = "", $alltags = FALSE; 

  function __get($x){
    return $this->$x;
  }

  function __isset($x){
    return isset($this->$x);
  }

  function __construct() { 
    // Create a statistics object
    $this->statistics = new Statistics();
  }

  // function that returns the posts made this month or x months back in time
  function getMonthsPosts($monthsBack = 0){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    if ($monthsBack < 1){
      $qMonthPosts = ' 
        SELECT post.*, UCASE(user.Username) AS "Username"
        FROM   post LEFT JOIN user
                ON post.Author = user.id
        WHERE  post.Timestamp BETWEEN 
                 DATE_FORMAT(NOW(), "%Y-%m-01 01:01:01") AND 
                 DATE_FORMAT(NOW(), "%Y-%m-%d %H:%i:%s")
        ORDER BY post.Timestamp DESC
      ';
    } else {
      $qMonthPosts = ' 
        SELECT post.*, UCASE(user.Username) AS "Username"
        FROM   post LEFT JOIN user
                ON post.Author = user.id
        WHERE  post.Timestamp BETWEEN 
                 DATE_FORMAT(NOW() - INTERVAL '.$monthsBack.' MONTH, "%Y-%m-01 00:00:00") AND 
                 DATE_FORMAT(LAST_DAY(NOW() - INTERVAL '.$monthsBack.' MONTH), "%Y-%m-%d 23:59:59")
        ORDER BY post.Timestamp DESC
      ';
    }

    $this->posts = array();
    // Construct the posts
    if( $result = $database->query($qMonthPosts)){
      while ($row = $result->fetch_assoc()) {
        $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                  $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get posts from month $monthsBack: ".$database->error;
      return FALSE ;
    }
    return TRUE;
  }

  function getPostsFromTag($tagID){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qPostFromTAg = ' 
      SELECT DISTINCT post.*, UCASE(user.Username) AS "Username", Tag.Name AS "TagName" 
      FROM   post LEFT JOIN p_Has_t
               ON p_Has_t.postID = post.id
             LEFT JOIN user
               ON post.Author = user.id
             LEFT JOIN Tag
               ON Tag.id = '.$tagID.'
      WHERE  p_Has_t.tagID = '.$tagID.'
      ORDER BY post.Timestamp DESC
    ';

    // Get the relevant posts from db and construct them
    if( $result = $database->query($qPostFromTAg)){
      $this->posts = array();
      while ($row = $result->fetch_assoc()) {
        $this->posts[]   = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                    $row['Username'], $row['id'],      $row['Timestamp'] );
        $this->activeTag = new Tag( $row['TagName'], $tagID);
      }
    } else {
      echo "Failed to get posts from tag $tagID: ".$database->error;
      return FALSE;
    }
    return TRUE;
  }

  function getSinglePost($postID){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qOnePost = ' SELECT post.*, UCASE(user.Username) AS "Username" 
                  FROM   post join user 
                    ON   post.Author = user.id 
                  WHERE  post.id = '.$postID.' LIMIT 1';

    // Construct the post
    if( $result = $database->query($qOnePost)){
      $this->posts = array();
      while ($row = $result->fetch_assoc()) { // still loop through in case we get more rows
        $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                  $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get Post $postID: ".$database->error;
      return FALSE;
    }
    $this->posts[0]->getTags();// make sure the tags are displayable
    return TRUE;
  }

  function getAllTags(){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qAllTags = 'SELECT * FROM Tag ORDER BY id DESC ';

    // Get all tags
    if( $result = $database->query($qAllTags)){
      while ($row = $result->fetch_assoc()) {
        $this->tags[] = new Tag( $row['Name'], $row['id'] );
      }
    } else {
      echo "Failed to get Tags: ".$database->error;
      return FALSE;
    }
    $this->alltags = TRUE;
    return TRUE;
  }

  function getLimitedTags(){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $qLTags = 'SELECT * FROM Tag ORDER BY id DESC LIMIT 20';

    // Get all tags
    if( $result = $database->query($qLTags)){
      while ($row = $result->fetch_assoc()) {
        $this->tags[] = new Tag( $row['Name'], $row['id'] );
      }
    } else {
      echo "Failed to get Tags: ".$database->error;
      return FALSE;
    }
    $this->alltags = FALSE;
    return TRUE;
  }

  function getTwelPosts(){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // query to get the 12 latest posts
    $qTwelePosts = ' 
                  SELECT post.*, UCASE(user.Username) AS Username
                  FROM post LEFT JOIN user 
                    ON post.Author = user.id 
                  ORDER BY Timestamp DESC
                  LIMIT 12
                  ';

    // Construct all posts
    if( $result = $database->query($qTwelePosts)){
      while ($row = $result->fetch_assoc()) {
        $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                  $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get Posts: ".$database->error;
      return FALSE;
    }
    return TRUE;
  }

  function getAllPosts(){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // query to get all posts
    $qAllPosts = 'SELECT post.*, UCASE(user.Username) AS Username
                  FROM post LEFT JOIN user 
                    ON post.Author = user.id 
                  ORDER BY Timestamp DESC
                  ';

    // Construct all posts
    if( $result = $database->query($qAllPosts)){
      while ($row = $result->fetch_assoc()) {
        $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                  $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo "Failed to get Posts: ".$database->error;
      return FALSE;
    }
    return TRUE;
  }

  function groupPosts(){
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
    return isset($this->groupedPosts[0][0]);
  }

  function search($searchFor){
    // Create the connection to our db
    $database = new mysqli('localhost', 'root', '','Phlogger');

    $searchQuery = '
      SELECT post.*, user.Username FROM post join user on post.Author = user.id
      WHERE  content LIKE "%'.$searchFor.'%" OR content LIKE "%'.$searchFor.'" 
          OR content LIKE "'.$searchFor.'%"  OR content LIKE "'.$searchFor.'" 
          OR title   LIKE "%'.$searchFor.'%" OR title   LIKE "%'.$searchFor.'" 
          OR title   LIKE "'.$searchFor.'%"  OR title   LIKE "'.$searchFor.'" 
      ORDER BY Timestamp DESC 
    ';

    if ( $result = $database->query($searchQuery)){
      $this->posts = array(); // we got a result, so we clear the posts array
      while ($row = $result->fetch_assoc()) {
        $this->posts[] = new Post($row['Title'],    $row['Content'], $row['Image'], 
                                  $row['Username'], $row['id'],      $row['Timestamp'] );
      }
    } else {
      echo 'Something went wrong with the search: '.$database->error ;
      return FALSE;
    }
    $this->searchInput = $searchFor;
    return TRUE;
  }
}
