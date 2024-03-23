<?php
//funciones jwt
require_once dirname(__DIR__).'/config/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = sanitizeInput($_GET['id']);
    echo $id;
}

?>