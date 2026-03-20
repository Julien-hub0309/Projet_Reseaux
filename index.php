<?php
session_start();
require_once './Back_end/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sécurité : redirection si pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ./Back_end/login.php");
    exit;
}

// --- RÉCUPÉRATION DES DONNÉES ---
// 1. Les Actus (live_info)
$newsList = $pdo->query("SELECT * FROM live_info ORDER BY created_at DESC LIMIT 5")->fetchAll();

// 2. Les Absences
$absences = $pdo->query("SELECT * FROM teacher_status WHERE end_time >= NOW() OR end_time IS NULL")->fetchAll();

// 3. Le Menu du jour
$stmt = $pdo->prepare("SELECT meals_json FROM daily_menu WHERE day_date = CURRENT_DATE");
$stmt->execute();
$menuData = $stmt->fetch();

// 4. Contenu universel (météo)
$universalContent = $pdo->query("SELECT * FROM universal_content")->fetchAll();
$themeColor = "#0081bc"; 

// --- RÉCUPÉRATION FLUX RSS ---
$rss_url = "https://www.lemonde.fr/education/rss_full.xml";
$rss_data = @simplexml_load_file($rss_url);
$rss_items = [];
if ($rss_data) {
    for ($i = 0; $i < 5; $i++) {
        if (isset($rss_data->channel->item[$i])) $rss_items[] = $rss_data->channel->item[$i];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail - Lycée Lavoisier</title>
    <link rel="stylesheet" href="./Module/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --main-color: <?php echo $themeColor; ?>; 
            --bg-dark: #2c3e50;
        }

        /* Menu Utilisateur Moderne */
        .user-menu {
            float: right;
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 15px;
            border-radius: 30px;
            backdrop-filter: blur(5px);
        }
        .user-info { color: white; display: flex; align-items: center; gap: 8px; font-weight: 500; }
        .admin-link { color: #ffd700; font-size: 1.2rem; transition: 0.3s; }
        .admin-link:hover { transform: scale(1.1); }
        .logout-link { color: #ff7675; font-size: 1.1rem; }

        /* Style RSS Footer */
        .rss-footer {
            background: var(--bg-dark);
            color: white;
            padding: 20px;
            margin-top: 20px;
            border-top: 5px solid var(--main-color);
        }
        .rss-container { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; }
        .rss-item { 
            min-width: 280px; background: rgba(255,255,255,0.05); 
            padding: 12px; border-radius: 8px; font-size: 0.85rem; 
        }
        .rss-item a { color: #00d1b2; text-decoration: none; font-weight: bold; display: block; margin-bottom: 5px; }
        
        .main-header { background: var(--main-color); color: white; padding: 20px 40px; }
        .card { background: white; border-radius: 10px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; color: white; }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="user-menu">
            <div class="user-info">
                <i class="fas fa-user-circle fa-lg"></i>
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="dashboard.php" class="admin-link" title="Administration"><i class="fas fa-user-shield"></i></a>
            <?php endif; ?>
            <a href="./Back_end/logout.php" class="logout-link" title="Déconnexion"><i class="fas fa-sign-out-alt"></i></a>
        </div>

        <div class="header-info">
            <h1 id="clock" style="margin:0; font-size: 2.5rem;">00:00:00</h1> 
            <p id="current-date" style="margin:0; opacity:0.9;"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?> | Lycée Lavoisier</p>
        </div>
    </header>

    <main style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 20px;">
        
        <aside>
            <div class="card">
                <h3><i class="fas fa-cloud-sun"></i> Météo</h3>
                <?php 
                    $weather = array_filter($universalContent, fn($c) => $c['type'] == 'weather');
                    echo !empty($weather) ? htmlspecialchars(reset($weather)['body']) : "18°C - Éclaircies";
                ?>
            </div>
            <div class="card">
                <h3><i class="fas fa-utensils"></i> Menu du jour</h3>
                <p><?php echo $menuData ? nl2br(htmlspecialchars($menuData['meals_json'])) : "<em>Menu non renseigné.</em>"; ?></p>
            </div>
        </aside>

        <section>
            <h2 style="color: var(--main-color);"><i class="fas fa-rss"></i> Actualités du Lycée</h2>
            <?php foreach ($newsList as $news): ?>
                <article class="card">
                    <span class="badge" style="background: var(--main-color);"><?php echo htmlspecialchars($news['source']); ?></span>
                    <h4><?php echo htmlspecialchars($news['title']); ?></h4>
                    <p style="font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                </article>
            <?php endforeach; ?>
        </section>

        <aside>
            <div class="card">
                <h3><i class="fas fa-user-slash"></i> Absences</h3>
                <ul style="list-style:none; padding:0;">
                    <?php if (empty($absences)): ?>
                        <li><i class="fas fa-check-circle" style="color:green;"></i> Aucun absent</li>
                    <?php else: ?>
                        <?php foreach ($absences as $abs): ?>
                            <li style="margin-bottom:10px; border-bottom: 1px solid #eee; padding-bottom:5px;">
                                <strong><?php echo htmlspecialchars($abs['teacher_name']); ?></strong><br>
                                <span class="badge" style="background:#e74c3c;"><?php echo htmlspecialchars($abs['status']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>
    </main>

    <footer class="rss-footer">
        <h3 style="margin-top:0;"><i class="fas fa-broadcast-tower"></i> Actualités Éducation</h3>
        <div class="rss-container">
            <?php if (!empty($rss_items)): ?>
                <?php foreach ($rss_items as $item): ?>
                    <div class="rss-item">
                        <a href="<?php echo $item->link; ?>" target="_blank"><?php echo htmlspecialchars($item->title); ?></a>
                        <p><?php echo substr(strip_tags($item->description), 0, 80) . '...'; ?></p>
                        <small><i class="far fa-clock"></i> <?php echo date('d/m H:i', strtotime($item->pubDate)); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Flux RSS indisponible.</p>
            <?php endif; ?>
        </div>
    </footer>

    <script>
        function updateClock() {
            document.getElementById('clock').textContent = new Date().toLocaleTimeString('fr-FR');
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>