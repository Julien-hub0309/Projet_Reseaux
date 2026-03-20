<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Back_end/login.php");
    exit;
}
require_once '../Back_end/db.php';

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_POST['delete_news'])) {
    $stmt = $pdo->prepare("DELETE FROM live_info WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}
if (isset($_POST['delete_absence'])) {
    $stmt = $pdo->prepare("DELETE FROM teacher_status WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}

// --- LOGIQUE D'AJOUT ---
if (isset($_POST['add_news'])) {
    $stmt = $pdo->prepare("INSERT INTO live_info (title, content, source) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['content'], $_POST['source']]);
}

if (isset($_POST['add_absence'])) {
    $stmt = $pdo->prepare("INSERT INTO teacher_status (teacher_name, subject, status, room) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['teacher'], $_POST['subject'], $_POST['status'], $_POST['room']]);
}

// Récupération pour l'affichage
$newsList = $pdo->query("SELECT * FROM live_info ORDER BY created_at DESC")->fetchAll();
$absences = $pdo->query("SELECT * FROM teacher_status")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Lycée Lavoisier</title>
    <link rel="stylesheet" href="../Module/CSS/style.css">
</head>
<body>
    <header>
        <h1>Gestionnaire Lavoisier - <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a href="logout.php">Déconnexion</a>
    </header>

    <div class="container">
        <section>
            <h2>📢 Ajouter une Actualité</h2>
            <form method="POST">
                <input type="text" name="title" placeholder="Titre" required>
                <textarea name="content" placeholder="Message"></textarea>
                <select name="source">
                    <option value="Lycée">Lycée</option>
                    <option value="Internat">Internat</option>
                    <option value="Région">Région</option>
                </select>
                <button type="submit" name="add_news">Publier</button>
            </form>
            
            <h3>Actualités actives</h3>
            <?php foreach($newsList as $n): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $n['id']; ?>">
                    <p><?php echo $n['title']; ?> <button type="submit" name="delete_news">❌</button></p>
                </form>
            <?php endforeach; ?>
        </section>

        <hr>

        <section>
            <h2>🚫 Signaler une Absence</h2>
            <form method="POST">
                <input type="text" name="teacher" placeholder="Nom du Prof" required>
                <input type="text" name="subject" placeholder="Matière" required>
                <select name="status">
                    <option value="Absent">Absent</option>
                    <option value="Remplacé">Remplacé</option>
                </select>
                <input type="text" name="room" placeholder="Salle">
                <button type="submit" name="add_absence">Ajouter l'absence</button>
            </form>

            <h3>Liste des absences</h3>
            <?php foreach($absences as $a): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                    <p><?php echo $a['teacher_name']; ?> (<?php echo $a['subject']; ?>) <button type="submit" name="delete_absence">❌</button></p>
                </form>
            <?php endforeach; ?>
        </section>
    </div>
</body>
</html>