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

                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        // Variable pour stocker l'état actuel du flash
                        var flashOn = false;

                        // Fonction pour activer/désactiver le flash
                        function toggleFlash() {
                            if (!cameraContainer.querySelector('video')) return; // Ne pas activer le flash si la caméra est fermée

                            flashOn = !flashOn; // Inverser l'état actuel

                            // Récupérer l'élément vidéo
                            var videoElement = document.querySelector('#result video');
                            if (videoElement) {
                                // Activer/désactiver le flash en fonction de l'état
                                videoElement.setAttribute('playsinline', ''); // Ajouter l'attribut pour la lecture en ligne sur iOS
                                videoElement.srcObject.getVideoTracks().forEach(function (track) {
                                    if (track.kind === 'video') {
                                        track.applyConstraints({
                                            advanced: [{ torch: flashOn }]
                                        });
                                    }
                                });
                            }
                        }


                        // Créer le bouton pour activer/désactiver le flash
                        flashButton = document.createElement('button');
                        flashButton.innerHTML = '<i class="bi bi-lightning-fill"></i>'; // Icone du bouton
                        flashButton.classList.add('flash-button', 'text-warning'); // Ajouter des classes Bootstrap
                        flashButton.addEventListener('click', toggleFlash); // Ajouter un gestionnaire d'événement

                        // Ajouter le bouton en haut à droite de la page
                        document.body.appendChild(flashButton);
                    }

                    // Ajouter le bouton de fermeture
                    var closeButton = document.createElement('button');
                    closeButton.innerHTML = '<i class="bi bi-x-lg"></i>'; // Ajouter le symbole "X"
                    closeButton.classList.add('close-button', 'text-danger'); // Ajouter des classes Bootstrap
                    closeButton.addEventListener('click', function () {
                        Quagga.stop();
                        cameraContainer.innerHTML = ''; // Supprimer le contenu de la caméra
                        if (flashButton) {
                            flashButton.remove(); // Supprimer le bouton flash lorsque la caméra est fermée
                        }
                    });

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
});
