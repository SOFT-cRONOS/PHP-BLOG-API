<?php


// Ruta del endpoint solicitado
$request_uri = $_SERVER['REQUEST_URI'];

// Eliminar la parte base de la URL para obtener la ruta relativa al index.php
$request_path = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $request_uri);

// Eliminar parámetros de consulta de la URL si los hay
$request_path = strtok($request_path, '?');

// Dividir la ruta en partes usando la barra diagonal como separador
$parts = explode('/', trim($request_path, '/'));

// Obtener el nombre del archivo de endpoint
$endpoint = array_shift($parts);

// Directorio de endpoints
$endpoint_dir = 'routes';

// Determinar el archivo del endpoint solicitado
$endpoint_file = $endpoint_dir . '/' . $endpoint . '.php';

// Comprobar si el archivo del endpoint existe
if (file_exists($endpoint_file)) {
    // Incluir el archivo del endpoint
    require_once $endpoint_file;
} else {
    // Endpoint no encontrado
    http_response_code(404);
    echo json_encode(array('error' => 'Endpoint not found'));
}
?>

