<?php
session_start();
require_once 'config.php';

// Sécurité : Seul l'admin connecté peut supprimer
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if (deleteMessage($id)) {
        // Redirige vers le dashboard avec un signal de succès
        header("Location: dashboard.php?success=delete");
        exit();
    }
}

header("Location: dashboard.php?error=1");
exit();