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

// grabs the posts,comments,tags from the data 
// base and makes objects from them
$dataBase = new DataPuller();

// Check if we got a login request
if (isset($_POST['username']) && isset($_POST['password'])){
        // Then we create and store a new user session,
        $_SESSION['user'] = new UserSession($_POST['username'], $_POST['password']);  // strings escaped in the user class constructor
} 

// Remove a UserSession if the user tried to log in with bad credentials, or if we got a logout request
if (isset($_SESSION['user']) && !$_SESSION['user']->isLoggedIn || isset($_POST['logout'])) {
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

if (isset($_GET['readmore'])) {
        $readmore = $_GET['readmore'];
        $dataBase->getSinglePost($_GET['readmore']);
        $loadview = 'post';
}

if (isset($_GET['tags'])) {
        $loadTag = $_GET['tags'];
        $loadview = 'tagresults';
}

// Check for search inputs and redirect to search result view
if (isset($_GET['search'])) {
        $searchInput = $_GET['search'];
        $dataBase -> search($searchInput);
        $loadview = 'searchresults';
}

// Check if someone is trying to submit a post, and if he is logged in, let him.
if ( isset($_POST['postTitle']) && isset($_POST['postContent']) && isset($_POST['postImage']) && isset($_SESSION['user']) && $_SESSION['user']->isLoggedIn ) {
        $blogPost = new Post($_POST['postTitle'], $_POST['postContent'], $_POST['postImage'], $_SESSION['user']->id); 
        $blogPost->storePost(); //strings escaped in object
}

// Check if we got a comment, and put it on the corresponding post
if ( isset($_POST['commentContent']) && isset($_POST['commentSignature']) && isset($_POST['commentParent'])) {
        $newComment = new Comment($_POST['commentContent'], $_POST['commentSignature']);
        $newComment->storeComment($_POST['commentParent']);
}

// Create and render the twig-templates 
if (isset($_SESSION['user'])){
        $page = new PagePrinter(['user' => $_SESSION['user'], 'dataBase' => $dataBase, 'loadview' => $loadview]);
} else {
        $page = new PagePrinter(['dataBase' => $dataBase, 'loadview' => $loadview, 'readmore' => $readmore ]);
}

echo $page->render();
