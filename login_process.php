<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = Connexion();
    
    $query = $db->prepare("SELECT * FROM connexion WHERE login = :login");
    $query->execute(['login' => $login]);
    $user = $query->fetch();

    if ($user && md5($password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['prenom'] ?? '';
        header('Location: index.php#contact');
        exit();
    }

    header('Location: index.php?error=auth#contact');
    exit();
}

header('Location: index.php');
exit();
