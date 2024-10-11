<?php
//require_once '../vendor/autoload.php';
//use Dotenv\Dotenv;
//$dotenv = Dotenv::createImmutable('../');
//$dotenv->load();

$link = pg_connect($_ENV['DATABASE_URL']);

?>
