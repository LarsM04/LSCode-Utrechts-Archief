<?php
require 'auth_check.php';
require 'db.php';

$id        = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pagina_id = isset($_GET['pagina_id']) ? (int) $_GET['pagina_id'] : 0;

if ($id > 0) {

    // Eerst alle bestanden van hotspot_fotos verwijderen
    $stmtF = $conn->prepare('SELECT bestand FROM hotspot_fotos WHERE hotspot_id = ?');
    $stmtF->bind_param('i', $id);
    $stmtF->execute();
    $resF = $stmtF->get_result();
    while ($rowF = $resF->fetch_assoc()) {
        if (!empty($rowF['bestand']) && is_file($rowF['bestand'])) {
            @unlink($rowF['bestand']);
        }
    }
    $stmtF->close();

    // Dan de records in hotspot_fotos
    $stmtDelF = $conn->prepare('DELETE FROM hotspot_fotos WHERE hotspot_id = ?');
    $stmtDelF->bind_param('i', $id);
    $stmtDelF->execute();
    $stmtDelF->close();

    // Dan gerelateerde info verwijderen
    $stmtInfo = $conn->prepare('DELETE FROM hotspot_info WHERE hotspot_id = ?');
    $stmtInfo->bind_param('i', $id);
    $stmtInfo->execute();
    $stmtInfo->close();

    // En uiteindelijk de hotspot zelf
    $stmt = $conn->prepare('DELETE FROM hotspots WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: hotspots_index.php?pagina_id=' . $pagina_id);
exit;
