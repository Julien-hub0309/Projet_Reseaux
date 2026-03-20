// Données universelles simulées (Base de données du projet)
const universalNews = [
    { title: "Menu de la semaine", content: "Découvrez les plats du terroir cette semaine.", category: "Cantine" },
    { title: "Flux RSS : Vie du Lycée", content: "Les élèves de BTS CIEL en visite d'entreprise.", category: "Lycée" },
    { title: "Météo Méru", content: "Tempête prévue jeudi, soyez prudents.", category: "Alerte" }
];

document.addEventListener("DOMContentLoaded", () => {
    startClock();
    determineWeekType();
    renderUniversalNews();
    
    // Automatisation : Extinction simulée à 18h30 (Cahier des charges)
    checkOperatingHours();
});

// 1. Horloge en temps réel
function startClock() {
    function updateClock() {
        const now = new Date();

        const clock = document.getElementById('clock');
        const dateEl = document.getElementById('current-date');

        if (clock) {
            clock.textContent = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
        }

        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }

    updateClock();                 // première mise à jour
    setInterval(updateClock, 1000); // puis toutes les secondes
}



// 2. Détermination de la semaine A ou B [cite: 43, 55, 63, 71]
function determineWeekType() {
    const now = new Date();
    const startOfYear = new Date(now.getFullYear(), 0, 1);
    const pastDaysOfYear = (now - startOfYear) / 86400000;
    const weekNumber = Math.ceil((pastDaysOfYear + startOfYear.getDay() + 1) / 7);
    
    const weekDisplay = document.querySelector('.week-type');
    if (weekDisplay) {
        weekDisplay.textContent = weekNumber % 2 === 0 ? "Semaine B" : "Semaine A";
    }
}

// 3. Affichage des News Universelles
function renderUniversalNews() {
    const feed = document.getElementById('news-feed');
    if (!feed) return;

    feed.innerHTML = universalNews.map(item => `
        <article class="card">
            <small style="color:var(--accent-orange)">${item.category}</small>
            <h3>${item.title}</h3>
            <p>${item.content}</p>
        </article>
    `).join('');
}

// 4. Gestion des horaires (7h30 - 18h30) 
function checkOperatingHours() {
    const now = new Date();
    const hour = now.getHours();
    const minutes = now.getMinutes();
    const currentTime = hour + minutes / 60;

    if (currentTime < 7.5 || currentTime > 18.5) {
        document.body.innerHTML = "<div style='background:#000; color:#fff; height:100vh; display:flex; align-items:center; justify-content:center;'><h1>SYSTÈME EN VEILLE (REPRISE À 07H30)</h1></div>";
    }
}