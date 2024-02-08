$(document).ready(function() {
    $('#titre, #auteur, #prix, #maison_edition, #resume').blur(function() {
        var titre = $('#titre').val();
        var auteur = $('#auteur').val();
        var prix = $('#prix').val();
        var maison_edition = $('#maison_edition').val();
        var resume = $('#resume').val();

        if (titre !== '' || auteur !== '' || prix !== '' || maison_edition !== '' || resume !== '') {
            $.ajax({
                url: 'https://www.googleapis.com/books/v1/volumes',
                data: {
                    q: (titre !== '' ? 'intitle:' + titre : '') + (auteur !== '' ? '+inauthor:' + auteur : ''),
                    maxResults: 1
                },
                dataType: 'json',
                success: function(data) {
                    if (data.totalItems > 0) {
                        var book = data.items[0];
                        $('#titre').val(book.volumeInfo.title ? book.volumeInfo.title : '/');
                        $('#auteur').val(book.volumeInfo.authors ? book.volumeInfo.authors[0] : '/');
                        $('#prix').val(book.saleInfo.listPrice ? book.saleInfo.listPrice.amount : '/');
                        $('#maison_edition').val(book.volumeInfo.publisher ? book.volumeInfo.publisher : '/');
                        $('#resume').val(book.volumeInfo.description ? book.volumeInfo.description : '/');
                    }
                }
            });
        }
    });
});
