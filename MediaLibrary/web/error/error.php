<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Erreur Cat sans Cat - 404</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      color: #444;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .container {
      text-align: center;
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
      max-width: 400px;
      margin-bottom: 20px;
      animation: bounce 2s infinite;
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
  </style>

  <script>
    function goBack() {
      window.history.back();
    }
  </script>
</head>

<body>
  <div class="container">
    <img class="cat-image" src="https://static.vecteezy.com/system/resources/previews/011/662/655/original/cute-cat-3d-rendering-free-png.png" alt="Cat">
    <h1 class="text-fade-in">Erreur Cat sans Cat - 404</h1>
    <p class="text-fade-in">Désolé, la page que vous recherchez est introuvable.</p>
    <p class="text-fade-in">Retournez à <a href="javascript:history.back()">la page précédente</a></p>
  </div>
</body>

</html>
