CREATE DATABASE IF NOT EXISTS lycee_lavoisier;
USE lycee_lavoisier;

-- Configuration des écrans (Hall, Profs, Internat)
CREATE TABLE display_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location_name VARCHAR(50) NOT NULL, -- 'hall', 'profs', 'internat'
    theme_color VARCHAR(20) DEFAULT '#0081bc'
);

-- Contenu universel
CREATE TABLE universal_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('news', 'menu', 'weather', 'rss') NOT NULL,
    title VARCHAR(255),
    body TEXT,
    target_location VARCHAR(50) DEFAULT 'all', -- Pour cibler un écran spécifique [cite: 39, 60]
    date_info DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les informations en direct (Flux RSS / Infos Lycée)
CREATE TABLE live_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    source ENUM('Lycée', 'Internat', 'Région') DEFAULT 'Lycée',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour la gestion fine des professeurs (Absences et Remplacements)
CREATE TABLE teacher_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_name VARCHAR(100),
    subject VARCHAR(100),
    status ENUM('Absent', 'Remplacé') DEFAULT 'Absent',
    replacement_teacher VARCHAR(100) DEFAULT NULL, -- Pour les remplacements courte durée
    room VARCHAR(50),
    start_time DATETIME,
    end_time DATETIME
);

-- Table pour le menu automatisé
CREATE TABLE daily_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_date DATE UNIQUE,
    meals_json TEXT -- Stockage structuré des plats
);

-- Table pour sécuriser l'accès au Back Office (Exigence 76 & 78)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user'
);


-- Message d'accueil pour le Hall (Exigence 59)
INSERT INTO live_info (title, content, source) 
VALUES ('Bienvenue', 'Le Lycée Lavoisier souhaite la bienvenue aux intervenants de la conférence Métiers.', 'Lycée');

-- Remplacement pour la Salle des Profs (Exigence 47)
INSERT INTO teacher_status (teacher_name, subject, status, replacement_teacher, room) 
VALUES ('M. Durand', 'Mathématiques', 'Remplacé', 'Mme. Martin', 'Salle 204');

-- Information spécifique Internat (Exigence 61)
INSERT INTO live_info (title, content, source) 
VALUES ('Soirée Cinéma', 'Projection au foyer ce soir à 20h.', 'Internat');