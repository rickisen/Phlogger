<?php session_start();


// INCLUDING TWIG
require_once 'Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('templates/');
$twig = new Twig_Environment($loader);

// CREATE TWIG TAGS TO RENDER IN HTML INDEX
$data = [

];

// SEND TO INDEX
echo $twig -> render('index.html', $data);

// IMPORT CLASS FILES
function __autoload($classname) {
    include $classname . '.class.php';
}