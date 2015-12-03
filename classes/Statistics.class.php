<?php

class Statistics {
        private $qNumberOfComments     = 'SELECT count(*) as total_amount_of_comments FROM comment';
        private $qNumberOfPosts        = 'SELECT count(post.id) as total_amount_of_posts FROM post';

        private $qAvgAmmountOfComments = '
        SELECT AVG(counts.comments) AS "Average" 
        FROM (Select comment.post as Post_ID, count(*) as "comment" 
              From comment Group By comment.post) as counts RIGHT JOIN post 
                    ON counts.post_ID = post.id
        ';

        private $qTopThreePosts = '
          Select comment.Post as Post_ID, count(*) as "comment"
          From comment
          Group By comment.Post
          ORDER BY comments DESC
          LIMIT 3
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
          $result = $database->query($statQuery) ;
          while ($row = $result->fetch_assoc()) {
            if (isset($row['comment']))
              $ret[] = $row['comment'];
            elseif (isset($row['total_amount_of_posts'])) 
              $ret = $row['total_amount_of_posts']; 
            elseif (isset($row['total_amount_of_comments'])) 
              $ret = $row['total_amount_of_comments']; 
          }
          return $ret;
	}

	function __isset($stats) {
	    return true;
        }
}
