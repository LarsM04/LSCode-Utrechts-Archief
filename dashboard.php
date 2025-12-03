<?php
require 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
    <h1 class="page-title">Beheerdashboard</h1>
    <p class="page-subtitle">Beheer de panorama-pagina's en hun hotspots.</p>

    <div class="card-grid">
        <div class="card">
            <div class="card-accent"></div>
            <h2>Pagina's beheren</h2>
            <p>Voeg nieuwe panorama-pagina's toe of wijzig titel en afbeelding.</p>
            <a class="btn btn-primary" href="paginas_index.php">Naar pagina's</a>
        </div>

        <div class="card">
            <div class="card-accent"></div>
            <h2>Hotspots beheren</h2>
            <p>Beheer hotspots op een geselecteerde pagina en pas tekst aan.</p>
            <a class="btn btn-secondary" href="hotspots_index.php">Naar hotspots</a>
        </div>

        <div class="card">
            <div class="card-accent"></div>
            <h2>Gebruikers</h2>
            <p>Maak extra beheerdersaccounts aan.</p>
            <a class="btn btn-primary" href="create_user.php">Nieuwe gebruiker</a>
        </div>
    </div>
</main>
</body>
</html>
