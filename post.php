<?php
// Funciones conexion

require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $mysqli = openConex();
        $id = $_GET['id'];
        $result = $mysqli->query("SELECT p.id, p.title, p.sinopsis, p.content, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                            FROM post p
                            INNER JOIN autor a ON p.id_autor = a.id_autor
                            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                            WHERE id = $id
                            AND p.publishing_status = 1
                            ORDER BY p.id DESC");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    elseif (isset($_GET['limit'])) {
        $mysqli = openConex();
        $limit = $_GET['limit'];
        $result = $mysqli->query("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                    FROM post p
                    INNER JOIN autor a ON p.id_autor = a.id_autor
                    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.publishing_status = 1
                    ORDER BY p.id DESC
                    LIMIT $limit;");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    elseif (isset($_GET['bycategory'])) {
        $mysqli = openConex();
        $category = $_GET['bycategory'];
        $result = $mysqli->query("SELECT p.id, p.title, p.sinopsis, p.content, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                            FROM post p
                            INNER JOIN autor a ON p.id_autor = a.id_autor
                            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                            WHERE c.nombre = '$category'
                            AND p.publishing_status = 1
                            ORDER BY p.id DESC");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    elseif (isset($_GET['bytag'])) {
        $mysqli = openConex();
        $tag = $_GET['bytag'];
        $result = $mysqli->query("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, c.nombre AS categoria, t.name
                                                    FROM post p 
                                                    INNER JOIN post_tags pt ON p.id= pt.post_id
                                                    INNER JOIN  categorias c ON p.id_categoria = c.id_categoria
                                                    INNER JOIN tags t ON pt.tag_id = t.id 
                                                    WHERE t.name = '$tag' 
                                                    AND p.publishing_status = 1 
                                                    ORDER BY p.id DESC;");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
    elseif (isset($_GET['found'])) {
        $mysqli = openConex();
        $text = $_GET['found'];
        $result = $mysqli->query("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, c.nombre  AS categoria, t.name 
                                                    FROM post p
                                                    INNER JOIN post_tags pt ON p.id= pt.post_id
                                                    INNER JOIN  categorias c ON p.id_categoria = c.id_categoria
                                                    INNER JOIN tags t ON pt.tag_id = t.id 
                                                    WHERE sinopsis LIKE '%$text%'    OR title LIKE '%$text%'    OR tags LIKE '%$text%
                                                    AND p.publishing_status = 1 
                                                    ORDER BY p.id DESC;'");
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
    elseif (isset($_GET['rand'])) {
        $mysqli = openConex();
        $limit = $_GET['rand'];
        $result = $mysqli->query("SELECT *
                                                    FROM post
                                                    ORDER BY RAND()
                                                    LIMIT $limit;");
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
    //si no esta id o bycategory o bytag da todos los post
    else  {
        $mysqli = openConex();
        $result = $mysqli->query("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                FROM post p
                INNER JOIN autor a ON p.id_autor = a.id_autor
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.publishing_status = 1
                ORDER BY p.id DESC
                ;");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Obtiene el cuerpo de la solicitud POST
    $inputJSON = file_get_contents('php://input');
    // Decodifica el JSON si es una solicitud con datos JSON
    $postData = json_decode($inputJSON, true);
    // Accede a los datos según sea necesario
    if ($postData !== null) {
        // Ejemplo de cómo acceder a los datos específicos
        $title = $postData['title'];
        $idCategoria = $postData['id_categoria'];
        $idAutor = $postData['id_autor'];
        $sinopsis = $postData['sinopsis'];
        $content = $postData['content'];
        $imageUrl = $postData['image_url'];
        $publishing_status = $postData['publishing_status'];
        $mysqli = openConex();
        $result = $mysqli->query(" INSERT INTO `post` 
                                    (`id_categoria`, `id_autor`, `title`, `sinopsis`, `content`, `image_url`, `publishing_status`) 
                                    VALUES ($idCategoria, $idAutor, '$title', '$sinopsis', '$content', '$imageUrl', $publishing_status) 
                                ;");
        /* obtiene el id del post agregado */
        $post_id =  $mysqli->insert_id;
        $data['message'] = 'Ok, post creado';
        $data['id_post'] = $post_id;
        echo json_encode($data);
    } else {
        $data['message'] = 'Error al decodificar JSON.';
        echo json_encode($data);
    }
}
/* 
(!isset($_GET['id']x) && !isset($_GET['bycategory']) && !isset($_GET['bytag'])&& !isset($_GET['limit'])&&  !isset($_GET['found']))
 */
?>
