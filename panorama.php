<?php
require 'db.php';


$paginasResult = $conn->query("SELECT id, titel, afbeelding FROM paginas ORDER BY id ASC");


$hotspotsResult = $conn->query("
    SELECT h.id, h.pagina_id, h.x, h.y, h.titel,
           i.tekst
    FROM hotspots h
    LEFT JOIN hotspot_info i ON i.hotspot_id = h.id
    ORDER BY h.id
");
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Panorama</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="panorama.css">
</head>

<body>

    <div class="panorama">


        <?php while ($hs = $hotspotsResult->fetch_assoc()): ?>
            <div class="hotspot"
                style="left: <?= (int)$hs['x'] ?>px; top: <?= (int)$hs['y'] ?>px;"
                data-title="<?= htmlspecialchars($hs['titel']) ?>"
                data-text="<?= htmlspecialchars($hs['tekst'] ?? '') ?>"
                data-image="Beeldmateriaal/Beeld02.png">
                <?= $hs['id'] ?>
            </div>
        <?php endwhile; ?>


        <?php while ($p = $paginasResult->fetch_assoc()): ?>
            <img src="<?= htmlspecialchars($p['afbeelding']) ?>"
                alt="<?= htmlspecialchars($p['titel']) ?>">
        <?php endwhile; ?>

    </div>


    <div id="popup" class="popup">
        <div class="popup-content">
            <span id="popup-close">&times;</span>
            <h2 id="popup-title"></h2>
            <img id="popup-image">
            <p id="popup-text"></p>
        </div>
    </div>

    <script src="panorama.js"></script>
</body>

</html>