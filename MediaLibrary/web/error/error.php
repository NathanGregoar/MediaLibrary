<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Erreur Cat 100 Cat - 404</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      color: #444;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      overflow: hidden; /* Empêcher le défilement vertical et horizontal */
      position: relative; /* Permet de positionner les images absolument à l'intérieur de body */
    }

    /* Custom styles for "Erreur Cat sans Cat" */
    .cat-image {
      max-width: 250px;
      margin-bottom: 20px;
      animation: bounce 2s infinite;
      position: absolute;
    }

    @keyframes bounce {
      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-20px);
      }
    }

    /* Adjust z-index to avoid image overlap */
    .cat-image {
      z-index: -1;
    }
  </style>

  <script>
    // Function to add 99 more cat images
    function addCatImages() {
      const body = document.body;

      // Create and add 99 cat images
      for (let i = 0; i < 99; i++) {
        const img = document.createElement('img');
        img.className = 'cat-image';
        img.src = 'https://cdn-icons-png.flaticon.com/512/616/616430.png';
        img.style.left = `${Math.random() * (window.innerWidth - 250)}px`; // Random horizontal position within window
        img.style.top = `${Math.random() * (window.innerHeight - 250)}px`; // Random vertical position within window
        body.appendChild(img);

        // Apply bounce animation to each image with a delay of 1 second
        img.style.animation = `bounce 2s infinite ${i + 1}s`;
      }
    }

    // Wait for the page to load and then add the cat images
    window.onload = function() {
      addCatImages();
    };
  </script>
</head>

<body>
  <h1>Erreur Cat 100 Cat - 404</h1>
  <p>Désolé, la page que vous recherchez est introuvable.</p>
  <p>Retournez à <a href="javascript:history.back()">la page précédente</a></p>
</body>

</html>
