<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id']   = $user_id;
            $_SESSION['username']  = $username;

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Onjuiste gebruikersnaam of wachtwoord.';
    } else {
        $error = 'Onjuiste gebruikersnaam of wachtwoord.';
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
        body {
            font-family: Arial, sans-serif;
            background: #fef2f2;
            margin: 0;
            padding: 0;
        }

        .login-container {
            width: 320px;
            margin: 80px auto;
            padding: 24px 22px 26px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #fee2e2;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 16px;
            text-align: center;
            color: #111827;
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 8px 10px;
            margin: 6px 0 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
        }

        input[type=text]:focus,
        input[type=password]:focus {
            outline: 2px solid #fee2e2;
            border-color: #dc2626;
        }

        input[type=submit] {
            width: 100%;
            padding: 9px 0;
            cursor: pointer;
            border-radius: 999px;
            border: none;
            background: #dc2626;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 8px 18px rgba(220, 38, 38, 0.4);
            transition: background 0.15s ease, transform 0.1s ease,
                box-shadow 0.1s ease;
        }

        input[type=submit]:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(220, 38, 38, 0.55);
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 12px;
        }
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
