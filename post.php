<?php
// Funciones de conexión
require_once 'config.php'; 
//funciones jwt
require_once 'auth.php';

// Función para limpiar datos de entrada
function limpiar_entrada($entrada) {
    return htmlspecialchars(strip_tags($entrada));
}

// Validar la solicitud GET o POST
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Obtener datos del post por ID
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        try {
            $mysqli = openConex();
        } catch (\Throwable $th) {
            $data['status'] = 'error';
            echo json_encode($data);
        }
        
        $id = limpiar_entrada($_GET['id']);
        $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.content, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                            FROM post p
                            INNER JOIN autor a ON p.id_autor = a.id_autor
                            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                            WHERE id = ? AND p.publishing_status = 1
                            ORDER BY p.id DESC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    // Obtener datos de post por límite
    elseif (isset($_GET['limit'])) {
        $mysqli = openConex();
        $limit = limpiar_entrada($_GET['limit']);
        $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                    FROM post p
                    INNER JOIN autor a ON p.id_autor = a.id_autor
                    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.publishing_status = 1
                    ORDER BY p.id DESC
                    LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    // Obtener datos de post por categoría
    elseif (isset($_GET['bycategory'])) {
        $mysqli = openConex();
        $category = limpiar_entrada($_GET['bycategory']);
        $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.content, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                            FROM post p
                            INNER JOIN autor a ON p.id_autor = a.id_autor
                            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                            WHERE c.nombre = ? AND p.publishing_status = 1
                            ORDER BY p.id DESC");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    // Obtener datos de post por etiqueta
    elseif (isset($_GET['bytag'])) {
        $mysqli = openConex();
        $tag = limpiar_entrada($_GET['bytag']);
        $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, c.nombre AS categoria, t.name
                                                    FROM post p 
                                                    INNER JOIN post_tags pt ON p.id= pt.post_id
                                                    INNER JOIN  categorias c ON p.id_categoria = c.id_categoria
                                                    INNER JOIN tags t ON pt.tag_id = t.id 
                                                    WHERE t.name = ? AND p.publishing_status = 1 
                                                    ORDER BY p.id DESC");
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    // Buscar datos de post por texto en título o sinopsis
    elseif (isset($_GET['found'])) {
        $mysqli = openConex();
        $text = limpiar_entrada($_GET['found']);
        $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, c.nombre AS categoria, t.name 
                                                    FROM post p
                                                    INNER JOIN post_tags pt ON p.id= pt.post_id
                                                    INNER JOIN  categorias c ON p.id_categoria = c.id_categoria
                                                    INNER JOIN tags t ON pt.tag_id = t.id 
                                                    WHERE sinopsis LIKE ? OR title LIKE ? OR tags LIKE ?
                                                    AND p.publishing_status = 1 
                                                    ORDER BY p.id DESC");
        $search_text = "%$text%";
        $stmt->bind_param("sss", $search_text, $search_text, $search_text);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        // Verificar si hay resultados
        if (!empty($data)) {
            // Si hay resultados, imprimir el JSON
            echo json_encode($data);
        } else {
            // Si no hay resultados, imprimir un JSON vacío
            echo json_encode((object) []);
        }
    }
    // Obtener datos de post aleatorios
    elseif (isset($_GET['rand'])) {
        $mysqli = openConex();
        $limit = limpiar_entrada($_GET['rand']);
        $stmt = $mysqli->prepare("SELECT *
                                                    FROM post
                                                    ORDER BY RAND()
                                                    LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        // Verificar si hay resultados
        if (!empty($data)) {
            // Si hay resultados, imprimir el JSON
            echo json_encode($data);
        } else {
            // Si no hay resultados, imprimir un JSON vacío
            echo json_encode((object) []);
        }
    }
    // Si no se proporciona ningún parámetro, obtener todos los posts
    else  {
        try {
            $mysqli = openConex();
        } catch (\Throwable $th) {
            $data['status'] = 'server error';
            echo json_encode($data);
            exit;
        }
        $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                FROM post p
                INNER JOIN autor a ON p.id_autor = a.id_autor
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.publishing_status = 1
                ORDER BY p.id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
} 
// Procesar solicitud POST
elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    $headers = getallheaders();
    if (authToken($headers)) {
        // Obtiene el cuerpo de la solicitud POST
        $inputJSON = file_get_contents('php://input');
        // Decodifica el JSON si es una solicitud con datos JSON
        $postData = json_decode($inputJSON, true);
        // Accede a los datos según sea necesario
        if ($postData !== null) {
            // Limpiar y validar datos de entrada
            $title = limpiar_entrada($postData['title']);
            $idCategoria = limpiar_entrada($postData['id_categoria']);
            $idAutor = limpiar_entrada($postData['id_autor']);
            $sinopsis = limpiar_entrada($postData['sinopsis']);
            $content = limpiar_entrada($postData['content']);
            $imageUrl = limpiar_entrada($postData['image_url']);
            $publishing_status = limpiar_entrada($postData['publishing_status']);
            
            // Preparar la consulta usando declaraciones preparadas para prevenir inyección SQL
            $mysqli = openConex();
            $stmt = $mysqli->prepare("INSERT INTO `post` 
                                        (`id_categoria`, `id_autor`, `title`, `sinopsis`, `content`, `image_url`, `publishing_status`) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            // Vincular parámetros
            $stmt->bind_param("iissssi", $idCategoria, $idAutor, $title, $sinopsis, $content, $imageUrl, $publishing_status);
            // Ejecutar la consulta
            $stmt->execute();
            // Obtener el ID del post agregado
            $post_id = $mysqli->insert_id;
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
    } else {
        echo httpMessage(401);
    }
}
?>