<?php
// Connexion à la base de données (remplacez les valeurs par les vôtres)
$host = 'db';
$user = 'nathan';
$password = '444719';
$database = 'movie_collection';

$connection = new mysqli($host, $user, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Traitement du formulaire d'ajout de table
if (isset($_POST['ajouter_table'])) {
    $table_name = $_POST['nom_table'];

    // Votre code pour ajouter la table à la base de données
    $sql = "CREATE TABLE $table_name (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(30) NOT NULL,
                email VARCHAR(50) NOT NULL
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Table $table_name ajoutée avec succès.";
    } else {
        echo "Erreur lors de la création de la table : " . $conn->error;
    }
}

// Traitement du formulaire de suppression de table
if (isset($_POST['supprimer_table'])) {
    $table_name = $_POST['nom_table'];

    // Votre code pour supprimer la table de la base de données
    $sql = "DROP TABLE $table_name";

    if ($conn->query($sql) === TRUE) {
        echo "Table $table_name supprimée avec succès.";
    } else {
        echo "Erreur lors de la suppression de la table : " . $conn->error;
    }
}

// Traitement du formulaire d'ajout d'élément
if (isset($_POST['ajouter_element'])) {
    $table_name = $_POST['nom_table'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    // Votre code pour ajouter l'élément à la table
    $sql = "INSERT INTO $table_name (nom, email) VALUES ('$nom', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Élément ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout de l'élément : " . $conn->error;
    }
}

// Traitement du formulaire de modification d'élément
if (isset($_POST['modifier_element'])) {
    $table_name = $_POST['nom_table'];
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    // Votre code pour modifier l'élément dans la table
    $sql = "UPDATE $table_name SET nom='$nom', email='$email' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Élément modifié avec succès.";
    } else {
        echo "Erreur lors de la modification de l'élément : " . $conn->error;
    }
}

// Traitement du formulaire de suppression d'élément
if (isset($_POST['supprimer_element'])) {
    $table_name = $_POST['nom_table'];
    $id = $_POST['id'];

    // Votre code pour supprimer l'élément de la table
    $sql = "DELETE FROM $table_name WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Élément supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression de l'élément : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion de la base de données</title>
</head>
<body>
    <div class="navbar">
        <a href="../index.php">Accueil</a>
    </div>

    <h2>Ajouter une table</h2>
    <form method="post">
        <input type="text" name="nom_table" placeholder="Nom de la table" required>
        <input type="submit" name="ajouter_table" value="Ajouter">
    </form>

    <h2>Supprimer une table</h2>
    <form method="post">
        <input type="text" name="nom_table" placeholder="Nom de la table" required>
        <input type="submit" name="supprimer_table" value="Supprimer">
    </form>

    <h2>Ajouter un élément</h2>
    <form method="post">
        <input type="text" name="nom_table" placeholder="Nom de la table" required>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="submit" name="ajouter_element" value="Ajouter">
    </form>

    <h2>Modifier un élément</h2>
    <form method="post">
        <input type="text" name="nom_table" placeholder="Nom de la table" required>
        <input type="number" name="id" placeholder="ID de l'élément" required>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="submit" name="modifier_element" value="Modifier">
    </form>

    <h2>Supprimer un élément</h2>
    <form method="post">
        <input type="text" name="nom_table" placeholder="Nom de la table" required>
        <input type="number" name="id" placeholder="ID de l'élément" required>
        <input type="submit" name="supprimer_element" value="Supprimer">
    </form>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
