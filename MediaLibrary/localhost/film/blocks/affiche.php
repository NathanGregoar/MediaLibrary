<?php

function getMoviePoster($title) {
    $apiKey = 'f1e681ff'; // Votre clé API OMDb
    $query = urlencode($title);
    $url = "http://www.omdbapi.com/?apikey=$apiKey&t=$query";

    // Effectuer la requête HTTP GET
    $response = file_get_contents($url);

    // Vérifier si la réponse est valide
    if ($response) {
        $data = json_decode($response, true);
        if ($data['Response'] == 'True') {
            return $data['Poster']; // Renvoie l'URL de l'affiche du film
        }
    }

    return null; // Si aucune affiche n'est trouvée ou si la requête échoue
}
