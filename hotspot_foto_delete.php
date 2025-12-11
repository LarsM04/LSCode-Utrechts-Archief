<?php
require 'auth_check.php';
require 'db.php';

$fotoId    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hotspotId = isset($_GET['hotspot_id']) ? (int)$_GET['hotspot_id'] : 0;
$pagina_id = isset($_GET['pagina_id']) ? (int)$_GET['pagina_id'] : 0;

if ($fotoId > 0) {
    $stmt = $conn->prepare('SELECT bestand FROM hotspot_fotos WHERE id = ?');
    $stmt->bind_param('i', $fotoId);
    $stmt->execute();
    $stmt->bind_result($pad);
    if ($stmt->fetch()) {
        if (!empty($pad) && is_file($pad)) {
            @unlink($pad);
        }
    }
    $stmt->close();

    $stmtDel = $conn->prepare('DELETE FROM hotspot_fotos WHERE id = ?');
    $stmtDel->bind_param('i', $fotoId);
    $stmtDel->execute();
    $stmtDel->close();
}

header('Location: hotspots_edit.php?id=' . $hotspotId . '&pagina_id=' . $pagina_id);
exit;
