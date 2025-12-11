<?php
require 'auth_check.php';
require 'db.php';

// alle pagina's ophalen
$result = $conn->query('SELECT id, titel, afbeelding FROM paginas ORDER BY id ASC');
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Pagina's beheren</title>
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
    <h1 class="page-title">Pagina's beheren</h1>

    <p>
        <a class="btn btn-primary" href="paginas_edit.php">+ Nieuwe pagina</a>
    </p>

    <table cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Titel</th>
            <th>Afbeelding</th>
            <th>Acties</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= htmlspecialchars($row['titel']) ?></td>
                <td>
                    <?php if (!empty($row['afbeelding'])): ?>
                        <img src="<?= htmlspecialchars($row['afbeelding']) ?>"
                             alt=""
                             style="max-width:140px; max-height:70px; object-fit:cover;">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="paginas_edit.php?id=<?= (int)$row['id'] ?>">Bewerken</a> |
                    <a href="paginas_delete.php?id=<?= (int)$row['id'] ?>"
                       onclick="return confirm('Weet je zeker dat je deze pagina (en bijbehorende hotspots) wilt verwijderen?');">
                        Verwijderen
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="4">Nog geen pagina's aangemaakt.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
