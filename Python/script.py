import pronotepy
from pronotepy.ent import ac_amiens # Académie d'Amiens pour Méru [cite: 9]
import mysql.connector
from datetime import date

# 1. Connexion à Pronote
client = pronotepy.Client('https://0600029v.index-education.net/pronote/eleve.html',
                          username='UTILISATEUR',
                          password='MOT_DE_PASSE',
                          ent=ac_amiens)

# 2. Connexion à ta base de données MySQL
db = mysql.connector.connect(
    host = 'localhost';
    dbname = 'lycee_lavoisier';
    user = 'webmaster';
    pass = 'Admin123';
)
cursor = db.cursor()

if client.logged_in:
    # --- RÉCUPÉRATION DES REMPLACEMENTS ET ABSENCES ---
    # Récupère l'emploi du temps du jour [cite: 46, 47]
    lessons = client.lessons(date.today())
    for lesson in lessons:
        if lesson.status == "Absent":
            # On insère l'absence
            sql = "INSERT INTO teacher_status (teacher_name, subject, status, start_time) VALUES (%s, %s, 'Absent', %s)"
            cursor.execute(sql, (lesson.teacher_name, lesson.subject.name, lesson.start))
        
        elif lesson.status == "Remplacé": # Remplacement courte durée [cite: 47]
            sql = "INSERT INTO teacher_status (teacher_name, subject, status, replacement_teacher, room) VALUES (%s, %s, 'Remplacé', %s, %s)"
            cursor.execute(sql, (lesson.teacher_name, lesson.subject.name, "Nom du remplaçant", lesson.classroom))

    # --- RÉCUPÉRATION DU MENU DE LA CANTINE ---
    menus = client.menus(date.today()) # [cite: 45, 57]
    for menu in menus:
        menu_text = ", ".join([str(meal.label) for meal in menu.meals])
        sql = "INSERT IGNORE INTO daily_menu (day_date, meals_json) VALUES (%s, %s)"
        cursor.execute(sql, (date.today(), menu_text))

    # --- RÉCUPÉRATION DES INFORMATIONS (FLUX RSS / INFOS) ---
    infos = client.information_and_surveys() # [cite: 40, 54, 69]
    for info in infos:
        sql = "INSERT INTO live_info (title, content, source) VALUES (%s, %s, 'Lycée')"
        cursor.execute(sql, (info.title, info.content))

    db.commit()
    print("Base de données mise à jour avec les informations de Pronote.")

cursor.close()
db.close()