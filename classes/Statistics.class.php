<?php

class Statistics {
	private $count_total_comments, $count_total_posts, $avg_comment_post, $three_most_commented_posts;

	function __construct($count_total_comments, $count_total_posts, $avg_comment_post, $three_most_commented_posts) {
		$this->count_total_comments 	  = $count_total_comments;
		$this->count_total_posts 		  = $count_total_posts;
		$this->avg_comment_post 		  = $three_most_commented_posts;
		$this->three_most_commented_posts = $three_most_commented_posts;
	}

	function __get($stats) {
		$this->$stats;
	}

	function __isset($stats) {
	    if (isset($this->$stats)){
	        return true;
	      } else {
	        return false;
	      }
	    }
}