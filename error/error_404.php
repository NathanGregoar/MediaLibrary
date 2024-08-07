<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Erreur Cat cent Cat - 404</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      color: #444;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      position: relative; /* Ajouter une position relative pour positionner les nouvelles images */
      overflow: hidden; /* Empêcher le défilement vertical et horizontal */
    }

    .container {
      text-align: center;
      position: relative; /* Permet de positionner le contenu du container */
      z-index: 1; /* Mettre le container au-dessus des images */
    }

    h1 {
      font-size: 48px;
      margin-bottom: 20px;
      color: #333;
    }

    p {
      font-size: 18px;
      margin-bottom: 40px;
    }

    a {
      color: #007bff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    /* Custom styles for "Erreur Cat sans Cat" */
    .cat-image {
      max-width: 250px;
      margin-bottom: 20px;
      animation: bounce 2s infinite;
    }

    /* Add a class for the new cat images */
    .cat-image-small {
      max-width: 20px;
      position: absolute;
      opacity: 0; /* Start with 0 opacity */
      z-index: 0; /* Mettre les images au-dessous du contenu */
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-20px);
      }
    }

    .text-fade-in {
      opacity: 0;
      animation: fade-in 2s forwards;
    }

    @keyframes fade-in {
      0% {
        opacity: 0;
      }

      100% {
        opacity: 1;
      }
    }

    /* Add a new element to contain the cat images outside of the container */
    .cat-images-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0; /* Mettre le conteneur d'images au-dessous du contenu */
    }
  </style>

  <script>
    function goBack() {
      window.history.back();
    }

    // Function to add 99 more cat images
    function addCatImages() {
      const catImagesContainer = document.querySelector('.cat-images-container');

      // Create and add 99 cat images
      for (let i = 0; i < 99; i++) {
        const img = document.createElement('img');
        img.className = 'cat-image-small'; // Use the new class for styling
        img.src = 'https://cdn-icons-png.flaticon.com/512/616/616430.png';
        img.style.left = `${Math.random() * (window.innerWidth - 20)}px`; // Random horizontal position within window
        img.style.top = `${Math.random() * (window.innerHeight - 20)}px`; // Random vertical position within window
        img.style.animation = `fade-in 1s forwards ${0.2 * i}s`; // Apply fade-in animation with a delay of 0.5 seconds * i
        catImagesContainer.appendChild(img);
      }
    }

    // Wait for the page to load and then add the cat images
    window.onload = function() {
      addCatImages();
    };
  </script>
</head>

<body>
  <div class="container">
    <img class="cat-image" src="https://static.vecteezy.com/system/resources/previews/011/662/655/original/cute-cat-3d-rendering-free-png.png" alt="Cat">
    <h1 class="text-fade-in">Erreur Cat cent Cat - 404</h1>
    <p class="text-fade-in">Désolé, la page que vous recherchez est introuvable.</p>
    <p class="text-fade-in">Retournez à <a href="../accueil/index.php">la page d'accueil</a></p>
  </div>
  <!-- New element to contain the cat images -->
  <div class="cat-images-container"></div>
</body>

</html>
