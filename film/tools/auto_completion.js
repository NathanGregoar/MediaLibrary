$(document).ready(function() {
    var autocompleteTimeout;
    var selectedPoster = '';

    function fetchFilmSuggestions(query) {
        var apiKey = 'f1e681ff'; // Votre clé API OMDb
        var url = 'https://www.omdbapi.com/?apikey=' + apiKey + '&s=' + query;

        return $.ajax({
            url: url,
            dataType: 'json'
        });
    }

    function showSuggestions(suggestions, targetInput) {
        var suggestionBox = $('<div class="autocomplete-suggestions"></div>');
        suggestions.forEach(function(item) {
            var suggestionItem = $('<div class="autocomplete-suggestion"></div>').text(item.Title + ' (' + item.Year + ')');
            suggestionItem.on('click', function() {
                fetchFilmDetails(item.imdbID);
                suggestionBox.remove();
            });
            suggestionBox.append(suggestionItem);
        });
        targetInput.after(suggestionBox);
    }

    function fetchFilmDetails(imdbID) {
        var apiKey = 'f1e681ff'; // Votre clé API OMDb
        var url = 'https://www.omdbapi.com/?apikey=' + apiKey + '&i=' + imdbID;

        $.ajax({
            url: url,
            dataType: 'json',
            success: function(response) {
                if (response.Response === 'True') {
                    $('#title').val(response.Title);
                    $('#director').val(response.Director);
                    $('#release_year').val(response.Year);
                    if (response.Poster && response.Poster !== 'N/A') {
                        selectedPoster = response.Poster;
                        $('#poster').attr('src', response.Poster).show();
                    } else {
                        $('#poster').hide();
                    }
                } else {
                    console.log('Aucun résultat trouvé pour la recherche du film.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la recherche du film:', error);
            }
        });
    }

    function handleInputChange() {
        var title = $('#title').val();
        var query = title.trim();

        clearTimeout(autocompleteTimeout);
        if (query) {
            autocompleteTimeout = setTimeout(function() {
                fetchFilmSuggestions(query).done(function(data) {
                    $('.autocomplete-suggestions').remove();
                    if (data.Response === 'True') {
                        showSuggestions(data.Search, $('#title'));
                    }
                });
            }, 300);
        }
    }

    $('#title').on('input', handleInputChange);

    // Suppression de la liste de suggestions lorsque le champ perd le focus
    $('#title').on('blur', function() {
        setTimeout(function() {
            $('.autocomplete-suggestions').remove();
        }, 200); // Un léger délai pour permettre la sélection de l'option
    });

    // Conserver l'affiche du film si le titre est modifié
    $('#title, #director, #release_year').on('input', function() {
        if (selectedPoster && $('#poster').attr('src') !== selectedPoster) {
            $('#poster').attr('src', selectedPoster).show();
        }
    });
});
