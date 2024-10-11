<?php
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

if ($_SERVER['HTTP_HOST'] == 'localhost') {
  $dotenv = Dotenv::createImmutable('../');
  $dotenv->load();
}

$link = pg_connect($_ENV['DATABASE_URL']);
?>
