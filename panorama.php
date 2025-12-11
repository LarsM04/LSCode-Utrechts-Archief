<?php
require 'db.php';

// Pagina's ophalen
$paginasResult = $conn->query('SELECT id, titel, afbeelding FROM paginas ORDER BY id ASC');

// Hotspots ophalen en per pagina groeperen
$hotspotsResult = $conn->query("
    SELECT h.id, h.pagina_id, h.x, h.y, h.titel, i.tekst
    FROM hotspots h
    LEFT JOIN hotspot_info i ON i.hotspot_id = h.id
    ORDER BY h.id
");

$hotspotsByPage = [];
while ($hs = $hotspotsResult->fetch_assoc()) {
    $hotspotsByPage[$hs['pagina_id']][] = $hs;
}

// Extra foto’s per hotspot ophalen
$fotoResult = $conn->query('SELECT hotspot_id, bestand FROM hotspot_fotos ORDER BY id ASC');
$fotoByHotspot = [];
while ($f = $fotoResult->fetch_assoc()) {
    $fotoByHotspot[$f['hotspot_id']][] = $f['bestand'];
}

// Voor de minimap nog een tweede resultset met alleen afbeeldingen
$paginasMinimapResult = $conn->query('SELECT afbeelding FROM paginas ORDER BY id ASC');
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

    <!-- Terug-knop -->
    <a href="startscherm.html" class="terug-knop">← Terug</a>

    <!-- Hoofd-panorama: let op id="panorama" voor zoom -->
    <div class="panorama" id="panorama">
        <?php while ($p = $paginasResult->fetch_assoc()): ?>
            <div class="panorama-page">
                <img
                    src="<?= htmlspecialchars($p['afbeelding']) ?>"
                    alt="<?= htmlspecialchars($p['titel']) ?>">

                <?php
                $pid    = $p['id'];
                $nummer = 1;

                if (!empty($hotspotsByPage[$pid])):
                    foreach ($hotspotsByPage[$pid] as $hs):

                        // standaard: pagina-afbeelding
                        $popupImage = $p['afbeelding'];

                        // als er een extra foto is voor deze hotspot, gebruik die
                        if (!empty($fotoByHotspot[$hs['id']][0])) {
                            $popupImage = $fotoByHotspot[$hs['id']][0];
                        }
                ?>
                        <div class="hotspot"
                             style="left: <?= (float) $hs['x'] ?>%; top: <?= (float) $hs['y'] ?>%;"
                             data-title="<?= htmlspecialchars($hs['titel']) ?>"
                             data-text="<?= htmlspecialchars($hs['tekst'] ?? '') ?>"
                             data-image="<?= htmlspecialchars($popupImage) ?>">
                            <?= $nummer ?>
                        </div>

                <?php
                        $nummer++;
                    endforeach;
                endif;
                ?>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Popup -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span id="popup-kruis">&times;</span>
            <h2 id="popup-title"></h2>
            <img id="popup-image" alt="">
            <p id="popup-text"></p>
        </div>
    </div>

    <!-- Nieuwe minimap in Sultan-stijl (met pijlen en thumbnails) -->
    <div class="panorama-minimap" id="panoramaMinimap">
        <button class="minimap-arrow left" id="arrow-left">&#10094;</button>

        <div class="minimap-track" id="minimap-track">
            <?php
            $thumbIndex = 0;
            while ($m = $paginasMinimapResult->fetch_assoc()):
            ?>
                <img class="minimap-thumb"
                     data-index="<?= $thumbIndex ?>"
                     src="<?= htmlspecialchars($m['afbeelding']) ?>"
                     alt="miniatuur <?= $thumbIndex + 1 ?>">
            <?php
                $thumbIndex++;
            endwhile;
            ?>
        </div>

        <button class="minimap-arrow right" id="arrow-right">&#10095;</button>
    </div>

    <!-- Zoom-knoppen van Sultan -->
    <div class="zoom-buttons">
        <button id="zoom-in">+</button>
        <button id="zoom-out">−</button>
        <button id="zoom-reset">Reset</button>
    </div>

    

    <script src="panorama.js"></script>
</body>

</html>
