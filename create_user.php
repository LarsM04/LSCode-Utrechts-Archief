<?php
session_start();
require 'db.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username  = trim($_POST["username"] ?? '');
    $password  = $_POST["password"] ?? '';
    $password2 = $_POST["password2"] ?? '';


    if ($username === '' || $password === '' || $password2 === '') {
        $error = "Vul alle velden in.";
    } elseif ($password !== $password2) {
        $error = "De wachtwoorden komen niet overeen.";
    } else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Deze gebruikersnaam bestaat al.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $username, $hash);

            if ($stmt_insert->execute()) {
                $success = "Gebruiker succesvol aangemaakt!";
            } else {
                $error = "Fout bij aanmaken gebruiker: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Gebruiker aanmaken</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .container {
            width: 320px;
            margin: 80px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 8px;
            margin: 6px 0 12px;
        }

        input[type=submit] {
            width: 100%;
            padding: 8px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Nieuwe gebruiker aanmaken</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" action="create_user.php">
            <label>Gebruikersnaam</label>
            <input type="text" name="username" required>

            <label>Wachtwoord</label>
            <input type="password" name="password" required>

            <label>Herhaal wachtwoord</label>
            <input type="password" name="password2" required>

            <input type="submit" value="Gebruiker aanmaken">
        </form>

        <p><a href="dashboard.php">Terug naar dashboard</a></p>
    </div>
</body>

</html>