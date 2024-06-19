$(document).ready(function() {
    var autocompleteTimeout;

    function fetchBookSuggestions(query) {
        return $.ajax({
            url: 'https://www.googleapis.com/books/v1/volumes',
            data: { q: query, maxResults: 5 },
            dataType: 'json'
        });
    }

    function showSuggestions(suggestions, targetInput) {
        var suggestionBox = $('<div class="autocomplete-suggestions"></div>');
        suggestions.forEach(function(item) {
            var suggestionItem = $('<div class="autocomplete-suggestion"></div>').text(item.volumeInfo.title + ' by ' + item.volumeInfo.authors);
            suggestionItem.on('click', function() {
                $('#titre').val(item.volumeInfo.title || '/');
                $('#auteur').val(item.volumeInfo.authors ? item.volumeInfo.authors.join(', ') : '/');
                $('#prix').val(item.saleInfo.listPrice ? item.saleInfo.listPrice.amount : '/');
                $('#maison_edition').val(item.volumeInfo.publisher || '/');
                $('#resume').val(item.volumeInfo.description || '/');
                $('.autocomplete-suggestions').remove(); // Suppression de la liste de suggestions
            });
            suggestionBox.append(suggestionItem);
        });
        targetInput.after(suggestionBox);
    }

    function handleInputChange() {
        var titre = $('#titre').val();
        var auteur = $('#auteur').val();
        var query = (titre !== '' ? 'intitle:' + titre : '') + (auteur !== '' ? '+inauthor:' + auteur : '');
        
        clearTimeout(autocompleteTimeout);
        if (query) {
            autocompleteTimeout = setTimeout(function() {
                fetchBookSuggestions(query).done(function(data) {
                    $('.autocomplete-suggestions').remove();
                    if (data.totalItems > 0) {
                        showSuggestions(data.items, $('#titre'));
                    }
                });
            }, 300);
        }
    }

    $('#titre, #auteur').on('input', handleInputChange);
    
    // Suppression de la liste de suggestions lorsque le champ perd le focus
    $('#titre, #auteur').on('blur', function() {
        setTimeout(function() {
            $('.autocomplete-suggestions').remove();
        }, 200); // Un léger délai pour permettre la sélection de l'option
    });
});
