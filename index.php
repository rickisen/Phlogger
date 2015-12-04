<?php 

require_once 'classes/UserSession.class.php';
require_once 'classes/DataPuller.class.php';
require_once 'classes/Post.class.php';
require_once 'classes/Comment.class.php';
require_once 'classes/PagePrinter.class.php';
require_once 'classes/Statistics.class.php';

// Start a session 
session_start();

// Check if we got a login request
if (isset($_POST['username']) && isset($_POST['password'])){ //removed "!isset $_SESSION"
  $_SESSION['user'] = new UserSession($_POST['username'], $_POST['password']);  // escaped in the user class constructor
} elseif ( isset($_POST['logout']) && isset($_SESSION['user'])) {
  unset($_SESSION['user']);
}

// Check for search inputs
if (isset($_GET['search'])) {
	$searchInput = (isset($_GET['search']));
}

// Different default pages load depending on if we are loged in
//
// What happens if someone manualy puts in a get request for dash?!!
//
if ( isset($_SESSION['user']) && $_SESSION['user']->isLoggedIn  ) {
  $loadview = 'dashboard';
} else {
  $loadview = 'landingpage';
}
// But if we get an explicit request we load that instead
if (isset($_GET['loadview'])) {
	$loadview = $_GET['loadview'];
}

// grabs the posts,comments,tags from the data 
// base and makes objects from them
$dataBase = new DataPuller();

// create and render the twig-templates 
if (isset($_SESSION['user'])){
  $page = new PagePrinter(['user' => $_SESSION['user'], 'dataBase' => $dataBase, 'loadview' => $loadview]);
} else {
  $page = new PagePrinter(['dataBase' => $dataBase, 'loadview' => $loadview ]);
}

echo $page->render();
