<?php
require 'auth_check.php';
require 'db.php';

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors  = [];
$success = '';

// standaard waarden
$pagina = [
    'id'        => $id,
    'titel'     => '',
    'afbeelding'=> ''
];

// bestaande pagina ophalen als id > 0
if ($id > 0) {
    $stmt = $conn->prepare('SELECT id, titel, afbeelding FROM paginas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $pagina = $row;
    } else {
        $errors[] = 'Pagina niet gevonden.';
    }
    $stmt->close();
}

// formulier verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int)($_POST['id'] ?? 0);
    $titel         = trim($_POST['titel'] ?? '');
    $oudeAfbeelding = $_POST['oude_afbeelding'] ?? '';

    if ($titel === '') {
        $errors[] = 'Titel is verplicht.';
    }

    // afbeelding uploaden (optioneel bij bewerken, verplicht bij nieuwe pagina)
    $afbeeldingPad = $oudeAfbeelding;

    if (!empty($_FILES['afbeelding']['name'])) {
        $uploadDir  = 'uploads/';
        $bestand    = $_FILES['afbeelding'];

        if ($bestand['error'] === UPLOAD_ERR_OK) {
            $ext   = pathinfo($bestand['name'], PATHINFO_EXTENSION);
            $naam  = 'panorama_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $doel  = $uploadDir . $naam;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($bestand['tmp_name'], $doel)) {
                $afbeeldingPad = $doel;
            } else {
                $errors[] = 'Afbeelding kon niet worden opgeslagen.';
            }
        } else {
            $errors[] = 'Fout bij uploaden van afbeelding.';
        }
    } else {
        // bij nieuwe pagina moet er écht een afbeelding zijn
        if ($id === 0 && $afbeeldingPad === '') {
            $errors[] = 'Afbeelding is verplicht bij een nieuwe pagina.';
        }
    }

    if (empty($errors)) {
        if ($id === 0) {
            // nieuwe pagina
            $stmt = $conn->prepare('INSERT INTO paginas (titel, afbeelding) VALUES (?, ?)');
            $stmt->bind_param('ss', $titel, $afbeeldingPad);
            if ($stmt->execute()) {
                header('Location: paginas_index.php');
                exit;
            } else {
                $errors[] = 'Fout bij opslaan: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            // bestaande pagina bijwerken
            $stmt = $conn->prepare('UPDATE paginas SET titel = ?, afbeelding = ? WHERE id = ?');
            $stmt->bind_param('ssi', $titel, $afbeeldingPad, $id);
            if ($stmt->execute()) {
                header('Location: paginas_index.php');
                exit;
            } else {
                $errors[] = 'Fout bij bijwerken: ' . $stmt->error;
            }
            $stmt->close();
        }
    }

    // formulier opnieuw vullen met laatste waarden
    $pagina['id']        = $id;
    $pagina['titel']     = $titel;
    $pagina['afbeelding']= $afbeeldingPad;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?= $pagina['id'] ? "Pagina bewerken" : "Nieuwe pagina" ?></title>
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
    <h1 class="page-title">
        <?= $pagina['id'] ? "Pagina bewerken" : "Nieuwe pagina" ?>
    </h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$pagina['id'] ?>">
        <input type="hidden" name="oude_afbeelding" value="<?= htmlspecialchars($pagina['afbeelding']) ?>">

        <label>Titel</label>
        <input type="text" name="titel" value="<?= htmlspecialchars($pagina['titel']) ?>" required>

        <label>Afbeelding (jpg/png)</label>
        <input type="file" name="afbeelding" accept="image/*">

        <?php if (!empty($pagina['afbeelding'])): ?>
            <p>Huidige afbeelding:</p>
            <img src="<?= htmlspecialchars($pagina['afbeelding']) ?>"
                 alt=""
                 style="max-width:200px; max-height:100px; object-fit:cover; margin-bottom:10px;">
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="paginas_index.php" class="btn btn-secondary">Annuleren</a>
    </form>
</main>
</body>
</html>
