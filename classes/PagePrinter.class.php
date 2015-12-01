<?php
// INCLUDING TWIG
require_once 'twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();


// SEND TO INDEX
echo $twig -> render('index.html', $data);

Class PagePrinter {
  private $data = array(), $loader, $twig, $target = 'templates/blogg.twig';

  function __construct($data){
    $this->data   = $data;
    $this->loader = new Twig_Loader_Filesystem('templates/');
    $this->twig   = new Twig_Environment($this->loader);
  }

  function render(){
    return $this->twig->render( $this->target, $this->data );
  }
}
