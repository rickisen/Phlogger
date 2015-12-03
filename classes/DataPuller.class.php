<?php
class DataPuller{
  private $posts = array(), $statistics;

  // querys we will send
  private $qAllPosts = 'SELECT * FROM post ORDER BY Timestamp';

  private $statQueries = array(
    'qNumberOfComments'     => 'SELECT count(*) as total_amount_of_comments FROM comment',
    'qNumberOfPosts'        => 'SELECT count(post.id) as total_amount_of_posts FROM post',
    'qAvgAmmountOfComments' => 
    '
    SELECT AVG(counts.comments) AS "Average" 
    FROM (Select comment.post as Post_ID, count(*) as "comments" 
          From comment Group By comment.post) as counts RIGHT JOIN post 
                ON counts.post_ID = post.id
    ',
  'qTopThreePosts'     => 
    '
      Select comment.Post as Post_ID, count(*) as "comments"
      From comment
      Group By comment.Post
      ORDER BY comments DESC
      LIMIT 3
    ' 
    );

  function __construct() { 
    $database = new mysqli('localhost', 'root', '','Phlogger');

    // Construct all posts
    // Ignores tags for now
    $result = $database->query($this->qAllPosts);
    while ($row = $result->fetch_assoc()) {
      $this->posts[] = new Post($row['Title'], $row['Content'], $row['Author'], 
                                   $row['Timestamp'], $row['id']);
    }

    // Construct statistics
    // first fetch all the data, safe guard incase we get arrays
    $stats = array();
    foreach ($this->statQueries as $statQueryName => $statQuery ){ 
      if ( $result = $database->query($statQuery) ){
        while ($val = $result->fetch_assoc()){
          $stats[$statQueryName] = $statQuery;
        }
      }
    } // Construct it
    $this->statistics = new Statistics($stats['qNumberOfComments'], 
                                       $stats['qNumberOfPosts'], 
                                       $stats['qAvgAmmountOfComments'], 
                                       $stats['qTopThreePosts']);
  }

}
