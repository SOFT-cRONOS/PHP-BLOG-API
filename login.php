<?php
/* debug */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* end debug */

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
        // Validar la solicitud POST
        $inputJSON = file_get_contents('php://input');
        // Decodificar el JSON
        $postData = json_decode($inputJSON, true);
        if (isset($postData ['op'])){
            if ($postData ['op'] === 'login') {
                if (isset($postData ['user']) && isset($postData ['pass'])) {
                    $password = $postData ['pass'];
                    $user = $postData ['user'];
                    if ($password === "1234"){;
                        $token = getToken('1');
                        $data['id'] = 1;
                        $data['token'] = $token;
                        $data['user'] = $user;
                        echo httpMessage(200,$data);
                    }
                    else {
                        echo httpMessage(401, 'contraseña o usuario incorrectos');
                    }
                }
                else {
                    echo httpMessage(400, 'Faltan los datos');
                }
            } elseif ($postData ['op'] === 'reg') {
                if (isset($postData ['user']) && isset($postData ['pass'])) {
                    $password = $postData ['pass'];
                    $user = $postData ['user'];
                    echo httpMessage(200, 'registrando usuario');
                }
                else {
                    echo httpMessage(400, 'Faltan los datos');
                }
            } else {
                echo httpMessage(400, 'Faltan los datos');
            }
        }
        else {
            // Método de solicitud no válido
            echo httpMessage(400, 'Método de solicitud no válido');
        }

    }
} 
else {
    // Método de solicitud no válido
    echo httpMessage(400, 'Método de solicitud no válido');
}
?>