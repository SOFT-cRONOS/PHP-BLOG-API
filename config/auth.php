<?php
$dir = dirname(__DIR__);

// Funciones conexion
require_once 'conection.php';

//modulos vendor
require_once $dir.'/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Función para limpiar datos de entrada
function sanitizeInput($entry) {
    return htmlspecialchars(strip_tags($entry));
    //htmlspecialchars elimina caracteres html
    //strip_tags elimina caracteres php
}

function userPermision($headers) {
    if (authToken($headers)){
        return true;
    } else {
        echo httpMessage(401);
        exit;
    }
}

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

function authToken($rank, $headers) {
    //rank son los permisos q va a tener. visit, usuario, admin,
    if (isset( $headers['user']) && isset($headers['token'])) {
        $_userObj = new User();

        $username= $headers['user'];
        $token = $headers['token'];
        $result = $_userObj->getUserByUname($username);
        
        // Verificar si se encontró algún registro
        if ($result->num_rows > 0) {
            // Obtener la primera fila de la respuesta
            $row = $result->fetch_assoc();
            $Stored_id_user = strval($row['id_user']);

            //desencriptar token
            $mask = unMask($token);
            $id_user = $mask['data'];

            // Comparar el id de la bd con el token
            if ($Stored_id_user === $id_user) {
                return true;
            } else {
                echo httpMessage(401);
                exit;
            }
        } else {
            echo httpMessage(401);
            exit;
        }
    } else {
        echo httpMessage(401);
        exit;
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