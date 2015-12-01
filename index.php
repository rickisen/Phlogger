<?php 
// IMPORT CLASS FILES
function __autoload($classname) {
    include $classname . '.class.php';
}

// Start a session 
session_start();

