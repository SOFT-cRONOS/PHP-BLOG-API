<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = explode( '/', $uri );

echo "API SOLAR soft-cronos.com";

require_once 'config.php';

$mysqli = openConex();
$stmt = $mysqli->prepare("SELECT *
                            FROM users
                            ORDER BY id_user DESC");
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($data);
?>
<div class="container">
    <div class="copyright">
    © Copyright <strong><span>SOLAR</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
    Created by <a href="https://softcronos.com.ar/">Soft-cRONOS</a>
    </div>
</div>