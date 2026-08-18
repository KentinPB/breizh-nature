document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('activites-filter-form');
    const resultsContainer = document.getElementById('activites-results-container');

    if (filterForm && resultsContainer) {

        // On écoute la soumission du formulaire (clic sur Rechercher)
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêche le rechargement classique de la page
            fetchResults();
        });

        // BONUS : On déclenche aussi la recherche dès qu'un menu déroulant change !
        const inputs = filterForm.querySelectorAll('select, input[type="date"]');
        inputs.forEach(input => {
            input.addEventListener('change', fetchResults);
        });

        function fetchResults() {
            // Affichage d'un petit message de chargement
            resultsContainer.innerHTML = '<p>Chargement des activités en cours...</p>';

            // Récupération automatique de toutes les valeurs du formulaire
            const formData = new FormData(filterForm);
            formData.append('action', 'filter_activites'); // Indique à WordPress quelle fonction PHP appeler
            formData.append('security', breizh_ajax.nonce); // Jeton de sécurité obligatoire

            // Appel AJAX natif en JavaScript (Fetch API)
            fetch(breizh_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    // On remplace le contenu du conteneur par les résultats renvoyés par PHP
                    resultsContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    resultsContainer.innerHTML = '<p>Une erreur est survenue lors de la recherche.</p>';
                });
        }
    }
});