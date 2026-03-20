<?php
$host = 'localhost';
$dbname = 'lycee_lavoisier';
$user = 'webmaster';
$pass = 'Admin123';

try {
    // Ajout de l'encodage pour éviter les problèmes d'accents
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>