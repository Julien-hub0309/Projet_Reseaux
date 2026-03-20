🏫 Portail d'Information - Lycée Lavoisier
📝 Présentation
Ce projet est une application web dynamique conçue pour le Lycée Lavoisier. Elle permet de diffuser en temps réel des informations essentielles sur des écrans répartis dans l'établissement (Hall, Salle des professeurs, Internat).

L'application se compose de deux parties :

Le Portail (Index) : Un affichage automatisé des actualités, des absences de professeurs et du menu de la cantine.

Le Back-Office (Dashboard) : Une interface sécurisée permettant aux administrateurs et au personnel de la vie scolaire de gérer le contenu de la base de données (CRUD).

🛠️ Fonctionnalités
- Gestion des Actualités : Publication de flash infos avec distinction de la source (Lycée, Région, Internat).
- Suivi des Absences : Affichage en temps réel des professeurs absents et des éventuels remplacements/changements de salle.
- Menu de la Cantine : Affichage automatique du menu en fonction de la date du jour.
- Sécurité : Accès au tableau de bord protégé par authentification (sessions PHP) et hachage des mots de passe.
- Automatisation : Un script Python (script.py) est prévu pour synchroniser les données directement depuis Pronote.

🚀 Installation
1. Base de données
Importez le fichier databases.sql dans votre gestionnaire de base de données (phpMyAdmin, MySQL).

Note : La base de données doit s'appeler lycee_lavoisier.

2. Configuration PHP
Modifiez le fichier PHP/db.php pour qu'il corresponde à vos identifiants locaux :

PHP

$host = 'localhost';
$user = 'votre_utilisateur';
$pass = 'votre_mot_de_passe';

3. Création d'un compte Admin
Pour accéder au dashboard, insérez un utilisateur dans la table users. Attention : le mot de passe doit être haché.

📖 Guide d'utilisation
Accès au Dashboard
Rendez-vous sur login.php.

Connectez-vous avec votre identifiant et mot de passe.

Une fois sur le dashboard.php, vous pouvez :

Ajouter : Remplissez le formulaire de la section souhaitée et cliquez sur "Ajouter" ou "Publier".

Supprimer : Cliquez sur la croix rouge (❌) à côté d'un élément pour le retirer de l'affichage.

Affichage sur les écrans
L'index.php est conçu pour être affiché en plein écran sur les moniteurs du lycée. Il se rafraîchit dynamiquement pour afficher les dernières données saisies dans le dashboard

Structure de l'application : 

Projet_Reseaux
- index.php (index du serveur)
- Back_end (Dossier)
    - auth.php (Fichier)
    - dashboard.php (Fichier)
    - login.php (Fichier)
    - logout.php (Fichier)
- Module (Dossier)
    - CSS (Dossier)
        - style.css (Fichier)
    - Java (Dossier)
        - script.js (Fichier)
- PHP (Dossier)
    - db.php (Fichier)
- Python (Dossier)
    - script.py (Fichier)
- SQL (Dossier)
    - databases.sql (Fichier)