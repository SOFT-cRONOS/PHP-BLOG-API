<?php
// Funciones conexion
require_once 'config.php';
//funciones jwt
require_once 'auth.php';
//modulos vendor
require_once 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === "GET" || $_SERVER['REQUEST_METHOD'] === "POST") {
    $data = array();
    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $data['status'] = 'ok';
        $data['message'] = 'Hola, mi nombre es cronos';
        echo json_encode($data);
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
        // leer header user y token $_SERVER['user']
        $headers = getallheaders();
        
        if (authToken($headers)) {
            echo httpMessage(200, 'autorizado');
        } else {
            echo httpMessage(401);
        }

    }
} 
else {
    // Método de solicitud no válido
    echo httpMessage(400, 'Método de solicitud no válido');
}
?>