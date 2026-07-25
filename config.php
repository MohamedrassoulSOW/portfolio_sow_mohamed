<?php

require_once __DIR__ . '/config.db.php';

function Connexion(){
    $db = getDbConfig();
    try {
        $cnx = new PDO(
            'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=utf8mb4',
            $db['user'],
            $db['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
    return $cnx;
}

function cheminImageProjet($image) {
    if (empty($image)) {
        return '';
    }
    if (strpos($image, 'uploads/') === 0 || strpos($image, 'photos/') === 0) {
        return $image;
    }
    return 'photos/' . $image;
}

function cheminFichierImage($image) {
    if (empty($image)) {
        return null;
    }
    if (strpos($image, 'uploads/') === 0 || strpos($image, 'photos/') === 0) {
        return $image;
    }
    return 'photos/' . $image;
}

function messageUtilisateur($prenom, $nom, $email, $message) {
	$cnx = Connexion();
	$req = $cnx->prepare("INSERT INTO `messages` (`prenom`, `nom`, `email`, `message`) VALUES(?,?,?,?)");
	$req->execute(array($prenom, $nom, $email, $message));
}

function recupererMessages() {
    $cnx = Connexion();
    $req = $cnx->prepare("SELECT id, prenom, nom, email, message, date FROM messages ORDER BY date DESC");
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function compterProjets() {
    $cnx = Connexion();
    return (int) $cnx->query("SELECT COUNT(*) FROM projects")->fetchColumn();
}

function compterMessages() {
    $cnx = Connexion();
    return (int) $cnx->query("SELECT COUNT(*) FROM messages")->fetchColumn();
}

function recupererProjetParId($id) {
    $cnx = Connexion();
    $req = $cnx->prepare("SELECT * FROM projects WHERE id = ?");
    $req->execute([intval($id)]);
    return $req->fetch();
}

function ajouterProjet($title, $description, $image, $url, $published_at) {
    $cnx = Connexion();
    $sql = "INSERT INTO projects (title, description, image, url, published_at) VALUES (?, ?, ?, ?, ?)";
    $req = $cnx->prepare($sql);
    
    return $req->execute([
        $title, 
        $description, 
        $image, 
        $url, 
        $published_at
    ]);
}

function recupererProjets() {
    $cnx = Connexion();
    $req = $cnx->prepare(
        "SELECT * FROM projects ORDER BY published_at DESC"
    );
    $req->execute();
    return $req->fetchAll();
}

function deleteMessage($id) {
    $cnx = Connexion(); 
    try {
        $req = $cnx->prepare("DELETE FROM messages WHERE id = ?");
        return $req->execute([intval($id)]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function supprimerProjet($id) {
    $cnx = Connexion();
    $stmt = $cnx->prepare("SELECT image FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if ($project && !empty($project['image'])) {
        $path = cheminFichierImage($project['image']);
        if ($path && file_exists($path)) {
            unlink($path);
        }
    }

    $query = $cnx->prepare("DELETE FROM projects WHERE id = ?");
    return $query->execute([$id]);
}

function modifierProjet($id, $title, $description, $url, $image = null) {
    $cnx = Connexion();
    if ($image) {
        $query = $cnx->prepare("UPDATE projects SET title = ?, description = ?, url = ?, image = ? WHERE id = ?");
        return $query->execute([$title, $description, $url, $image, $id]);
    } else {
        $query = $cnx->prepare("UPDATE projects SET title = ?, description = ?, url = ? WHERE id = ?");
        return $query->execute([$title, $description, $url, $id]);
    }
}

function uploaderImageProjet($fileInput, $ancienneImage = null) {
    if (!isset($fileInput) || $fileInput['error'] !== UPLOAD_ERR_OK) {
        return $ancienneImage;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $fileInput['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) {
        return $ancienneImage;
    }

    if ($fileInput['size'] > 5 * 1024 * 1024) {
        return $ancienneImage;
    }

    $uploadDir = 'uploads/projets/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($fileInput['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('projet_', true) . '.' . strtolower($ext);
    $destPath = $uploadDir . $fileName;

    if (move_uploaded_file($fileInput['tmp_name'], $destPath)) {
        if ($ancienneImage) {
            $oldPath = cheminFichierImage($ancienneImage);
            if ($oldPath && file_exists($oldPath) && $oldPath !== $destPath && strpos($oldPath, 'uploads/') === 0) {
                unlink($oldPath);
            }
        }
        return $destPath;
    }

    return $ancienneImage;
}

function uploaderFichierGenerique($fileInput, $uploadDir, $allowedMimes, $prefix, $ancienne = null, $maxSize = 8388608) {
    if (!isset($fileInput) || $fileInput['error'] !== UPLOAD_ERR_OK) {
        return $ancienne;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $fileInput['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMimes, true) || $fileInput['size'] > $maxSize) {
        return $ancienne;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
    $destPath = rtrim($uploadDir, '/\\') . '/' . uniqid($prefix, true) . '.' . $ext;

    if (move_uploaded_file($fileInput['tmp_name'], $destPath)) {
        if ($ancienne && file_exists($ancienne) && strpos($ancienne, 'uploads/') === 0) {
            unlink($ancienne);
        }
        return $destPath;
    }

    return $ancienne;
}

require_once __DIR__ . '/content_init.php';

function obtenirReglages(): array {
    initialiserContenuSite();
    $cnx = Connexion();
    $rows = $cnx->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function obtenirReglage(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = obtenirReglages();
    }
    return $cache[$key] ?? $default;
}

function enregistrerReglages(array $data): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare(
        "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    foreach ($data as $key => $value) {
        $stmt->execute([$key, $value]);
    }
    return true;
}

function recupererCompetences(): array {
    initialiserContenuSite();
    $cnx = Connexion();
    return $cnx->query("SELECT * FROM site_skills ORDER BY sort_order ASC, id ASC")->fetchAll();
}

function ajouterCompetence(string $name, int $percent, int $sort = 0): bool {
    $cnx = Connexion();
    if ($sort <= 0) {
        $sort = (int) $cnx->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM site_skills")->fetchColumn();
    }
    $stmt = $cnx->prepare("INSERT INTO site_skills (name, percent, sort_order) VALUES (?, ?, ?)");
    return $stmt->execute([$name, max(0, min(100, $percent)), $sort]);
}

function modifierCompetence(int $id, string $name, int $percent, int $sort): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("UPDATE site_skills SET name = ?, percent = ?, sort_order = ? WHERE id = ?");
    return $stmt->execute([$name, max(0, min(100, $percent)), $sort, $id]);
}

function supprimerCompetence(int $id): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("DELETE FROM site_skills WHERE id = ?");
    return $stmt->execute([$id]);
}

function recupererServices(): array {
    initialiserContenuSite();
    $cnx = Connexion();
    return $cnx->query("SELECT * FROM site_services ORDER BY sort_order ASC, id ASC")->fetchAll();
}

function ajouterService(string $title, string $description, string $icon, int $sort = 0): bool {
    $cnx = Connexion();
    if ($sort <= 0) {
        $sort = (int) $cnx->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM site_services")->fetchColumn();
    }
    $stmt = $cnx->prepare("INSERT INTO site_services (title, description, icon, sort_order) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$title, $description, $icon ?: 'fa fa-globe', $sort]);
}

function modifierService(int $id, string $title, string $description, string $icon, int $sort): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("UPDATE site_services SET title = ?, description = ?, icon = ?, sort_order = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $icon ?: 'fa fa-globe', $sort, $id]);
}

function supprimerService(int $id): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("DELETE FROM site_services WHERE id = ?");
    return $stmt->execute([$id]);
}

function recupererParcours(?string $type = null): array {
    initialiserContenuSite();
    $cnx = Connexion();
    if ($type) {
        $stmt = $cnx->prepare("SELECT * FROM site_timeline WHERE type = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    return $cnx->query("SELECT * FROM site_timeline ORDER BY type ASC, sort_order ASC, id ASC")->fetchAll();
}

function ajouterParcours(string $type, string $dateLabel, string $title, string $description, int $sort = 0): bool {
    if (!in_array($type, ['education', 'experience'], true)) {
        return false;
    }
    $cnx = Connexion();
    if ($sort <= 0) {
        $stmtMax = $cnx->prepare("SELECT COALESCE(MAX(sort_order),0)+1 FROM site_timeline WHERE type = ?");
        $stmtMax->execute([$type]);
        $sort = (int) $stmtMax->fetchColumn();
    }
    $stmt = $cnx->prepare("INSERT INTO site_timeline (type, date_label, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$type, $dateLabel, $title, $description, $sort]);
}

function modifierParcours(int $id, string $type, string $dateLabel, string $title, string $description, int $sort): bool {
    if (!in_array($type, ['education', 'experience'], true)) {
        return false;
    }
    $cnx = Connexion();
    $stmt = $cnx->prepare("UPDATE site_timeline SET type = ?, date_label = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
    return $stmt->execute([$type, $dateLabel, $title, $description, $sort, $id]);
}

function supprimerParcours(int $id): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("DELETE FROM site_timeline WHERE id = ?");
    return $stmt->execute([$id]);
}

function recupererDiplomes(): array {
    initialiserContenuSite();
    $cnx = Connexion();
    return $cnx->query("SELECT * FROM site_diplomes ORDER BY sort_order ASC, id ASC")->fetchAll();
}

function supprimerImageUpload(?string $path): void {
    if ($path && file_exists($path) && strpos($path, 'uploads/') === 0) {
        unlink($path);
    }
}

function ajouterDiplome(string $title, string $issuer, string $dateLabel, string $description, string $url, string $icon, int $sort = 0, ?string $image = null): bool {
    $cnx = Connexion();
    if ($sort <= 0) {
        $sort = (int) $cnx->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM site_diplomes")->fetchColumn();
    }
    $stmt = $cnx->prepare("INSERT INTO site_diplomes (title, issuer, date_label, description, url, image, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $issuer, $dateLabel, $description, $url, $image, $icon ?: 'fa fa-graduation-cap', $sort]);
}

function modifierDiplome(int $id, string $title, string $issuer, string $dateLabel, string $description, string $url, string $icon, int $sort, ?string $image = null): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("UPDATE site_diplomes SET title = ?, issuer = ?, date_label = ?, description = ?, url = ?, image = ?, icon = ?, sort_order = ? WHERE id = ?");
    return $stmt->execute([$title, $issuer, $dateLabel, $description, $url, $image, $icon ?: 'fa fa-graduation-cap', $sort, $id]);
}

function supprimerDiplome(int $id): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("SELECT image FROM site_diplomes WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        supprimerImageUpload($row['image'] ?? null);
    }
    $stmt = $cnx->prepare("DELETE FROM site_diplomes WHERE id = ?");
    return $stmt->execute([$id]);
}

function recupererCertifications(): array {
    initialiserContenuSite();
    $cnx = Connexion();
    return $cnx->query("SELECT * FROM site_certifications ORDER BY sort_order ASC, id ASC")->fetchAll();
}

function ajouterCertification(string $title, string $issuer, string $dateLabel, string $description, string $url, string $icon, int $sort = 0, ?string $image = null): bool {
    $cnx = Connexion();
    if ($sort <= 0) {
        $sort = (int) $cnx->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM site_certifications")->fetchColumn();
    }
    $stmt = $cnx->prepare("INSERT INTO site_certifications (title, issuer, date_label, description, url, image, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $issuer, $dateLabel, $description, $url, $image, $icon ?: 'fa fa-certificate', $sort]);
}

function modifierCertification(int $id, string $title, string $issuer, string $dateLabel, string $description, string $url, string $icon, int $sort, ?string $image = null): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("UPDATE site_certifications SET title = ?, issuer = ?, date_label = ?, description = ?, url = ?, image = ?, icon = ?, sort_order = ? WHERE id = ?");
    return $stmt->execute([$title, $issuer, $dateLabel, $description, $url, $image, $icon ?: 'fa fa-certificate', $sort, $id]);
}

function supprimerCertification(int $id): bool {
    $cnx = Connexion();
    $stmt = $cnx->prepare("SELECT image FROM site_certifications WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        supprimerImageUpload($row['image'] ?? null);
    }
    $stmt = $cnx->prepare("DELETE FROM site_certifications WHERE id = ?");
    return $stmt->execute([$id]);
}

function calculerAge(?string $birthday = null): int {
    $birthday = $birthday ?: obtenirReglage('birthday', '1998-09-27');
    try {
        return (new DateTime($birthday))->diff(new DateTime())->y;
    } catch (Exception $e) {
        return 0;
    }
}

