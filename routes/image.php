<?php

    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //array de respuesta
        $data = array();

        $uploadDIr = __DIR__ . '/images/';
        $temp_name = $_FILES['upfile']['tmp_name'];
        $name_file = $_FILES['upfile']['name'];

        // Obtener la información de la ruta del archivo
        $pathInfo = pathinfo($name_file);
        $fileExtension = strtolower($pathInfo['extension']);

        //formatos permitidos
        $allowedExtensions = array('png', 'jpg', 'webp');
    
        if (in_array($fileExtension, $allowedExtensions)) {
            //comprobar existencia
            if (file_exists( __DIR__ . '/images/' . $name_file)) {
                //mover el archivo temporal
                $data['success'] = false;
                $data['message'] = 'File already exists';
                $data['imageUrl'] = '/images/error_load.webp';
            } else {
                if (move_uploaded_file($temp_name, $uploadDIr. $name_file)) {
                    //exito
                    $data['success'] = true;
                    $data['message'] = 'Ok';
                    $data['imageUrl'] = '/images/'. $name_file;

                } else {
                    //fallo
                    $data['success'] = false;
                    $data['message'] = 'error al mover en ' . $uploadDIr ;
                    $data['imageUrl'] = '/images/error_load.webp';
                }
            }
        } else {
            //fallo
            $data['success'] = false;
            $data['message'] = 'Format not support';
            $data['imageUrl'] = '/images/error_load.webp';
        }

        echo json_encode($data);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = array();
        $uploadDIr = __DIR__ . '/images/';
        // Lee el contenido del directorio
        $files = scandir($uploadDIr);

        // Filtra solo los archivos de imagen (puedes ajustar esta condición según tus necesidades)
        $imageFiles = array_filter($files, function ($file) {
            return preg_match("/\.(jpg|jpeg|png|webp)$/i", 'images/' . $file);
        });

        // Agrega los nombres de las imágenes al array de respuesta
        $data = array_values($imageFiles);

        // Devuelve la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($data);

    }

?>