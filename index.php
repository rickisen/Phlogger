<?php 
// IMPORT CLASS FILES
/*function __autoload($classname) {
    include $classname . '.class.php';
}*/

require_once 'classes/UserSession.class.php';
require_once 'classes/Post.class.php';
require_once 'classes/Comment.class.php';
require_once 'classes/PagePrinter.class.php';
require_once 'classes/Statistics.class.php';

// Start a session 
session_start();

$loadview = 'landingpage';
if (isset($_GET['loadview'])) {
	$loadview = $_GET['loadview'];
}

$data = [
	'loadview' => $loadview
];

$view = new PagePrinter($data);

echo $view->render();

//echo $loadview;