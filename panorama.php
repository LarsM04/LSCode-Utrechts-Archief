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

<!-- mijn code sultan  -->
    <div class="panorama">
        <a href="startscherm.html" class="terug-knop">← Terug</a>



        <?php while ($hs = $hotspotsResult->fetch_assoc()): ?>
            <div class="hotspot"
                style="left: <?= (int)$hs['x'] ?>px; top: <?= (int)$hs['y'] ?>px;"
                data-title="<?= htmlspecialchars($hs['titel']) ?>"
                data-text="<?= htmlspecialchars($hs['tekst'] ?? '') ?>"
                data-image="Beeldmateriaal/Beeld02.png">
                <?= $hs['id'] ?>
            </div>
        <?php endwhile; ?>


<!-- mijn code sultan  -->
        <?php while ($p = $paginasResult->fetch_assoc()): ?>
            <img src="<?= htmlspecialchars($p['afbeelding']) ?>"
                alt="<?= htmlspecialchars($p['titel']) ?>">
        <?php endwhile; ?>

    </div>

<!-- mijn code sultan  -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span id="popup-kruis">&times;</span>
            <h2 id="popup-title"></h2>
            <img id="popup-image">
            <p id="popup-text"></p>
            
        </div>
    </div>


    


    <!-- MINI MAP  -->
<!-- MINI MAP MET AFBEELDINGEN -->
<div class="panorama-minimap" id="panoramaMinimap">
  <div class="minimap-track" id="minimapTrack">
    <?php
    $paginasResult2 = $conn->query("SELECT afbeelding FROM paginas ORDER BY id ASC");
    while ($m = $paginasResult2->fetch_assoc()):
    ?>
      <img src="<?= htmlspecialchars($m['afbeelding']) ?>">
    <?php endwhile; ?>
  </div>

  <div class="panorama-minimap-viewport" id="panoramaMinimapViewport"></div>
</div>

<!-- mijn code sultan  -->
<div id="vergrootglas" class="magnifier">
  <img id="vergrootInhoud" class="magnifier-inner">
</div>



    <script src="panorama.js"></script>
</body>

</html>