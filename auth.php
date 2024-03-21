<?php
// Funciones conexion
require_once 'config.php';

//modulos vendor
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getToken($data) {
    $now = strtotime("now");
    $masterkey = $_ENV['KEY_MASTER']; //leer de .env
    $payload = [
        'exp' => $now + 3600,
        'data' => $data, //valor de consulta que se va a encriptar
    ];
    
    //jwt es mi token, lo genero con el expiracion y el id del usuario q se esta logeando 
    $jwt = JWT::encode($payload, $masterkey, 'HS256');
    //mensaje de retorno con el token
    return $jwt;
}

function authToken($headers) {
    if (isset( $headers['user']) || isset($headers['token'])) {
        $user= $headers['user'];
        $token = $headers['token'];
        
        $mask = unMask($token);
        $id_user = $mask['data'];
        $storedUser = 'cronos'; //nombre de usuario traido con el id
        if ($id_user === '1' && $storedUser === $user) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
    
}

function unMask($token) {
    $data = array();
   try {
        $masterkey = $_ENV['KEY_MASTER']; //leer de .env
        $decoded = JWT::decode($token, new Key($masterkey, 'HS256'));
        return (array)$decoded;
    } catch (\Throwable $th) {
        $data['data'] = 'error';
        return $data;
    }
}

function httpMessage($status, $message = null) {
    $data = array();
    switch ($status) {
        case 200:
            $data['status'] = 'ok';
            $data['message'] = $message ?? 'Operación exitosa';
            break;
        case 400:
            $data['status'] = 'error';
            $data['error'] = $message ?? 'Solicitud incorrecta';
            break;
        case 401:
            $data['status'] = 'error';
            $data['error'] = $message ?? 'No autorizado';
            break;
        case 403:
            $data['status'] = 'error';
            $data['error'] = $message ?? 'Prohibido';
            break;
        case 404:
            $data['status'] = 'error';
            $data['error'] = $message ?? 'No encontrado';
            break;
        case 500:
            $data['status'] = 'error';
            $data['error'] = $message ?? 'Error interno del servidor';
            break;
        default:
            $data['status'] = 'error';
            $data['error'] = $message ?? 'Código de estado no reconocido';
            break;
    }
    http_response_code($status);
    return json_encode($data);
}
?>