<?php 

require_once 'classes/UserSession.class.php';
require_once 'classes/Post.class.php';
require_once 'classes/Comment.class.php';
require_once 'classes/PagePrinter.class.php';
require_once 'classes/Statistics.class.php';

// Start a session 
session_start();

// Check if we got a login request
if (isset($_POST['username']) && isset($_POST['password']) && !isset($_SESSION['user'])){
  $_SESSION['user'] = new UserSession($_POST['username'], $_POST['password']);  // escaped in the user class constructor
} elseif (isset($_POST['logout'])) {
  unsset($_SESSION['user']);
}

// Different default pages load depending on if we are loged in
if ( isset($_SESSION['user']) ){
  $loadview = 'dash';
} else {
  $loadview = 'landingpage';
}
// But if we get a explicit request we load that instead
if (isset($_GET['loadview'])) {
	$loadview = $_GET['loadview'];
}

// grabs the posts,comments,tags from the data 
// base and makes objects from them
$dataBase = new DataPuller();

// create and render the twig-templates 
$page = new PagePrinter( ['user' => $_SESSION['user'], 'dataBase' => $dataBase ]);
echo $page->render();

//echo $loadview;
