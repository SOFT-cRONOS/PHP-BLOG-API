<?php
/* debug */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* end debug */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

define('DBUSER','paecadmin');
define('DBPWD','7cronos1');
define('DBHOST','172.17.0.2');
define('DBNAME','paecblog');

function openConex(){
    $conn = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
    if ($conn->connect_error) {
        return 'error';
	}
    $conn->set_charset("utf8mb4");
	
    return $conn;
}   


?>
