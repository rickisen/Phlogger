<?php session_start();

require_once 'Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('templates/');
$twig = new Twig_Environment($loader);

$data = [

];

echo $twig -> render('index.html', $data);

function __autoload($classname) {
    include $classname . '.class.php';
}