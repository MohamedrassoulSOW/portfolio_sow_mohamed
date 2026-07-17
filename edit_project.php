<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=auth#contact');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: dashboard.php');
    exit();
}

$db = Connexion();
$stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $url = htmlspecialchars(trim($_POST['url'] ?? ''));
    $final_image_path = uploaderImageProjet($_FILES['image'] ?? null, $p['image']);

    if (modifierProjet($id, $title, $description, $url, $final_image_path)) {
        header('Location: dashboard.php?success=updated');
        exit();
    }
}

$imgSrc = cheminImageProjet($p['image']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier projet</title>
</head>
<body>
<form method="POST" enctype="multipart/form-data" style="max-width: 500px; margin: auto; padding: 50px; font-family: sans-serif;">
    <h3>Modifier : <?= htmlspecialchars($p['title']) ?></h3>
    
    <label>Titre</label>
    <input type="text" name="title" value="<?= htmlspecialchars($p['title']) ?>" style="width:100%; margin-bottom:15px; padding: 8px;" required>
    
    <label>Description</label>
    <textarea name="description" style="width:100%; height:100px; margin-bottom:15px; padding: 8px;" required><?= htmlspecialchars($p['description']) ?></textarea>
    
    <label>Lien du projet</label>
    <input type="text" name="url" value="<?= htmlspecialchars($p['url']) ?>" style="width:100%; margin-bottom:15px; padding: 8px;">
    
    <label>Image du projet</label>
    <div style="margin-bottom: 10px;">
        <small>Actuelle :</small><br>
        <?php if ($imgSrc): ?>
            <img src="<?= htmlspecialchars($imgSrc) ?>" width="100" alt="" style="border-radius: 5px; margin-top: 5px;">
        <?php endif; ?>
    </div>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" style="width:100%; margin-bottom:20px;">
    
    <button type="submit" style="background: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px;">
        Enregistrer les modifications
    </button>
    <a href="dashboard.php" style="margin-left: 10px; color: #666; text-decoration: none;">Annuler</a>
</form>
</body>
</html>
