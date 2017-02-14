<?php
require_once( 'SplClassLoader.php' );

// Load the Ctct namespace
$loader = new \Ctct\CTCTSplClassLoader('Ctct', dirname(__DIR__));
$loader->register();
