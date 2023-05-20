<!DOCTYPE html>
<html>
<head>
    <title>My Movie Collection</title>
</head>
<body>
    <h1>My Movie Collection</h1>

    <?php
    $host = 'db';
    $user = 'root';
    $password = 'root';
    $database = 'movie_collection';

    $connection = new mysqli($host, $user, $password, $database);

    if ($connection->connect_error) {
        die('Connection failed: ' . $connection->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $director = $_POST['director'];
        $releaseYear = $_POST['release_year'];

        $sql = "INSERT INTO films (title, director, release_year) VALUES ('$title', '$director', '$releaseYear')";

        if ($connection->query($sql) === TRUE) {
            echo '<p>Movie added successfully!</p>';
        } else {
            echo '<p>Error adding movie: ' . $connection->error . '</p>';
        }
    }
    ?>

    <h2>Add a Movie</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="title">Title:</label>
        <input type="text" name="title" required><br>

        <label for="director">Director:</label>
        <input type="text" name="director" required><br>

        <label for="release_year">Release Year:</label>
        <input type="number" name="release_year" required><br>

        <input type="submit" value="Add Movie">
    </form>

    <h2>My Movies</h2>

    <?php
    $sql = "SELECT * FROM films";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        echo '<ul>';

        while ($row = $result->fetch_assoc()) {
            echo '<li>' . $row['title'] . ' (' . $row['director'] . ', ' . $row['release_year'] . ')</li>';
        }

        echo '</ul>';
    } else {
        echo '<p>No movies found.</p>';
    }

    $connection->close();
    ?>
</body>
</html>
