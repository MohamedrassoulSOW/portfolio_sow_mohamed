<?php
require_once 'config.php';
$s = obtenirReglages();
$file = $s['cv_file'] ?? 'photos/CV-Mohamed-SOW.pdf';

if ($file && file_exists($file)) {
    $filename = basename($file);
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

header('Location: index.php');
exit;
