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
        // Validar la solicitud POST
        $inputJSON = file_get_contents('php://input');
        // Decodificar el JSON
        $postData = json_decode($inputJSON, true);
        if (isset($postData ['op'])){
            if ($postData ['op'] === 'login') {
                if (isset($postData ['user']) && isset($postData ['pass'])) {
                    $password = $postData ['pass'];
                    $user = $postData ['user'];

                    $mysqli = openConex();
                    $stmt = $mysqli->prepare("SELECT id_user, password
                                                FROM users
                                                WHERE username = ?
                                                LIMIT 1");
                    $stmt->bind_param("s", $user);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    // Verificar si se encontró algún registro
                    if ($result->num_rows > 0) {
                        // Obtener la primera fila de la respuesta
                        $row = $result->fetch_assoc();
                        $Stored_id_user = strval($row['id_user']);
                        $Stored_pass = $row['password'];
                        // Comparar el pass guardado con el pass enviado
                        if ($password === $Stored_pass) {
                            $token = getToken($Stored_id_user);
                            $data['id'] = $Stored_id_user;
                            $data['token'] = $token;
                            $data['user'] = $user;
                            echo httpMessage(200,$data);
                        } else {
                            echo httpMessage(401,'not pass');
                            exit;
                        }
                    } else {
                        echo httpMessage(401, 'not exist');
                        exit;
                    }
                }
                else {
                    echo httpMessage(400, 'Faltan los datos');
                }
            } elseif ($postData ['op'] === 'reg') {
                if (isset($postData ['user']) && isset($postData ['pass'])) {
                    $headers = getallheaders();
                    authToken('user', $headers);
                    //registra usuario, falta hash y estado de usuario
                    $password = $postData['pass'];
                    $user = $postData['user'];
                    $mysqli = openConex();
                    $stmt = $mysqli->prepare("INSERT INTO 
                                                users (username, password)                            VALUES (?, ?)");
                    $stmt->bind_param("ss", $user, $password);
                    $stmt->execute();
                    echo httpMessage(200, 'usuario registrado');
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