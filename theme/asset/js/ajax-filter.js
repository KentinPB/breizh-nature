document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('activites-filter-form');
    const resultsContainer = document.getElementById('activites-results-container');

    if (filterForm && resultsContainer) {

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchResults();
        });

        // MISE À JOUR : On écoute les changements sur TOUS les champs (select et inputs)
        const inputs = filterForm.querySelectorAll('select, input');
        inputs.forEach(input => {
            input.addEventListener('change', fetchResults);
        });

        function fetchResults() {
            resultsContainer.innerHTML = '<p>Chargement des activités en cours...</p>';

            const formData = new FormData(filterForm);
            formData.append('action', 'filter_activites');
            formData.append('security', breizh_ajax.nonce);

            fetch(breizh_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    resultsContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    resultsContainer.innerHTML = '<p>Une erreur est survenue lors de la recherche.</p>';
                });
        }
    }
});