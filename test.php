<?php
/* debug */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* end debug */
//modulos vendor
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$masterkey = getenv('KEY_MASTER');

echo $masterkey;

?>