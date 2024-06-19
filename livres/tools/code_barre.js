document.addEventListener('DOMContentLoaded', function () {
    const openCameraButton = document.getElementById('openCamera');
    const cameraContainer = document.querySelector('#result');
    let codeReader;
    let videoElement;
    let stream;
    let flashButton;
    let closeButton;
    let isFlashOn = false; // Add this line to keep track of the flash state

    function initCamera() {
        codeReader = new ZXing.BrowserBarcodeReader();
        videoElement = document.createElement('video');
        cameraContainer.appendChild(videoElement);

        // Create flash button
        flashButton = document.createElement('button');
        flashButton.innerHTML = '<i class="bi bi-lightning-fill"></i>';
        flashButton.classList.add('flash-button', 'btn', 'btn-warning');
        flashButton.addEventListener('click', toggleFlash);
        document.body.appendChild(flashButton);

        // Create close button
        closeButton = document.createElement('button');
        closeButton.innerHTML = '<i class="bi bi-x-lg"></i>';
        closeButton.classList.add('close-button', 'btn', 'btn-danger');
        closeButton.addEventListener('click', closeCamera);
        document.body.appendChild(closeButton);

        navigator.mediaDevices.enumerateDevices()
            .then(function (devices) {
                let rearCameraId = null;
                devices.forEach(function (device) {
                    if (device.kind === 'videoinput' && device.label.toLowerCase().includes('back')) {
                        rearCameraId = device.deviceId;
                    }
                });

                navigator.mediaDevices.getUserMedia({
                    video: { 
                        deviceId: rearCameraId ? { exact: rearCameraId } : undefined
                    }
                }).then(function (mediaStream) {
                    stream = mediaStream;
                    videoElement.srcObject = stream;
                    videoElement.play();

                    const track = stream.getVideoTracks()[0];
                    const capabilities = track.getCapabilities();

                    if (capabilities.zoom) {
                        track.applyConstraints({
                            advanced: [{ zoom: 2 }]
                        }).then(() => {
                            console.log('Zoom applied successfully');
                        }).catch(err => console.error('Error applying zoom:', err));
                    } else {
                        console.warn('Zoom is not supported by this device.');
                    }

                    codeReader.decodeOnceFromVideoDevice(rearCameraId, videoElement)
                        .then(result => {
                            console.log(result.text);
                            // Use the barcode to make a request to the Google Books API
                            $.ajax({
                                url: 'https://www.googleapis.com/books/v1/volumes',
                                data: {
                                    q: 'isbn:' + result.text,
                                    maxResults: 1
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if (data.totalItems > 0) {
                                        var book = data.items[0].volumeInfo;
                                        alert("Code-Barre détecté : " + result.text + "\nTitre : " + book.title);
                                        $('#titre').val(book.title ? book.title : '/');
                                        $('#auteur').val(book.authors ? book.authors[0] : '/');
                                        $('#maison_edition').val(book.publisher ? book.publisher : '/');
                                        $('#prix').val(book.saleInfo && book.saleInfo.listPrice && book.saleInfo.listPrice.amount ? book.saleInfo.listPrice.amount : '/');
                                        $('#resume').val(book.description ? book.description : '/');
                                    }
                                    // Close the camera once the book is detected
                                    closeCamera();
                                }
                            });

                            codeReader.reset();
                        })
                        .catch(err => {
                            console.error(err);
                            closeCamera(); // Ensure camera closes on error
                        });
                }).catch(err => console.error(err));
            })
            .catch(err => console.error(err));
    }

    function toggleFlash() {
        if (stream) {
            const track = videoElement.srcObject.getVideoTracks()[0];
            const capabilities = track.getCapabilities();
            if (capabilities.torch) {
                isFlashOn = !isFlashOn;
                track.applyConstraints({
                    advanced: [{ torch: isFlashOn }]
                }).then(() => {
                    flashButton.innerHTML = isFlashOn ? '<i class="bi bi-lightning-fill"></i>' : '<i class="bi bi-lightning-fill"></i>';
                }).catch(err => console.error(err));
            } else {
                alert('Torch feature is not supported on this device.');
            }
        }
    }

    function closeCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null; // Ensure stream is set to null after stopping
        }
        cameraContainer.innerHTML = '';
        if (flashButton) flashButton.style.display = 'none';
        if (closeButton) closeButton.style.display = 'none';
        codeReader.reset(); // Reset the code reader to ensure it stops all operations
    }

    openCameraButton.addEventListener('click', function () {
        initCamera();
    });
});
