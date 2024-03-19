<?php
// Funciones conexion
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    // Consulta SQL para obtener la lista agrupada por fecha y ordenada por fecha y cantidad de visitas
    $mysqli = openConex();
    if (isset($_GET['query_type'])) {
        $queryType = mysqli_real_escape_string($mysqli, $_GET['query_type']);
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
    $result = $mysqli->query($sql);

    // Inicializar un array para almacenar los resultados
    $data = array();

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

    // Convertir el array a formato JSON
    $json_data = json_encode($data);

    // Imprimir o devolver el JSON según tus necesidades
    echo $json_data;
} elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Obtiene el cuerpo de la solicitud POST
    $inputJSON = file_get_contents('php://input');
    // Decodifica el JSON si es una solicitud con datos JSON
    $postData = json_decode($inputJSON, true);
    if (isset($postData['id_post']) && $postData['id_post'] !== '') {

        // ID del post desde el HTML
        $id_post_abierto = $postData['id_post'];  // Sanear los datos recibidos (mejor usar prepared statements)
        $id_post_abierto = filter_var($id_post_abierto, FILTER_SANITIZE_NUMBER_INT); //saneado

        $mysqli = openConex();

        // Comenzar la transacción
        $mysqli->begin_transaction();

        // Array de mensaje
        $data = array();

        try {
            // Insertar en la tabla 'visit'
            $sql_insert_visit = "INSERT INTO visit (id_post, visit_datetime) VALUES (?, NOW())";
            $stmt = $mysqli->prepare($sql_insert_visit);
            $stmt->bind_param("i",$id_post_abierto);
            $stmt->execute();
            $stmt->close();

            // Commit (confirmar) la transacción
            $mysqli->commit();

            // Cerrar la conexión
            $mysqli->close();

            // Redireccionar o realizar otras acciones después de guardar la visita
            // header("Location: tu_pagina.php");
            // exit();
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
        $data['message'] = 'Error, no hay id.';
        echo json_encode($data);
    }
}
?>