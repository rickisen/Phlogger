<?php 

require_once 'classes/UserSession.class.php';
require_once 'classes/DataPuller.class.php';
require_once 'classes/Post.class.php';
require_once 'classes/Comment.class.php';
require_once 'classes/PagePrinter.class.php';
require_once 'classes/Statistics.class.php';


// Start a session 
session_start();

// grabs the posts,comments,tags from the data 
// base and makes objects from them
$dataBase = new DataPuller();

if (isset($_SESSION['user']) && !$_SESSION['user']->isLoggedIn) {
	unset($_SESSION['user']);
}
// Check if we got a login request
if (isset($_POST['username']) && isset($_POST['password'])){ //removed "!isset $_SESSION"
  $_SESSION['user'] = new UserSession($_POST['username'], $_POST['password']);  // escaped in the user class constructor
} elseif ( isset($_POST['logout']) && isset($_SESSION['user'])) {
  unset($_SESSION['user']);
}

if (isset($_GET['home']) && $_GET['home'] == 'true') {
	$loadview = 'landingpage';
	$readmore = "";
}

// Different default pages load depending on if we are loged in
//
// What happens if someone manualy puts in a get request for dash?!!
//
if ( isset($_SESSION['user']) && $_SESSION['user']->isLoggedIn  ) {
  $loadview = 'dash';
} else {
  $loadview = 'landingpage';
}

// But if we get an explicit request we load that instead
if (isset($_GET['loadview'])) {
	$loadview = $_GET['loadview'];
}

$readmore = "";
if (isset($_GET['readmore'])) {
	$readmore = $_GET['readmore'];
}

if (isset($_GET['tags'])) {
	$loadview = $_GET['tags'];
}

// Check for search inputs and render search result view
if (isset($_GET['search'])) {
	$searchInput = $_GET['search'];
	$dataBase -> search($searchInput);
	$loadview = 'searchresults';
}

// Check if all "create post"-fields are filled, connect to user ID, call storePost
if ( isset ($_POST['postTitle']) && isset($_POST['postContent']) && isset($_POST['postImage']) ) {
	$blogPost = new Post($_POST['postTitle'], $_POST['postContent'], $_POST['postImage'], $_SESSION['user']->id);
	$blogPost->storePost();
}

// create and render the twig-templates 
if (isset($_SESSION['user'])){
  $page = new PagePrinter(['user' => $_SESSION['user'], 'dataBase' => $dataBase, 'loadview' => $loadview]);
}
else {
	$page = new PagePrinter(['dataBase' => $dataBase, 'loadview' => $loadview, 'readmore' => $readmore ]);
}

echo $page->render();
