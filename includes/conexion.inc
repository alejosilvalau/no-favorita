<?php 
require_once '../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

$link = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'], $_ENV['DB_PORT']) or die("Error");

?>
