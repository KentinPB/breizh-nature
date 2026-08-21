document.addEventListener("DOMContentLoaded", function() {
    const cookieBanner = document.getElementById("bnr-cookie-banner");
    const btnAccept = document.getElementById("btn-accept-cookies");
    const btnRefuse = document.getElementById("btn-refuse-cookies");

    // Fonction pour lire un cookie
    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }

    // Si le cookie de consentement n'existe pas, on affiche la bannière
    if (!getCookie("bnr_cookie_consent")) {
        cookieBanner.style.display = "block";
    }

    // Gérer le clic sur les boutons
    function handleConsent(value) {
        // Création du cookie valable 13 mois (395 jours)
        const date = new Date();
        date.setTime(date.getTime() + (395 * 24 * 60 * 60 * 1000));
        document.cookie = "bnr_cookie_consent=" + value + "; expires=" + date.toUTCString() + "; path=/; SameSite=Lax";

        // Masquer la bannière
        cookieBanner.style.display = "none";
    }

    if (btnAccept) btnAccept.addEventListener("click", () => handleConsent("accepted"));
    if (btnRefuse) btnRefuse.addEventListener("click", () => handleConsent("refused"));
});