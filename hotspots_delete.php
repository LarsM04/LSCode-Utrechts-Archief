<?php
require 'auth_check.php';
require 'db.php';

$id        = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pagina_id = isset($_GET['pagina_id']) ? (int)$_GET['pagina_id'] : 0;

if ($id > 0) {

    $stmtInfo = $conn->prepare("DELETE FROM hotspot_info WHERE hotspot_id = ?");
    $stmtInfo->bind_param("i", $id);
    $stmtInfo->execute();
    $stmtInfo->close();


    $stmt = $conn->prepare("DELETE FROM hotspots WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: hotspots_index.php?pagina_id=" . $pagina_id);
exit;
