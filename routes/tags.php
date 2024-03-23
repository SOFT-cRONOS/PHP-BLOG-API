<?php
$dir = dirname(__DIR__);
// Funciones conexion
require_once $dir.'/config/conection.php'; 
//vendor modules
require_once $dir.'vendor/autoload.php';
//functions jwt
require_once $dir.'/config/auth.php';
//class
require_once $dir.'models/tagsClass.php';

$_tags = new tags();
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
/* 	$headers = getallheaders();
    authToken('visit', $headers); */
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$_tags->getTagsbyPost($_GET['id']);
	} elseif (isset($_GET['ranking']) && ($_GET['ranking']) == 1) {
		$limit = $_GET['ranking'];
		$_tags->getTagsRanking($limit);
	} else {
		$_tags->getTags();
	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$headers = getallheaders();
    authToken('user', $headers);
	// Obtiene el cuerpo de la solicitud POST
	$inputJSON = file_get_contents('php://input');
	// Decodifica el JSON si es una solicitud con datos JSON
	$postData = json_decode($inputJSON, true);
	$_tags->putStrTags($postData);
}
?>