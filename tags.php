<?php

// Funciones conexion
require_once 'config.php'; 

 


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$mysqli = openConex();
		$id = $_GET['id'];
		$stmt = $mysqli->prepare("SELECT t.name FROM post_tags INNER JOIN tags t ON post_tags.tag_id = t.id WHERE post_tags.post_id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);

		foreach ($data as &$row) {
            $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        }

		echo json_encode($data);
	} elseif (isset($_GET['ranking']) && ($_GET['ranking']) == 1) {
		$mysqli = openConex();
		$limit = $_GET['ranking'];
		$stmt = $mysqli->prepare("SELECT t.name, sum(1) AS used from tags t inner join post_tags pt on t.id = pt.tag_id group by tag_id");
		$stmt->execute();
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);

		foreach ($data as &$row) {
            $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        }

		echo json_encode($data);
	} else {
		$mysqli = openConex();
		$result = $mysqli->query("SELECT t.name 
					 FROM tags t");
		$data = $result->fetch_all(MYSQLI_ASSOC);

		foreach ($data as &$row) {
            $row['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        }

		echo json_encode($data);
	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Obtiene el cuerpo de la solicitud POST
	$inputJSON = file_get_contents('php://input');
	// Decodifica el JSON si es una solicitud con datos JSON
	$postData = json_decode($inputJSON, true);
	// Obtener los tags ingresados por el usuario desde el input (separados por punto y comas)
	$tags_input =  $postData['tags']; 
	$post_id =  $postData['id_post']; 
	//opera la conexion
	$conn  = openConex();
	// separa el str por la coma
	$tags_list = explode(';', $tags_input);
	// Recorrer la lista de tags ingresados
	foreach ($tags_list as $tag_name) {
		// Escapar el nombre del tag para evitar SQL injection
		$tag_name = mysqli_real_escape_string($conn, trim($tag_name));
		// Verificar si el tag ya existe en la tabla tags
		$sql = "SELECT id FROM tags WHERE name = '$tag_name'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			// El tag ya existe, obtener su id
			$row = $result->fetch_assoc();
			$tag_id = $row["id"];
		} else {
			// El tag no existe, insertarlo en la tabla tags y obtener su id
			$sql = "INSERT INTO tags (name) VALUES ('$tag_name')";
			if ($conn->query($sql) === TRUE) {
				//Obtiene el id insertado
				$tag_id = $conn->insert_id;
			} else {
				echo "Error al insertar el tag: " . $conn->error;
				continue; // Continuar con el siguiente tag si hay un error
			}
		}
		// Crear la relación en la tabla post_tags
		$sql = "INSERT INTO post_tags (post_id, tag_id) VALUES ($post_id, $tag_id)";
		if ($conn->query($sql) !== TRUE) {
			echo "Error al crear la relación: " . $conn->error;
		}
	}
	// Cerrar la conexión
	$conn->close();
}
?>