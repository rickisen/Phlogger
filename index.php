<?php 
require_once 'classes/UserSession.class.php';
require_once 'classes/DataPuller.class.php';
require_once 'classes/Post.class.php';
require_once 'classes/Tag.class.php';
require_once 'classes/Comment.class.php';
require_once 'classes/PagePrinter.class.php';
require_once 'classes/Statistics.class.php';

// Start a session 
session_start();

// grabs posts,comments,tags from the database and makes objects from them
$dataBase = new DataPuller();

// HANDLE USERS ================================================================================

// Check if we got a login request
if (isset($_POST['username']) && isset($_POST['password'])){
        // Then we create and store a new user session,
        $_SESSION['user'] = new UserSession($_POST['username'], $_POST['password']);  // strings escaped in the user class constructor
} 

// Remove a UserSession if the user tried to log in with bad credentials, or if we got a logout request
if (isset($_SESSION['user']) && !$_SESSION['user']->isLoggedIn || isset($_POST['logout'])) {
        unset($_SESSION['user']);
}

// HANDLE POST REQUESTS ================================================================================

// Check if someone is trying to submit a post, and if he is logged in, let him.
if ( isset($_POST['postTitle']) && !empty($_POST['postTitle']) && isset($_POST['postContent']) && !empty($_POST['postContent']) && isset($_POST['postImage']) && isset($_SESSION['user']) && $_SESSION['user']->isLoggedIn ) {
        $blogPost = new Post($_POST['postTitle'], $_POST['postContent'], $_POST['postImage'], $_SESSION['user']->id); 
        $blogPost->SetTagsFromString($_POST['postTags']);
        $blogPost->storePost($_SESSION['user']->id); //strings escaped in object
}

// Check if we got a comment, and put it on the corresponding post
if ( isset($_POST['commentContent']) && !empty($_POST['commentContent']) && isset($_POST['commentSignature']) && !empty($_POST['commentSignature']) && isset($_POST['commentParent'])) {
        $newComment = new Comment($_POST['commentContent'], $_POST['commentSignature']);
        $newComment->storeComment($_POST['commentParent']);
}

// HANDLE PAGE LOADS AND VARIABLES ================================================================================

if (isset($_GET['loadview'])) {
        $loadview = $_GET['loadview'];
} 

if (isset($_POST['loadview'])){
        $loadview = $_POST['loadview'];
}

if (isset($_GET['readmore'])) {
        $readmore = $_GET['readmore'];
        $dataBase->getSinglePost($_GET['readmore']);
}

if (isset($_GET['tag'])) {
        $dataBase->getPostsFromTag($_GET['tag']);
}

if (isset($_GET['search'])) {
        $dataBase->search($_GET['search']);
}

// RENDER THE PAGE ================================================================================
if (!isset($readmore)) $readmore = 0 ;
if (!isset($loadview)) $loadview = "landingpage" ;
if (!isset($_SESSION['user'])) {
  $page = new PagePrinter(['dataBase' => $dataBase, 'loadview' => $loadview, 'readmore' => $readmore ]);
} else {
  $page = new PagePrinter(['dataBase' => $dataBase, 'user' => $_SESSION['user'], 'loadview' => $loadview, 'readmore' => $readmore ]);
}

echo $page->render();
