<?php
require 'auth_check.php';
require 'db.php';


$paginasResult = $conn->query("SELECT id, titel FROM paginas ORDER BY id ASC");


$pagina_id = isset($_GET['pagina_id']) ? (int)$_GET['pagina_id'] : 0;


if ($pagina_id === 0 && $paginasResult->num_rows > 0) {
    $first = $paginasResult->fetch_assoc();
    $pagina_id = (int)$first['id'];

    $paginasResult->data_seek(0);
}


$hotspots = [];
if ($pagina_id > 0) {
    $stmt = $conn->prepare("
        SELECT h.id, h.x, h.y, h.titel, i.tekst
        FROM hotspots h
        LEFT JOIN hotspot_info i ON i.hotspot_id = h.id
        WHERE h.pagina_id = ?
        ORDER BY h.id
    ");
    $stmt->bind_param("i", $pagina_id);
    $stmt->execute();
    $hotspotsResult = $stmt->get_result();
    while ($row = $hotspotsResult->fetch_assoc()) {
        $hotspots[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Hotspots beheren</title>
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
        <h1 class="page-title">Hotspots beheren</h1>


        <form method="get" style="margin-bottom: 20px;">
            <label>Pagina:</label>
            <select name="pagina_id" onchange="this.form.submit()">
                <?php while ($p = $paginasResult->fetch_assoc()): ?>
                    <option value="<?= (int)$p['id'] ?>"
                        <?= $pagina_id == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['titel']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <noscript><button type="submit">Toon</button></noscript>
        </form>

        <?php if ($pagina_id > 0): ?>
            <p>
                <a class="btn btn-primary"
                    href="hotspots_edit.php?pagina_id=<?= $pagina_id ?>">+ Nieuwe hotspot</a>
            </p>

            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <th>X</th>
                    <th>Y</th>
                    <th>Titel</th>
                    <th>Tekst</th>
                    <th>Acties</th>
                </tr>
                <?php foreach ($hotspots as $hs): ?>
                    <tr>
                        <td><?= (int)$hs['id'] ?></td>
                        <td><?= (int)$hs['x'] ?></td>
                        <td><?= (int)$hs['y'] ?></td>
                        <td><?= htmlspecialchars($hs['titel']) ?></td>
                        <td><?= htmlspecialchars($hs['tekst'] ?? '') ?></td>
                        <td>
                            <a href="hotspots_edit.php?id=<?= (int)$hs['id'] ?>&pagina_id=<?= $pagina_id ?>">Bewerken</a> |
                            <a href="hotspots_delete.php?id=<?= (int)$hs['id'] ?>&pagina_id=<?= $pagina_id ?>"
                                onclick="return confirm('Weet je zeker dat je deze hotspot wilt verwijderen?');">
                                Verwijderen
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($hotspots)): ?>
                    <tr>
                        <td colspan="6">Nog geen hotspots voor deze pagina.</td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php else: ?>
            <p>Maak eerst een pagina aan voordat je hotspots toevoegt.</p>
        <?php endif; ?>
    </main>
</body>

</html>