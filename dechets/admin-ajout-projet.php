<?php
require 'config.php'; // là où se trouve Connexion() et ajouterProjet()

if (isset($_POST['submit'])) {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);
    $published_at = $_POST['published_at'];

    // Dossier de stockage des images
    $uploadDir = 'photos/';
    $imageName = time() . '_' . $_FILES['image']['name'];
    $uploadPath = $uploadDir . $imageName;

    // Sécurité basique image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        die("Format d'image non autorisé");
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        ajouterProjet($title, $description, $imageName, $url, $published_at);
        echo "<div class='alert alert-success text-center'>Projet ajouté avec succès</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Erreur lors de l'upload</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Ajouter un projet</h2>

    <form action="" method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Titre du projet</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Lien du projet</label>
            <input type="url" name="url" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Date de publication</label>
            <input type="date" name="published_at" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Image du projet</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">
            Enregistrer le projet
        </button>

    </form>
</div>

</body>
</html>
