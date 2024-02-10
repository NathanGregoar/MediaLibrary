function searchFilmInfo(title) {
    var apiKey = 'f1e681ff'; // Votre clé API OMDb
    var query = encodeURIComponent(title);
    var url = 'http://www.omdbapi.com/?apikey=' + apiKey + '&t=' + query;

    $.ajax({
        url: url,
        dataType: 'json',
        success: function(response) {
            if (response.Response === 'True') {
                var director = response.Director; // Le réalisateur
                var releaseYear = response.Year; // L'année de sortie

                // Utilisez les données récupérées pour auto-compléter les champs du formulaire
                $('#director').val(director);
                $('#release_year').val(releaseYear);
            } else {
                console.log('Aucun résultat trouvé pour la recherche du film.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la recherche du film:', error);
        }
    });
}

$(document).ready(function() {
    $('#title').on('blur', function() {
        var title = $(this).val();
        if (title.trim() !== '') {
            searchFilmInfo(title);
        }
    });
});