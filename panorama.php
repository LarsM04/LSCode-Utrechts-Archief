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

<div class="panorama">
    <a href="startscherm.html" class="terug-knop">← Terug</a>

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
                    ?>
                    <div class="hotspot"
                         style="left: <?= (float) $hs['x'] ?>%; top: <?= (float) $hs['y'] ?>%;"
                         data-title="<?= htmlspecialchars($hs['titel']) ?>"
                         data-text="<?= htmlspecialchars($hs['tekst'] ?? '') ?>"
                         data-image="Beeldmateriaal/Beeld02.png">
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

<!-- Popup in Sultan-stijl -->
<div id="popup" class="popup">
    <div class="popup-content">
        <span id="popup-kruis">&times;</span>
        <h2 id="popup-title"></h2>
        <img id="popup-image" alt="">
        <p id="popup-text"></p>
    </div>
</div>

<!-- Mini-map onderin -->
<div class="panorama-minimap" id="panoramaMinimap">
    <div class="minimap-track" id="minimapTrack">
        <?php while ($m = $paginasMinimapResult->fetch_assoc()): ?>
            <img src="<?= htmlspecialchars($m['afbeelding']) ?>" alt="miniatuur">
        <?php endwhile; ?>
    </div>
</div>

<!-- Vergrootglas -->
<div id="vergrootglas" class="magnifier">
    <img id="vergrootInhoud" class="magnifier-inner" alt="">
</div>

<script src="panorama.js"></script>
</body>
</html>
