<?php
require 'auth_check.php';
require 'db.php';


function slaHotspotFotosOp(mysqli $conn, int $hotspotId, array &$errors): void
{

    if (empty($_FILES['extra_fotos']['name'][0])) {
        return;
    }

    $uploadDir = 'uploads/hotspots/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['extra_fotos']['name'] as $index => $origineleNaam) {
        $errorCode = $_FILES['extra_fotos']['error'][$index];

        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            $errors[] = 'Fout bij uploaden van een aanvullende foto.';
            continue;
        }

        $tmpName = $_FILES['extra_fotos']['tmp_name'][$index];
        $ext     = pathinfo($origineleNaam, PATHINFO_EXTENSION);


        if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $errors[] = 'Alleen afbeeldingen (jpg, png, gif, webp) zijn toegestaan.';
            continue;
        }

        $nieuwBestand = 'hotspot_' . $hotspotId . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $doelPad      = $uploadDir . $nieuwBestand;

        if (move_uploaded_file($tmpName, $doelPad)) {
            $stmtFoto = $conn->prepare('INSERT INTO hotspot_fotos (hotspot_id, bestand) VALUES (?, ?)');
            $stmtFoto->bind_param('is', $hotspotId, $doelPad);
            $stmtFoto->execute();
            $stmtFoto->close();
        } else {
            $errors[] = 'Een aanvullende foto kon niet worden opgeslagen.';
        }
    }
}

$pagina_id = isset($_GET['pagina_id']) ? (int) $_GET['pagina_id'] : 0;


$pagina_data = null;
if ($pagina_id > 0) {
    $stmtP = $conn->prepare('SELECT titel, afbeelding FROM paginas WHERE id = ?');
    $stmtP->bind_param('i', $pagina_id);
    $stmtP->execute();
    $resultP     = $stmtP->get_result();
    $pagina_data = $resultP->fetch_assoc();
    $stmtP->close();
}

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int) ($_POST['id'] ?? 0);
    $pagina_id = (int) ($_POST['pagina_id'] ?? 0);
    $x         = (float) ($_POST['x'] ?? 0);
    $y         = (float) ($_POST['y'] ?? 0);

    $titel = trim($_POST['titel'] ?? '');
    $tekst = trim($_POST['tekst'] ?? '');

    if ($pagina_id <= 0) {
        $errors[] = 'Pagina ontbreekt.';
    }

    if ($titel === '') {
        $errors[] = 'Titel is verplicht.';
    }

    if (empty($errors)) {
        if ($id === 0) {

            $stmt = $conn->prepare("
                INSERT INTO hotspots (pagina_id, x, y, titel)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('iiis', $pagina_id, $x, $y, $titel);

            if ($stmt->execute()) {
                $hotspot_id = $stmt->insert_id;
                $stmt->close();


                $stmtInfo = $conn->prepare('INSERT INTO hotspot_info (hotspot_id, tekst) VALUES (?, ?)');
                $stmtInfo->bind_param('is', $hotspot_id, $tekst);
                $stmtInfo->execute();
                $stmtInfo->close();


                slaHotspotFotosOp($conn, $hotspot_id, $errors);

                if (empty($errors)) {
                    header('Location: hotspots_index.php?pagina_id=' . $pagina_id);
                    exit;
                }
            } else {
                $errors[] = 'Fout bij opslaan: ' . $stmt->error;
                $stmt->close();
            }
        } else {

            $stmt = $conn->prepare("
                UPDATE hotspots
                SET x = ?, y = ?, titel = ?
                WHERE id = ? AND pagina_id = ?
            ");
            $stmt->bind_param('iisii', $x, $y, $titel, $id, $pagina_id);

            if ($stmt->execute()) {
                $stmt->close();


                $stmtCheck = $conn->prepare('SELECT id FROM hotspot_info WHERE hotspot_id = ?');
                $stmtCheck->bind_param('i', $id);
                $stmtCheck->execute();
                $stmtCheck->store_result();

                if ($stmtCheck->num_rows > 0) {
                    $stmtInfo = $conn->prepare('UPDATE hotspot_info SET tekst = ? WHERE hotspot_id = ?');
                    $stmtInfo->bind_param('si', $tekst, $id);
                } else {
                    $stmtInfo = $conn->prepare('INSERT INTO hotspot_info (hotspot_id, tekst) VALUES (?, ?)');
                    $stmtInfo->bind_param('is', $id, $tekst);
                }

                $stmtCheck->close();

                $stmtInfo->execute();
                $stmtInfo->close();


                slaHotspotFotosOp($conn, $id, $errors);

                if (empty($errors)) {
                    header('Location: hotspots_index.php?pagina_id=' . $pagina_id);
                    exit;
                }
            } else {
                $errors[] = 'Fout bij bijwerken: ' . $stmt->error;
                $stmt->close();
            }
        }
    }
}


$hotspot = [
    'id'        => $id,
    'pagina_id' => $pagina_id,
    'x'         => 0,
    'y'         => 0,
    'titel'     => '',
    'tekst'     => ''
];


if ($id > 0) {
    $stmt = $conn->prepare("
        SELECT h.id, h.pagina_id, h.x, h.y, h.titel, i.tekst
        FROM hotspots h
        LEFT JOIN hotspot_info i ON i.hotspot_id = h.id
        WHERE h.id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $hotspot   = $row;
        $pagina_id = (int) $row['pagina_id'];
    }

    $stmt->close();
}


$hotspotFotos = [];
if ($id > 0) {
    $stmtF = $conn->prepare('SELECT id, bestand, bijschrift FROM hotspot_fotos WHERE hotspot_id = ? ORDER BY id ASC');
    $stmtF->bind_param('i', $id);
    $stmtF->execute();
    $resF = $stmtF->get_result();
    while ($r = $resF->fetch_assoc()) {
        $hotspotFotos[] = $r;
    }
    $stmtF->close();
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Hotspot bewerken' : 'Nieuwe hotspot' ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="header">
        <div class="logo">HUA Panorama CMS</div>
        <nav>
            <span>Ingelogd als <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="dashboard.php">Dashboard</a>
            <a href="paginas_index.php">Pagina's</a>
            <a href="hotspots_index.php">Hotspots</a>
            <a href="create_user.php">Nieuwe gebruiker</a>
            <a href="logout.php">Uitloggen</a>
        </nav>
    </header>

    <main class="page">
        <h1 class="page-title"><?= $id ? 'Hotspot bewerken' : 'Nieuwe hotspot' ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($pagina_data): ?>
            <p><strong>Klik op de afbeelding om de positie van de hotspot te bepalen.</strong></p>

            <div class="hotspot-image-wrapper">
                <img
                    src="<?= htmlspecialchars($pagina_data['afbeelding']) ?>"
                    alt="<?= htmlspecialchars($pagina_data['titel']) ?>"
                    id="panoramaImage">

                <div id="hotspotPreview"></div>
            </div>
            <br>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int) $hotspot['id'] ?>">
            <input type="hidden" name="pagina_id" value="<?= (int) $pagina_id ?>">

            <p><strong>Pagina ID:</strong> <?= (int) $pagina_id ?></p>

            <label>X positie (%)</label>
            <input
                type="number"
                name="x"
                step="0.01"
                min="0"
                max="100"
                value="<?= htmlspecialchars($hotspot['x']) ?>">

            <label>Y positie (%)</label>
            <input
                type="number"
                name="y"
                step="0.01"
                min="0"
                max="100"
                value="<?= htmlspecialchars($hotspot['y']) ?>">

            <label>Titel</label>
            <input type="text" name="titel" value="<?= htmlspecialchars($hotspot['titel']) ?>" required>

            <label>Toelichting / tekst</label>
            <textarea name="tekst" rows="5" cols="50"><?= htmlspecialchars($hotspot['tekst'] ?? '') ?></textarea>

            <label>Aanvullende foto’s (meerdere toegestaan)</label>
            <input type="file" name="extra_fotos[]" accept="image/*" multiple>

            <?php if (!empty($hotspotFotos)): ?>
                <label>Bestaande aanvullende foto’s</label>
                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                    <?php foreach ($hotspotFotos as $f): ?>
                        <div style="text-align:center; font-size:12px;">
                            <img src="<?= htmlspecialchars($f['bestand']) ?>"
                                alt=""
                                style="max-width:120px; max-height:80px; object-fit:cover; display:block; margin-bottom:4px;">
                            <a href="hotspot_foto_delete.php?id=<?= (int)$f['id'] ?>&hotspot_id=<?= (int)$hotspot['id'] ?>&pagina_id=<?= (int)$pagina_id ?>"
                                onclick="return confirm('Deze foto verwijderen?');">
                                Verwijderen
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="hotspots_index.php?pagina_id=<?= (int) $pagina_id ?>" class="btn btn-secondary">Annuleren</a>
        </form>
    </main>

    <script>
        const img = document.getElementById('panoramaImage');
        const preview = document.getElementById('hotspotPreview');
        const inputX = document.querySelector('input[name="x"]');
        const inputY = document.querySelector('input[name="y"]');

        if (img && preview && inputX && inputY) {
            function updatePreviewFromInputs() {
                const x = parseFloat(inputX.value);
                const y = parseFloat(inputY.value);

                if (!isNaN(x) && !isNaN(y)) {
                    preview.style.left = x + '%';
                    preview.style.top = y + '%';
                    preview.style.display = 'block';
                }
            }

            img.addEventListener('click', function(e) {
                const rect = img.getBoundingClientRect();

                const offsetX = e.clientX - rect.left;
                const offsetY = e.clientY - rect.top;

                const xPercent = (offsetX / rect.width) * 100;
                const yPercent = (offsetY / rect.height) * 100;

                inputX.value = xPercent.toFixed(2);
                inputY.value = yPercent.toFixed(2);

                updatePreviewFromInputs();
            });

            updatePreviewFromInputs();
        }
    </script>
</body>

</html>