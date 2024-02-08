document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('openCamera').addEventListener('click', function () {
        navigator.mediaDevices.enumerateDevices()
            .then(function (devices) {
                var rearCameraId = null;

                devices.forEach(function (device) {
                    if (device.kind === 'videoinput' && device.label.toLowerCase().includes('back')) {
                        rearCameraId = device.deviceId;
                    }
                });

                var cameraContainer = document.querySelector('#result');

                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: cameraContainer,
                        constraints: {
                            deviceId: rearCameraId ? {
                                exact: rearCameraId
                            } : undefined,
                            facingMode: 'environment',
                            autoFocus: true
                        }
                    },
                    decoder: {
                        readers: ["code_128_reader", "ean_reader"]
                    },
                    numOfWorkers: navigator.hardwareConcurrency,
                    locate: true,
                    halfSample: true,
                }, function (err) {
                    if (err) {
                        console.error(err);
                        return;
                    }

                    // Ajouter le bouton de fermeture
                    var closeButton = document.createElement('button');
                    closeButton.innerHTML = '&times;'; // Ajouter le symbole "X"
                    closeButton.classList.add('close-button');
                    closeButton.addEventListener('click', closeCamera);

                    cameraContainer.appendChild(closeButton);

                    Quagga.start();
                });

                Quagga.onDetected(function (result) {
                    // Utilisez le code-barres pour effectuer une requête à l'API Google Books
                    $.ajax({
                        url: 'https://www.googleapis.com/books/v1/volumes',
                        data: {
                            q: 'isbn:' + result.codeResult.code,
                            maxResults: 1
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.totalItems > 0) {
                                var book = data.items[0].volumeInfo;
                                alert("Code-Barre détecté : " + result.codeResult.code + "\nTitre : " + book.title);
                                $('#titre').val(book.title ? book.title : '/');
                                $('#auteur').val(book.authors ? book.authors[0] : '/');
                                $('#maison_edition').val(book.publisher ? book.publisher : '/');

                                // Vérifiez si 'listPrice' est défini avant de l'utiliser
                                if (book.saleInfo && book.saleInfo.listPrice && book.saleInfo.listPrice.amount) {
                                    $('#prix').val(book.saleInfo.listPrice.amount);
                                } else {
                                    $('#prix').val('/');
                                }

                                $('#resume').val(book.description ? book.description : '/');
                            }
                        }
                    });

                    Quagga.stop();
                    // Retirez l'emplacement de la caméra une fois fermée
                    cameraContainer.innerHTML = '';
                });
            })
            .catch(function (err) {
                console.error(err);
            });
    });

    function closeCamera() {
        Quagga.stop();
        document.querySelector('#result').innerHTML = ''; // Supprimer le contenu de la caméra
    }
});
