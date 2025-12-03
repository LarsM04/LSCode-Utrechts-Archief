<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    // Gebruiker opzoeken
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $hash);
        $stmt->fetch();

        // Wachtwoord checken
        if (password_verify($password, $hash)) {
            // Inloggen ok
            $_SESSION["logged_in"] = true;
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Onjuiste gebruikersnaam of wachtwoord.";
        }
    } else {
        $error = "Onjuiste gebruikersnaam of wachtwoord.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .login-container {
            width: 300px; margin: 80px auto; padding: 20px;
            background: white; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        input[type=text], input[type=password] {
            width: 100%; padding: 8px; margin: 6px 0 12px;
        }
        input[type=submit] {
            width: 100%; padding: 8px; cursor: pointer;
        }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label>Gebruikersnaam</label>
        <input type="text" name="username" required>

        <label>Wachtwoord</label>
        <input type="password" name="password" required>

        <input type="submit" value="Inloggen">
    </form>
</div>
</body>
</html>
