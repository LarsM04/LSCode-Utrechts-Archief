<?php
require 'auth_check.php';
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // hotspot_info weg
    $stmt = $conn->prepare('
        DELETE i FROM hotspot_info i
        JOIN hotspots h ON i.hotspot_id = h.id
        WHERE h.pagina_id = ?
    ');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // hotspots weg
    $stmt = $conn->prepare('DELETE FROM hotspots WHERE pagina_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // pagina zelf weg
    $stmt = $conn->prepare('DELETE FROM paginas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: paginas_index.php');
exit;
