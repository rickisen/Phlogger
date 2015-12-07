<?php
class Statistics {
        private $qNumberOfComments     = 'SELECT count(*) as total_amount_of_comments FROM comment';
        private $qNumberOfPosts        = 'SELECT count(post.id) as total_amount_of_posts FROM post';

        private $qAvgAmmountOfComments = '
        SELECT AVG(counts.comment) AS "avarage" 
        FROM (Select comment.post as Post_ID, count(*) as "comment" 
              From comment Group By comment.post) as counts RIGHT JOIN post 
                    ON counts.post_ID = post.id
        ';

        private $qTopThreePosts = '
        Select group_concat(post.Title SEPARATOR " | ") as "topThreeList"
        FROM      (Select comment.Post as Post_ID, count(*) as "comment"
                  From comment
                  Group By comment.Post
                  ORDER BY comment DESC
                  LIMIT 3) comment_amount JOIN post
                      ON comment_amount.Post_ID = post.id

        ';

	function __get($stat) {
          // depending on what the caller tried to get, we change the query
          switch ($stat){
          case 'count_total_comments' :
            $statQuery = $this->qNumberOfComments;
          break;
          case 'count_total_posts' :
            $statQuery = $this->qNumberOfPosts;
          break;
          case 'avg_comment_post' :
            $statQuery = $this->qAvgAmmountOfComments;
          break;
          case 'three_most_commented_posts' :
            $statQuery = $this->qTopThreePosts;
          break;
          default:
            return $this->$stat;
          break;
          }

          $database = new mysqli('localhost', 'root', '','Phlogger');
          $result = $database->query($statQuery);

          while ($row = $result->fetch_assoc()) {
              if (isset($row['avarage']))
                return $row['avarage'];

              elseif (isset($row['topThreeList']))
                return explode('|', $row['topThreeList']);

              elseif (isset($row['total_amount_of_posts'])) 
                return $row['total_amount_of_posts']; 

              elseif (isset($row['total_amount_of_comments'])) 
                return $row['total_amount_of_comments']; 
            }

          return "Data retrieval failed"; 
	}

	function __isset($stats) {
	    return true;
        }
}
