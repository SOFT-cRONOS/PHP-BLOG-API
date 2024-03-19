<?php

// Funciones conexion
require_once 'config.php'; 


function getCategoriasorig()
{
    $mysqli = openConex();

    $stmt = $mysqli->prepare("SELECT * FROM categorias");

    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;

}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mysqli = openConex();
    $result = $mysqli->query("SELECT * FROM categorias");
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($data);
}

?>
