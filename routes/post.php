<?php
$dir = dirname(__DIR__);
// Funciones conexion
require_once $dir.'/config/conection.php'; 
//functions jwt
require_once $dir.'/config/auth.php';
//class
require_once $dir.'/models/postClass.php';

$_post = new Post();



// Validar la solicitud GET o POST
if ($_SERVER['REQUEST_METHOD'] === "GET") {

    // Obtener datos del post por ID
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = sanitizeInput($_GET['id']);
        $data = $_post->getPostby('id', $id);
        echo json_encode($data);
    }
    // Obtener datos de post por límite
    elseif (isset($_GET['limit'])) {
        
        $limit = sanitizeInput($_GET['limit']);
        $data = $_post->getPostby(null,null,$limit);
        echo json_encode($data);
    }
    // Obtener datos de post por categoría
    elseif (isset($_GET['bycategory'])) {
        
        $category = sanitizeInput($_GET['bycategory']);
        $data = $_post->getPostby('category',$category);
        echo json_encode($data);
    }
    // Obtener datos de post por etiqueta
    elseif (isset($_GET['bytag'])) {

        $tag = sanitizeInput($_GET['bytag']);
        $data = $_post->getPostby('tag',$tag);
        echo json_encode($data);

    }
    // Buscar datos de post por texto en título o sinopsis
    elseif (isset($_GET['found'])) {
        
        $text = sanitizeInput($_GET['found']);
        $data = $_post->getPostby('found',$text);
        echo json_encode($data);
        
    }
    // Obtener datos de post aleatorios
    elseif (isset($_GET['rand'])) {

        $limit = sanitizeInput($_GET['rand']);
        $data = $_post->getPostby('rand',null,$limit);
        echo json_encode($data);
    
    }
    // Si no se proporciona ningún parámetro, obtener todos los posts
    else  {
        echo json_encode($_post->getPost());
    }
} 
// Procesar solicitud POST
elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    $headers = getallheaders();
    authToken('visit', $headers);
    // Obtiene el cuerpo de la solicitud POST
    $inputJSON = file_get_contents('php://input');
    // Decodifica el JSON si es una solicitud con datos JSON
    $postData = json_decode($inputJSON, true);
    // Accede a los datos según sea necesario
    if ($postData !== null) {
        // Limpiar y validar datos de entrada
        $data = array( 
            'title' =>sanitizeInput($postData['title']),
            'id_categoria' =>  sanitizeInput($postData['id_categoria']),
            'id_autor' =>  sanitizeInput($postData['id_autor']),
            'sinopsis' =>  sanitizeInput($postData['sinopsis']),
            'content' =>sanitizeInput($postData['content']),
            'image_url' => sanitizeInput($postData['image_url']),
            'publishing_status' =>  sanitizeInput($postData['publishing_status'])
        );

        $post_id = $_post->savePost($data);
        // Preparar datos de respuesta
        $data['message'] = 'Ok, post creado';
        $data['id_post'] = $post_id;
        // Imprimir respuesta en formato JSON
        echo json_encode($data);
    } else {
        // Si hay un error al decodificar JSON, enviar un mensaje de error
        $data['message'] = 'Error al decodificar JSON.';
        echo json_encode($data);
    }

}
?>