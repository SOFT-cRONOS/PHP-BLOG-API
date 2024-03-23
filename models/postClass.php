<?php
class Post {
    //atributos

    //getters
    public function getPost($limit = 2000) {
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
                ORDER BY p.id DESC
                LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $mysqli->close();
        return $data;
    }

    function getPostby($option = '', $value = '', $limit = 2000) {
        if ($option === '' || $value === '') {
            return 'Insufficient parameters';
        } else {
            $data = array();
            // Utilizar switch case para manejar diferentes opciones
            switch ($option) {
                case 'id':

                    try {
                        $mysqli = openConex();
                    } catch (\Throwable $th) {
                        $data['status'] = 'error';
                        echo json_encode($data);
                    }
                                   
                    $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.content, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                                        FROM post p
                                        INNER JOIN autor a ON p.id_autor = a.id_autor
                                        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                                        WHERE id = ? AND p.publishing_status = 1
                                        ORDER BY p.id DESC");
                    $stmt->bind_param("i", $value);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    break;

                case 'author':
                    $sql .= " author = '{$value}'";
                    break;
                case 'category':
                    $mysqli = openConex();
                    $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.content, p.date, p.image_url, a.nick AS nick, c.nombre AS categoria
                                        FROM post p
                                        INNER JOIN autor a ON p.id_autor = a.id_autor
                                        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                                        WHERE c.nombre = ? AND p.publishing_status = 1
                                        ORDER BY p.id DESC
                                        LIMIT ?");
                    $stmt->bind_param("si", $value, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    break;
                case 'tag':
                    $mysqli = openConex();
                    $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, c.nombre AS categoria, t.name
                                                                FROM post p 
                                                                INNER JOIN post_tags pt ON p.id= pt.post_id
                                                                INNER JOIN  categorias c ON p.id_categoria = c.id_categoria
                                                                INNER JOIN tags t ON pt.tag_id = t.id 
                                                                WHERE t.name = ? AND p.publishing_status = 1 
                                                                ORDER BY p.id DESC
                                                                LIMIT ?");
                    $stmt->bind_param("si", $value, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    break;
                case 'found':
                    $mysqli = openConex();
                    $stmt = $mysqli->prepare("SELECT p.id, p.title, p.sinopsis, p.date, p.image_url, c.nombre AS categoria, t.name 
                                                                FROM post p
                                                                INNER JOIN post_tags pt ON p.id= pt.post_id
                                                                INNER JOIN  categorias c ON p.id_categoria = c.id_categoria
                                                                INNER JOIN tags t ON pt.tag_id = t.id 
                                                                WHERE sinopsis LIKE ? OR title LIKE ? OR tags LIKE ?
                                                                AND p.publishing_status = 1 
                                                                ORDER BY p.id DESC
                                                                LIMIT ?");
                    $search_text = "%$value%";
                    $stmt->bind_param("sssi", $search_text, $search_text, $search_text, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    break;
                case 'rand':
                    $mysqli = openConex();
                    $stmt = $mysqli->prepare("SELECT *
                                                                FROM post
                                                                ORDER BY RAND()
                                                                LIMIT ?");
                    $stmt->bind_param("i", $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    break;
                default:
                    $mysqli = openConex();
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
            }
            $mysqli->close();
            if (!empty($data)) {
                return $data;
            } else {
                return (object) [];
            }
            
        }

    }

    function getAnalytics($condition = null, $limit = 2000) {
        switch ($condition) {
            case 'today':

                break;
            case 'week':
                $sql = "SELECT
                                id_post,
                                SUM(1) AS cantidad_visitas
                            FROM
                                visit
                            WHERE
                                visit_datetime >= CURDATE() - INTERVAL 1 WEEK
                            GROUP BY
                                id_post
                            ORDER BY
                                cantidad_visitas DESC;";
                break;
            case 'hourly':
                $sql = "SELECT
                            id_post,
                            HOUR(visit_datetime) AS fecha,
                            SUM(1) AS cantidad_visitas
                        FROM
                            visit
                        WHERE
                            visit_datetime >= NOW() - INTERVAL 30 DAY
                        GROUP BY
                            id_post,
                            HOUR(visit_datetime);";
                break;
            case 'alltime':
                $sql = "SELECT
                                id_post,
                                SUM(1) AS cantidad_visitas
                            FROM
                                visit
                            GROUP BY
                                id_post
                            ORDER BY
                                cantidad_visitas DESC
                            LIMIT 4;";
                break;
            default:
                    $sql = "SELECT
                    id_post,
                    DATE(visit_datetime) AS fecha,
                    SUM(1) AS cantidad_visitas
                    FROM
                        visit
                    WHERE
                        DATE(visit_datetime) = CURDATE()
                    GROUP BY
                        id_post,
                        DATE(visit_datetime);";
        }
        $mysqli->close();
    }
    //setters
    function savePost($data){
        // Preparar la consulta usando declaraciones preparadas para prevenir inyección SQL
        $mysqli = openConex();
        $stmt = $mysqli->prepare("INSERT INTO `post` 
                                    (`id_categoria`, `id_autor`, `title`, `sinopsis`, `content`, `image_url`, `publishing_status`) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Vincular parámetros
        $stmt->bind_param("iissssi", $data['id_categoria'], $data['id_autor'], $data['title'], $data['sinopsis'], $data['content'], $data['image_url'], $data['publishing_status']);
        // Ejecutar la consulta
        $stmt->execute();
        // Obtener el ID del post agregado
        $post_id = $mysqli->insert_id;
        $mysqli->close();
        return $post_id;
    }
}
?>