<?php
session_start();
require_once 'db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    // On récupère TOUTES les colonnes dont le 'role'
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?"); 
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // CRUCIAL pour le dashboard

        // Redirection intelligente
        if ($_SESSION['role'] === 'admin') {
            header('Location: dashboard.php');
        } else {
            header('Location: ../index.php');
        }
        exit();
    } else {
        $error_message = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Lavoisier</title>
    <link rel="stylesheet" href="../Module/CSS/style.css">
</head>
<body>
    <main class="main-content">
        <section class="card" style="max-width: 400px; margin: 50px auto;">
            <h2>Connexion</h2>
            <?php if ($error_message): ?>
                <p style="color:red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username_or_email" placeholder="Nom d'utilisateur" required style="width:100%; margin-bottom:10px;">
                <input type="password" name="password" placeholder="Mot de passe" required style="width:100%; margin-bottom:10px;">
                <button type="submit" style="width:100%;">Se connecter</button>
            </form>
        </section>
    </main>
</body>
</html>