<?php
// Funciones conexion
require_once 'config.php';

// Limitar el tamaño máximo permitido para 'query_type'
$max_query_type_length = 20;

// Inicializar un array para almacenar los resultados
$data = array();

// Validar la solicitud
if ($_SERVER['REQUEST_METHOD'] === "GET" || $_SERVER['REQUEST_METHOD'] === "POST") {
    // Validar la solicitud GET
    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        if (isset($_GET['query_type'])) {
            $queryType = filter_input(INPUT_GET, 'query_type', FILTER_SANITIZE_STRING);
            if ($queryType === false || strlen($queryType) > $max_query_type_length) {
                exit("Solicitud no válida: tamaño de 'query_type' excede el límite permitido.");
            }
            switch ($queryType) {
                case 'today':
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
                    exit("Solicitud no válida");
            }
        } else { 
            $sql = "SELECT
                            id_post,
                            DATE(visit_datetime) AS fecha,
                            SUM(1) AS cantidad_visitas
                        FROM
                            visit
                        GROUP BY
                            id_post,
                            DATE(visit_datetime);";
        }
        // Ejecutar la consulta
        $mysqli = openConex();
        $result = $mysqli->query($sql);
        // Recorrer los resultados y almacenarlos en el array
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'fecha' => isset($row['fecha']) ? date('d-m-y', strtotime($row['fecha'])) : null,
                'id_post' => $row['id_post'],
                'cantidad_visitas' => $row['cantidad_visitas']
            );
        }
        // Cerrar la conexión y liberar recursos
        $mysqli->close();

        // Convertir el array a formato JSON y devolverlo
        echo json_encode($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
        //verificacion de token
        session_start();
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // Token CSRF no válido
            $data['message'] = 'No autorizado';
            echo json_encode($data);
        } else {
            // Validar la solicitud POST
            $inputJSON = file_get_contents('php://input');
            // Decodificar el JSON
            $postData = json_decode($inputJSON, true);
            
            // Validar datos de entrada
            if (isset($postData['id_post']) && $postData['id_post'] !== '') {
                // Sanear el ID del post
                $id_post_abierto = filter_var($postData['id_post'], FILTER_SANITIZE_NUMBER_INT);

                // Abrir la conexión
                $mysqli = openConex();
                // Comenzar la transacción
                $mysqli->begin_transaction();

                try {
                    // Insertar en la tabla 'visit'
                    $sql_insert_visit = "INSERT INTO visit (id_post, visit_datetime) VALUES (?, NOW())";
                    $stmt = $mysqli->prepare($sql_insert_visit);
                    $stmt->bind_param("i", $id_post_abierto);
                    $stmt->execute();
                    $stmt->close();

                    // Commit (confirmar) la transacción
                    $mysqli->commit();

                    // Cerrar la conexión
                    $mysqli->close();

                    // Enviar respuesta JSON
                    $data['message'] = 'Visita registrada.';
                    echo json_encode($data);
                } catch (Exception $e) {
                    // En caso de error, realizar un rollback y manejar la excepción
                    $mysqli->rollback();

                    // Manejar el error
                    $data['message'] = 'Error en SQL.';
                    echo json_encode($data);
                }
            } else {
                // No se proporcionó ID de post válido
                $data['message'] = 'Error, no hay ID de post válido.';
                echo json_encode($data);
            }
        }
    }
} else {
    // Respuesta para solicitudes no válidas
    $data['message'] = 'Solicitud no valida.';
    echo json_encode($data);
}
?>

