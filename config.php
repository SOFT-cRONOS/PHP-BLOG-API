<?php
/* debug */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* end debug */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

define('DBUSER','bdname');
define('DBPWD','bdpassword');
define('DBHOST','localhost');
define('DBNAME','bduser');

function openConex(){
    $conn = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
    if ($conn->connect_error) {
        return 'error';
	}
    $conn->set_charset("utf8mb4");
	
    return $conn;
}   


?>
