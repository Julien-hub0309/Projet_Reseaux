<?php
session_start();
require_once 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PROTECTION STRICTE
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_POST['delete_news'])) {
    $stmt = $pdo->prepare("DELETE FROM live_info WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}

// --- LOGIQUE D'AJOUT ---
if (isset($_POST['add_news'])) {
    $stmt = $pdo->prepare("INSERT INTO live_info (title, content, source) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['content'], $_POST['source']]);
}

// Récupération
$newsList = $pdo->query("SELECT * FROM live_info ORDER BY created_at DESC")->fetchAll();
$absences = $pdo->query("SELECT * FROM teacher_status")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Lavoisier</title>
    <link rel="stylesheet" href="./Module/CSS/style.css">
    <style>
        .admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 20px; }
        form { background: white; padding: 15px; border-radius: 8px; }
        input, textarea, select { width: 100%; margin-bottom: 10px; padding: 8px; }
    </style>
</head>
<body style="background: #eee;">
    <header style="background: #333; color: white; padding: 10px 20px; display: flex; justify-content: space-between;">
        <h1>Dashboard Admin</h1>
        <nav>
            <a href="../index.php" style="color: white;">Voir le site</a> | 
            <a href="./Back_end/logout.php" style="color: #ff7675;">Déconnexion</a>
        </nav>
    </header>

    <div class="admin-grid">
        <section class="card">
            <h2>📢 Publier une info</h2>
            <form method="POST">
                <input type="text" name="title" placeholder="Titre" required>
                <textarea name="content" placeholder="Message" rows="4"></textarea>
                <select name="source">
                    <option value="Lycée">Lycée</option>
                    <option value="Internat">Internat</option>
                    <option value="Région">Région</option>
                </select>
                <button type="submit" name="add_news" style="background: #27ae60; color:white; border:none; padding:10px; cursor:pointer;">Publier</button>
            </form>
        </section>

        <section class="card">
            <h2>Liste des actualités</h2>
            <?php foreach($newsList as $n): ?>
                <div style="border-bottom: 1px solid #ddd; padding: 5px 0;">
                    <form method="POST" style="display:inline; padding:0; background:none;">
                        <input type="hidden" name="id" value="<?php echo $n['id']; ?>">
                        <strong><?php echo htmlspecialchars($n['title']); ?></strong>
                        <button type="submit" name="delete_news" style="background:none; border:none; cursor:pointer;">❌</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</body>
</html>