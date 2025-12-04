<?php
require 'auth_check.php';
require 'db.php';

$pagina_id = isset($_GET['pagina_id']) ? (int)$_GET['pagina_id'] : 0;
$id        = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $pagina_id = (int)($_POST['pagina_id'] ?? 0);
    $x         = (int)($_POST['x'] ?? 0);
    $y         = (int)($_POST['y'] ?? 0);
    $titel     = trim($_POST['titel'] ?? '');
    $tekst     = trim($_POST['tekst'] ?? '');

    if ($pagina_id <= 0)   $errors[] = "Pagina ontbreekt.";
    if ($titel === '')     $errors[] = "Titel is verplicht.";

    if (empty($errors)) {
        if ($id === 0) {

            $stmt = $conn->prepare("
                INSERT INTO hotspots (pagina_id, x, y, titel)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiis", $pagina_id, $x, $y, $titel);
            if ($stmt->execute()) {
                $hotspot_id = $stmt->insert_id;
                $stmt->close();


                $stmtInfo = $conn->prepare("
                    INSERT INTO hotspot_info (hotspot_id, tekst) VALUES (?, ?)
                ");
                $stmtInfo->bind_param("is", $hotspot_id, $tekst);
                $stmtInfo->execute();
                $stmtInfo->close();

                header("Location: hotspots_index.php?pagina_id=" . $pagina_id);
                exit;
            } else {
                $errors[] = "Fout bij opslaan: " . $stmt->error;
                $stmt->close();
            }
        } else {

            $stmt = $conn->prepare("
                UPDATE hotspots
                SET x = ?, y = ?, titel = ?
                WHERE id = ? AND pagina_id = ?
            ");
            $stmt->bind_param("iisii", $x, $y, $titel, $id, $pagina_id);
            if ($stmt->execute()) {
                $stmt->close();


                $stmtCheck = $conn->prepare("SELECT id FROM hotspot_info WHERE hotspot_id = ?");
                $stmtCheck->bind_param("i", $id);
                $stmtCheck->execute();
                $stmtCheck->store_result();

                if ($stmtCheck->num_rows > 0) {
                    $stmtInfo = $conn->prepare("
                        UPDATE hotspot_info SET tekst = ? WHERE hotspot_id = ?
                    ");
                    $stmtInfo->bind_param("si", $tekst, $id);
                } else {
                    $stmtInfo = $conn->prepare("
                        INSERT INTO hotspot_info (hotspot_id, tekst) VALUES (?, ?)
                    ");
                    $stmtInfo->bind_param("is", $id, $tekst);
                }
                $stmtCheck->close();

                $stmtInfo->execute();
                $stmtInfo->close();

                header("Location: hotspots_index.php?pagina_id=" . $pagina_id);
                exit;
            } else {
                $errors[] = "Fout bij bijwerken: " . $stmt->error;
                $stmt->close();
            }
        }
    }
}


$hotspot = [
    'id'     => $id,
    'pagina_id' => $pagina_id,
    'x'      => 0,
    'y'      => 0,
    'titel'  => '',
    'tekst'  => ''
];

if ($id > 0) {
    $stmt = $conn->prepare("
        SELECT h.id, h.pagina_id, h.x, h.y, h.titel, i.tekst
        FROM hotspots h
        LEFT JOIN hotspot_info i ON i.hotspot_id = h.id
        WHERE h.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $hotspot = $row;
        $pagina_id = (int)$row['pagina_id'];
    }
    $stmt->close();
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
            <span>Ingelogd als <?= htmlspecialchars($_SESSION["username"]); ?></span>
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
            <div style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?= (int)$hotspot['id'] ?>">
            <input type="hidden" name="pagina_id" value="<?= (int)$pagina_id ?>">

            <p><strong>Pagina ID:</strong> <?= (int)$pagina_id ?></p>

            <label>X positie (px)</label><br>
            <input type="number" name="x" value="<?= (int)$hotspot['x'] ?>"><br><br>

            <label>Y positie (px)</label><br>
            <input type="number" name="y" value="<?= (int)$hotspot['y'] ?>"><br><br>

            <label>Titel</label><br>
            <input type="text" name="titel" value="<?= htmlspecialchars($hotspot['titel']) ?>" required><br><br>

            <label>Toelichting / tekst</label><br>
            <textarea name="tekst" rows="5" cols="50"><?= htmlspecialchars($hotspot['tekst'] ?? '') ?></textarea><br><br>

            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="hotspots_index.php?pagina_id=<?= (int)$pagina_id ?>" class="btn btn-secondary">Annuleren</a>
        </form>
    </main>
</body>

</html>