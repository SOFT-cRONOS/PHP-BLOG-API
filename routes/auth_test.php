<?php
// Funciones conexion
require_once './config/conection.php';
//funciones jwt
require_once './config/auth.php';
//modulos vendor
require_once './vendor/autoload.php';

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
        
        authToken('user', $headers);

    }
} 
else {
    // Método de solicitud no válido
    echo httpMessage(400, 'Método de solicitud no válido');
}
?>