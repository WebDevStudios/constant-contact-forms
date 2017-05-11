<?php
require_once( 'SplClassLoader.php' );

// Load the Ctct namespace
$loader = new \Ctct\CTCTOfficialSplClassLoader('Ctct', dirname(__DIR__));
$loader->register();
