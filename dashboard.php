<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=auth#contact');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifierCsrf(): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '');
}

function redirigerDashboard(string $status, string $tab = 'overview'): void {
    header('Location: dashboard.php?tab=' . urlencode($tab) . '&' . $status . '=1');
    exit();
}

$allowedTabs = ['overview', 'profile', 'skills', 'services', 'certifications', 'timeline', 'projects', 'messages'];
$tab = $_GET['tab'] ?? 'overview';
if (!in_array($tab, $allowedTabs, true)) {
    $tab = 'overview';
}

// --- POST handlers ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifierCsrf()) {
        redirigerDashboard('error', $tab);
    }

    // Profil / contenu site
    if (isset($_POST['save_profile'])) {
        $current = obtenirReglages();
        $profileImage = $current['profile_image'] ?? 'photos/mahou.jpg';
        $mobileImage = $current['mobile_image'] ?? 'photos/moi.jpeg';
        $favicon = $current['favicon'] ?? 'photos/moi.jpeg';
        $cvFile = $current['cv_file'] ?? 'photos/CV-Mohamed-SOW.pdf';

        $imageMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $faviconMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon'];

        $profileImage = uploaderFichierGenerique(
            $_FILES['profile_image'] ?? null,
            'uploads/profile/',
            $imageMimes,
            'photo_',
            $profileImage
        );
        $mobileImage = uploaderFichierGenerique(
            $_FILES['mobile_image'] ?? null,
            'uploads/profile/',
            $imageMimes,
            'mobile_',
            $mobileImage
        );
        $favicon = uploaderFichierGenerique(
            $_FILES['favicon'] ?? null,
            'uploads/profile/',
            $faviconMimes,
            'favicon_',
            $favicon
        );
        $cvFile = uploaderFichierGenerique(
            $_FILES['cv_file'] ?? null,
            'uploads/cv/',
            ['application/pdf'],
            'cv_',
            $cvFile,
            10 * 1024 * 1024
        );

        $data = [
            'site_title' => trim($_POST['site_title'] ?? ''),
            'logo_letter' => trim($_POST['logo_letter'] ?? 'M'),
            'logo_text' => trim($_POST['logo_text'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'birthday' => trim($_POST['birthday'] ?? ''),
            'birthday_display' => trim($_POST['birthday_display'] ?? ''),
            'website_url' => trim($_POST['website_url'] ?? ''),
            'website_label' => trim($_POST['website_label'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'degree' => trim($_POST['degree'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'phone_secondary' => trim($_POST['phone_secondary'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'address_secondary' => trim($_POST['address_secondary'] ?? ''),
            'freelance_status' => trim($_POST['freelance_status'] ?? ''),
            'home_intro' => trim($_POST['home_intro'] ?? ''),
            'typing_strings' => trim($_POST['typing_strings'] ?? ''),
            'about_title' => trim($_POST['about_title'] ?? ''),
            'about_text' => trim($_POST['about_text'] ?? ''),
            'contact_title' => trim($_POST['contact_title'] ?? ''),
            'contact_subtitle' => trim($_POST['contact_subtitle'] ?? ''),
            'contact_form_title' => trim($_POST['contact_form_title'] ?? ''),
            'contact_form_subtitle' => trim($_POST['contact_form_subtitle'] ?? ''),
            'portfolio_heading' => trim($_POST['portfolio_heading'] ?? ''),
            'cv_button_label' => trim($_POST['cv_button_label'] ?? ''),
            'profile_image' => $profileImage,
            'mobile_image' => $mobileImage,
            'favicon' => $favicon,
            'cv_file' => $cvFile,
        ];
        enregistrerReglages($data);
        redirigerDashboard('success_saved', 'profile');
    }

    // Skills
    if (isset($_POST['ajouter_skill'])) {
        ajouterCompetence(trim($_POST['name'] ?? ''), (int)($_POST['percent'] ?? 0), (int)($_POST['sort_order'] ?? 0));
        redirigerDashboard('success_added', 'skills');
    }
    if (isset($_POST['modifier_skill'])) {
        modifierCompetence((int)$_POST['id'], trim($_POST['name'] ?? ''), (int)($_POST['percent'] ?? 0), (int)($_POST['sort_order'] ?? 0));
        redirigerDashboard('success_updated', 'skills');
    }
    if (isset($_POST['delete_skill'])) {
        supprimerCompetence((int)$_POST['delete_skill']);
        redirigerDashboard('success_deleted', 'skills');
    }

    // Services
    if (isset($_POST['ajouter_service'])) {
        ajouterService(trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), trim($_POST['icon'] ?? ''), (int)($_POST['sort_order'] ?? 0));
        redirigerDashboard('success_added', 'services');
    }
    if (isset($_POST['modifier_service'])) {
        modifierService((int)$_POST['id'], trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), trim($_POST['icon'] ?? ''), (int)($_POST['sort_order'] ?? 0));
        redirigerDashboard('success_updated', 'services');
    }
    if (isset($_POST['delete_service'])) {
        supprimerService((int)$_POST['delete_service']);
        redirigerDashboard('success_deleted', 'services');
    }

    // Certifications
    if (isset($_POST['ajouter_certification'])) {
        ajouterCertification(
            trim($_POST['title'] ?? ''),
            trim($_POST['issuer'] ?? ''),
            trim($_POST['date_label'] ?? ''),
            trim($_POST['description'] ?? ''),
            trim($_POST['url'] ?? ''),
            trim($_POST['icon'] ?? 'fa fa-certificate'),
            (int)($_POST['sort_order'] ?? 0)
        );
        redirigerDashboard('success_added', 'certifications');
    }
    if (isset($_POST['modifier_certification'])) {
        modifierCertification(
            (int)$_POST['id'],
            trim($_POST['title'] ?? ''),
            trim($_POST['issuer'] ?? ''),
            trim($_POST['date_label'] ?? ''),
            trim($_POST['description'] ?? ''),
            trim($_POST['url'] ?? ''),
            trim($_POST['icon'] ?? 'fa fa-certificate'),
            (int)($_POST['sort_order'] ?? 0)
        );
        redirigerDashboard('success_updated', 'certifications');
    }
    if (isset($_POST['delete_certification'])) {
        supprimerCertification((int)$_POST['delete_certification']);
        redirigerDashboard('success_deleted', 'certifications');
    }

    // Timeline
    if (isset($_POST['ajouter_timeline'])) {
        ajouterParcours($_POST['type'] ?? 'education', trim($_POST['date_label'] ?? ''), trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), (int)($_POST['sort_order'] ?? 0));
        redirigerDashboard('success_added', 'timeline');
    }
    if (isset($_POST['modifier_timeline'])) {
        modifierParcours((int)$_POST['id'], $_POST['type'] ?? 'education', trim($_POST['date_label'] ?? ''), trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), (int)($_POST['sort_order'] ?? 0));
        redirigerDashboard('success_updated', 'timeline');
    }
    if (isset($_POST['delete_timeline'])) {
        supprimerParcours((int)$_POST['delete_timeline']);
        redirigerDashboard('success_deleted', 'timeline');
    }

    // Projects
    if (isset($_POST['ajouter_projet'])) {
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));
        $url = trim($_POST['url'] ?? '');
        $image = uploaderImageProjet($_FILES['image'] ?? null);
        if ($title !== '' && ajouterProjet($title, $description, $image, $url, date('Y-m-d H:i:s'))) {
            redirigerDashboard('success_added', 'projects');
        }
        redirigerDashboard('error', 'projects');
    }
    if (isset($_POST['modifier_projet'])) {
        $id = (int)($_POST['id'] ?? 0);
        $title = htmlspecialchars(trim($_POST['title'] ?? ''));
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));
        $url = trim($_POST['url'] ?? '');
        $image = uploaderImageProjet($_FILES['image'] ?? null, $_POST['ancienne_image'] ?? null);
        if ($id > 0 && $title !== '' && modifierProjet($id, $title, $description, $url, $image)) {
            redirigerDashboard('success_updated', 'projects');
        }
        redirigerDashboard('error', 'projects');
    }
    if (isset($_POST['delete_project'])) {
        supprimerProjet((int)$_POST['delete_project']);
        redirigerDashboard('success_deleted', 'projects');
    }
    if (isset($_POST['delete_msg'])) {
        deleteMessage((int)$_POST['delete_msg']);
        redirigerDashboard('success_msg_deleted', 'messages');
    }
}

$s = obtenirReglages();
$messages = recupererMessages();
$projects = recupererProjets();
$skills = recupererCompetences();
$services = recupererServices();
$certifications = recupererCertifications();
$timeline = recupererParcours();
$countProjects = count($projects);
$countMessages = count($messages);
$userName = $_SESSION['user_name'] ?? 'Admin';
$csrf = htmlspecialchars($_SESSION['csrf_token']);

$flashMessages = [
    'success_saved' => ['success', 'Informations du site enregistrées.'],
    'success_added' => ['success', 'Élément ajouté avec succès.'],
    'success_updated' => ['success', 'Élément mis à jour.'],
    'success_deleted' => ['success', 'Élément supprimé.'],
    'success_msg_deleted' => ['success', 'Message supprimé.'],
    'error' => ['danger', 'Une erreur est survenue. Vérifiez les champs et réessayez.'],
];
$activeFlash = null;
foreach ($flashMessages as $key => $meta) {
    if (isset($_GET[$key])) {
        $activeFlash = $meta;
        break;
    }
}

$tabTitles = [
    'overview' => 'Tableau de bord',
    'profile' => 'Infos du site',
    'skills' => 'Compétences',
    'services' => 'Services',
    'certifications' => 'Certifications',
    'timeline' => 'Parcours',
    'projects' => 'Projets',
    'messages' => 'Messages',
];

function he(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?= he($s['full_name'] ?? 'Admin') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --sidebar-w: 260px; --brand: #ec1839; --ink: #1e1e2d; --surface: #f4f6f9; }
        body { background: var(--surface); color: var(--ink); }
        .sidebar { width: var(--sidebar-w); min-height: 100vh; background: #1a1d23; color: #fff; position: fixed; inset: 0 auto 0 0; z-index: 1040; display: flex; flex-direction: column; transition: transform .25s ease; }
        .sidebar-brand { padding: 1.5rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-brand .logo span { color: var(--brand); }
        .sidebar .nav-link { color: rgba(255,255,255,.65); border-radius: .6rem; padding: .65rem 1rem; margin: .1rem .75rem; display: flex; align-items: center; gap: .65rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-link .badge { margin-left: auto; }
        .sidebar-footer { margin-top: auto; padding: 1rem; border-top: 1px solid rgba(255,255,255,.08); }
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e9ecef; padding: .85rem 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; position: sticky; top: 0; z-index: 1020; }
        .content { padding: 1.5rem; }
        .stat-card, .panel { background: #fff; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .stat-card { height: 100%; }
        .stat-card .icon-box { width: 48px; height: 48px; border-radius: .85rem; display: grid; place-items: center; font-size: 1.25rem; }
        .icon-box.red { background: rgba(236,24,57,.12); color: var(--brand); }
        .icon-box.blue { background: rgba(13,110,253,.12); color: #0d6efd; }
        .icon-box.green { background: rgba(25,135,84,.12); color: #198754; }
        .icon-box.orange { background: rgba(253,126,20,.12); color: #fd7e14; }
        .panel { overflow: hidden; }
        .panel-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f0f0f0; display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; }
        .project-thumb { width: 56px; height: 56px; object-fit: cover; border-radius: .5rem; background: #eee; }
        .search-input { max-width: 280px; }
        .table > :not(caption) > * > * { vertical-align: middle; }
        .msg-preview { max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .empty-state { text-align: center; padding: 3rem 1.5rem; color: #6c757d; }
        .empty-state i { font-size: 2.5rem; opacity: .4; display: block; margin-bottom: .75rem; }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1035; }
        .sidebar-backdrop.show { display: block; }
        .img-preview-box { max-height: 140px; object-fit: cover; border-radius: .5rem; display: none; }
        .img-preview-box.show { display: block; }
        .form-section-title { font-size: .85rem; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; margin: 1.25rem 0 .75rem; font-weight: 700; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
        }
    </style>
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo fs-5 fw-bold"><span><?= he($s['logo_letter'] ?? 'M') ?></span><?= he($s['logo_text'] ?? "'Sow") ?> Admin</div>
        <div class="small text-white-50 mt-1">Bonjour, <?= he($userName) ?></div>
    </div>
    <nav class="nav flex-column py-3">
        <a class="nav-link <?= $tab === 'overview' ? 'active' : '' ?>" href="dashboard.php?tab=overview"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a>
        <a class="nav-link <?= $tab === 'profile' ? 'active' : '' ?>" href="dashboard.php?tab=profile"><i class="bi bi-person-gear"></i> Infos du site</a>
        <a class="nav-link <?= $tab === 'skills' ? 'active' : '' ?>" href="dashboard.php?tab=skills"><i class="bi bi-bar-chart"></i> Compétences</a>
        <a class="nav-link <?= $tab === 'services' ? 'active' : '' ?>" href="dashboard.php?tab=services"><i class="bi bi-grid"></i> Services</a>
        <a class="nav-link <?= $tab === 'certifications' ? 'active' : '' ?>" href="dashboard.php?tab=certifications"><i class="bi bi-award"></i> Certifications</a>
        <a class="nav-link <?= $tab === 'timeline' ? 'active' : '' ?>" href="dashboard.php?tab=timeline"><i class="bi bi-clock-history"></i> Parcours</a>
        <a class="nav-link <?= $tab === 'projects' ? 'active' : '' ?>" href="dashboard.php?tab=projects"><i class="bi bi-briefcase"></i> Projets <span class="badge text-bg-secondary"><?= $countProjects ?></span></a>
        <a class="nav-link <?= $tab === 'messages' ? 'active' : '' ?>" href="dashboard.php?tab=messages"><i class="bi bi-chat-left-text"></i> Messages <span class="badge text-bg-danger"><?= $countMessages ?></span></a>
        <a class="nav-link" id="voirSiteLink" href="index.php"><i class="bi bi-box-arrow-up-right"></i> Voir le site</a>
    </nav>
    <div class="sidebar-footer">
        <a class="btn btn-outline-light w-100" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Déconnexion</a>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="main-wrap">
    <header class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary d-lg-none" type="button" id="sidebarToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
            <div>
                <h1 class="h5 mb-0"><?= he($tabTitles[$tab]) ?></h1>
                <div class="small text-muted d-none d-sm-block">Gérez tout le contenu du portfolio</div>
            </div>
        </div>
        <?php if ($tab === 'projects' || $tab === 'overview'): ?>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAjoutProjet"><i class="bi bi-plus-lg me-1"></i> Nouveau projet</button>
        <?php elseif ($tab === 'skills'): ?>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAjoutSkill"><i class="bi bi-plus-lg me-1"></i> Compétence</button>
        <?php elseif ($tab === 'services'): ?>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAjoutService"><i class="bi bi-plus-lg me-1"></i> Service</button>
        <?php elseif ($tab === 'certifications'): ?>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAjoutCert"><i class="bi bi-plus-lg me-1"></i> Certification</button>
        <?php elseif ($tab === 'timeline'): ?>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAjoutTimeline"><i class="bi bi-plus-lg me-1"></i> Entrée</button>
        <?php endif; ?>
    </header>

    <main class="content">
        <?php if ($activeFlash): ?>
            <div class="alert alert-<?= $activeFlash[0] ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= he($activeFlash[1]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'overview'): ?>
            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="stat-card p-3 position-relative"><div class="d-flex align-items-center gap-3"><div class="icon-box red"><i class="bi bi-person-gear"></i></div><div><div class="text-muted small">Contenu</div><div class="fw-semibold">Infos site</div></div></div><a href="dashboard.php?tab=profile" class="stretched-link"></a></div></div>
                <div class="col-md-3"><div class="stat-card p-3 position-relative"><div class="d-flex align-items-center gap-3"><div class="icon-box orange"><i class="bi bi-briefcase"></i></div><div><div class="text-muted small">Projets</div><div class="fs-3 fw-bold lh-1"><?= $countProjects ?></div></div></div><a href="dashboard.php?tab=projects" class="stretched-link"></a></div></div>
                <div class="col-md-3"><div class="stat-card p-3 position-relative"><div class="d-flex align-items-center gap-3"><div class="icon-box blue"><i class="bi bi-envelope"></i></div><div><div class="text-muted small">Messages</div><div class="fs-3 fw-bold lh-1"><?= $countMessages ?></div></div></div><a href="dashboard.php?tab=messages" class="stretched-link"></a></div></div>
                <div class="col-md-3"><div class="stat-card p-3 position-relative"><div class="d-flex align-items-center gap-3"><div class="icon-box green"><i class="bi bi-grid"></i></div><div><div class="text-muted small">Services</div><div class="fs-3 fw-bold lh-1"><?= count($services) ?></div></div></div><a href="dashboard.php?tab=services" class="stretched-link"></a></div></div>
            </div>
            <div class="alert alert-light border">
                <strong>Astuce :</strong> utilisez le menu <em>Infos du site</em> pour modifier le texte d’accueil, la bio, les contacts, la photo et le CV. Les compétences, services et parcours ont chacun leur onglet.
            </div>
        <?php endif; ?>

        <?php if ($tab === 'profile'): ?>
            <form method="POST" enctype="multipart/form-data" class="panel p-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <div class="form-section-title">Identité</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nom complet</label><input type="text" name="full_name" class="form-control" value="<?= he($s['full_name'] ?? '') ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Titre de la page</label><input type="text" name="site_title" class="form-control" value="<?= he($s['site_title'] ?? '') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Lettre logo</label><input type="text" name="logo_letter" class="form-control" maxlength="2" value="<?= he($s['logo_letter'] ?? 'M') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Texte logo</label><input type="text" name="logo_text" class="form-control" value="<?= he($s['logo_text'] ?? '') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Date naissance (YYYY-MM-DD)</label><input type="date" name="birthday" class="form-control" value="<?= he($s['birthday'] ?? '') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Affichage date</label><input type="text" name="birthday_display" class="form-control" value="<?= he($s['birthday_display'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Diplôme</label><input type="text" name="degree" class="form-control" value="<?= he($s['degree'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Statut freelance</label><input type="text" name="freelance_status" class="form-control" value="<?= he($s['freelance_status'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= he($s['email'] ?? '') ?>"></div>
                </div>

                <div class="form-section-title">Contact & localisation</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Téléphone principal</label><input type="text" name="phone" class="form-control" value="<?= he($s['phone'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Téléphone secondaire</label><input type="text" name="phone_secondary" class="form-control" value="<?= he($s['phone_secondary'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Adresse principale</label><input type="text" name="address" class="form-control" value="<?= he($s['address'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Adresse secondaire</label><input type="text" name="address_secondary" class="form-control" value="<?= he($s['address_secondary'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">URL site web</label><input type="url" name="website_url" class="form-control" value="<?= he($s['website_url'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Libellé site web</label><input type="text" name="website_label" class="form-control" value="<?= he($s['website_label'] ?? '') ?>"></div>
                </div>

                <div class="form-section-title">Accueil</div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Texte d'introduction</label><textarea name="home_intro" class="form-control" rows="3"><?= he($s['home_intro'] ?? '') ?></textarea></div>
                    <div class="col-12"><label class="form-label">Métiers animés (un par ligne)</label><textarea name="typing_strings" class="form-control" rows="3"><?= he($s['typing_strings'] ?? '') ?></textarea></div>
                    <div class="col-md-6"><label class="form-label">Libellé bouton CV</label><input type="text" name="cv_button_label" class="form-control" value="<?= he($s['cv_button_label'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Titre section portfolio</label><input type="text" name="portfolio_heading" class="form-control" value="<?= he($s['portfolio_heading'] ?? '') ?>"></div>
                </div>

                <div class="form-section-title">À propos</div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Titre</label><input type="text" name="about_title" class="form-control" value="<?= he($s['about_title'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Texte bio</label><textarea name="about_text" class="form-control" rows="8"><?= he($s['about_text'] ?? '') ?></textarea></div>
                </div>

                <div class="form-section-title">Textes contact</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Titre contact</label><input type="text" name="contact_title" class="form-control" value="<?= he($s['contact_title'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Sous-titre contact</label><input type="text" name="contact_subtitle" class="form-control" value="<?= he($s['contact_subtitle'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Titre formulaire</label><input type="text" name="contact_form_title" class="form-control" value="<?= he($s['contact_form_title'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Sous-titre formulaire</label><input type="text" name="contact_form_subtitle" class="form-control" value="<?= he($s['contact_form_subtitle'] ?? '') ?>"></div>
                </div>

                <div class="form-section-title">Médias</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Photo desktop (accueil)</label>
                        <?php if (!empty($s['profile_image']) && file_exists($s['profile_image'])): ?>
                            <div class="mb-2"><img src="<?= he($s['profile_image']) ?>" alt="" style="height:90px;border-radius:8px;object-fit:cover;"></div>
                        <?php endif; ?>
                        <input type="file" name="profile_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">Visible sur ordinateur uniquement.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo mobile / tablette</label>
                        <?php
                        $mobileImg = $s['mobile_image'] ?? 'photos/moi.jpeg';
                        if (!empty($mobileImg) && file_exists($mobileImg)):
                        ?>
                            <div class="mb-2"><img src="<?= he($mobileImg) ?>" alt="" style="height:90px;border-radius:8px;object-fit:cover;"></div>
                        <?php endif; ?>
                        <input type="file" name="mobile_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">Affichée sur l'accueil en mobile et tablette.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon (onglet navigateur)</label>
                        <?php
                        $faviconImg = $s['favicon'] ?? 'photos/moi.jpeg';
                        if (!empty($faviconImg) && file_exists($faviconImg)):
                        ?>
                            <div class="mb-2"><img src="<?= he($faviconImg) ?>" alt="" style="height:48px;width:48px;border-radius:8px;object-fit:cover;border:1px solid #eee;"></div>
                        <?php endif; ?>
                        <input type="file" name="favicon" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif,image/x-icon,.ico">
                        <div class="form-text">Icône affichée dans l'onglet du navigateur.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fichier CV (PDF)</label>
                        <?php if (!empty($s['cv_file'])): ?>
                            <div class="small text-muted mb-2">Actuel : <?= he(basename($s['cv_file'])) ?></div>
                        <?php endif; ?>
                        <input type="file" name="cv_file" class="form-control" accept="application/pdf">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" name="save_profile" class="btn btn-danger px-4">Enregistrer les modifications</button>
                    <a href="index.php" id="previewSiteLink" class="btn btn-outline-secondary">Prévisualiser le site</a>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($tab === 'skills'): ?>
            <div class="panel">
                <div class="panel-header"><h2 class="h6 mb-0">Compétences (<?= count($skills) ?>)</h2></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Nom</th><th>Niveau</th><th>Ordre</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php if (!$skills): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-bar-chart"></i>Aucune compétence.</div></td></tr><?php endif; ?>
                        <?php foreach ($skills as $sk): ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?= he($sk['name']) ?></td>
                                <td>
                                    <div class="progress" style="height:8px;max-width:140px;">
                                        <div class="progress-bar bg-danger" style="width:<?= (int)$sk['percent'] ?>%"></div>
                                    </div>
                                    <small><?= (int)$sk['percent'] ?>%</small>
                                </td>
                                <td><?= (int)$sk['sort_order'] ?></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSkill<?= (int)$sk['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette compétence ?')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <button type="submit" name="delete_skill" value="<?= (int)$sk['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php foreach ($skills as $sk): ?>
            <div class="modal fade" id="editSkill<?= (int)$sk['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
                <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="id" value="<?= (int)$sk['id'] ?>">
                    <div class="modal-header"><h5 class="modal-title">Modifier compétence</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="name" class="form-control" value="<?= he($sk['name']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Pourcentage</label><input type="number" name="percent" class="form-control" min="0" max="100" value="<?= (int)$sk['percent'] ?>" required></div>
                        <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$sk['sort_order'] ?>"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="modifier_skill" class="btn btn-primary">Enregistrer</button></div>
                </form>
            </div></div></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($tab === 'services'): ?>
            <div class="panel">
                <div class="panel-header"><h2 class="h6 mb-0">Services (<?= count($services) ?>)</h2></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Service</th><th class="d-none d-md-table-cell">Description</th><th>Icône</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php if (!$services): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-grid"></i>Aucun service.</div></td></tr><?php endif; ?>
                        <?php foreach ($services as $sv): ?>
                            <tr>
                                <td class="ps-3"><div class="fw-semibold"><?= he($sv['title']) ?></div><small class="text-muted">Ordre <?= (int)$sv['sort_order'] ?></small></td>
                                <td class="d-none d-md-table-cell small text-muted" style="max-width:300px"><?= he(mb_strimwidth($sv['description'], 0, 100, '...')) ?></td>
                                <td><code><?= he($sv['icon']) ?></code></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editService<?= (int)$sv['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce service ?')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <button type="submit" name="delete_service" value="<?= (int)$sv['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php foreach ($services as $sv): ?>
            <div class="modal fade" id="editService<?= (int)$sv['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
                <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="id" value="<?= (int)$sv['id'] ?>">
                    <div class="modal-header"><h5 class="modal-title">Modifier service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" value="<?= he($sv['title']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required><?= he($sv['description']) ?></textarea></div>
                        <div class="mb-3"><label class="form-label">Icône Font Awesome</label><input type="text" name="icon" class="form-control" value="<?= he($sv['icon']) ?>" placeholder="fa fa-globe"><div class="form-text">Ex: fa fa-globe, fa fa-database, fa fa-chart-line</div></div>
                        <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$sv['sort_order'] ?>"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="modifier_service" class="btn btn-primary">Enregistrer</button></div>
                </form>
            </div></div></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($tab === 'certifications'): ?>
            <div class="panel">
                <div class="panel-header"><h2 class="h6 mb-0">Certifications (<?= count($certifications) ?>)</h2></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Certification</th>
                                <th>Organisme</th>
                                <th class="d-none d-md-table-cell">Date</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$certifications): ?>
                            <tr><td colspan="4"><div class="empty-state"><i class="bi bi-award"></i>Aucune certification.</div></td></tr>
                        <?php endif; ?>
                        <?php foreach ($certifications as $cert): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold"><?= he($cert['title']) ?></div>
                                    <div class="small text-muted"><?= he(mb_strimwidth($cert['description'] ?? '', 0, 80, '...')) ?></div>
                                </td>
                                <td><span class="badge text-bg-danger"><?= he($cert['issuer']) ?></span></td>
                                <td class="d-none d-md-table-cell small text-muted"><?= he($cert['date_label']) ?></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCert<?= (int)$cert['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette certification ?')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <button type="submit" name="delete_certification" value="<?= (int)$cert['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php foreach ($certifications as $cert): ?>
            <div class="modal fade" id="editCert<?= (int)$cert['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="id" value="<?= (int)$cert['id'] ?>">
                            <div class="modal-header"><h5 class="modal-title">Modifier certification</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" value="<?= he($cert['title']) ?>" required></div>
                                <div class="mb-3"><label class="form-label">Organisme</label><input type="text" name="issuer" class="form-control" value="<?= he($cert['issuer']) ?>" required placeholder="Udemy, Coursera..."></div>
                                <div class="mb-3"><label class="form-label">Date / période</label><input type="text" name="date_label" class="form-control" value="<?= he($cert['date_label']) ?>" placeholder="2024"></div>
                                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= he($cert['description']) ?></textarea></div>
                                <div class="mb-3"><label class="form-label">Lien du certificat (URL)</label><input type="url" name="url" class="form-control" value="<?= he($cert['url']) ?>" placeholder="https://..."></div>
                                <div class="mb-3"><label class="form-label">Icône</label><input type="text" name="icon" class="form-control" value="<?= he($cert['icon']) ?>" placeholder="fa fa-certificate"></div>
                                <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$cert['sort_order'] ?>"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" name="modifier_certification" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($tab === 'timeline'): ?>
            <div class="panel">
                <div class="panel-header"><h2 class="h6 mb-0">Éducation & Expérience (<?= count($timeline) ?>)</h2></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Type</th><th>Période</th><th>Titre</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php if (!$timeline): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-clock-history"></i>Aucune entrée.</div></td></tr><?php endif; ?>
                        <?php foreach ($timeline as $tl): ?>
                            <tr>
                                <td class="ps-3"><span class="badge text-bg-<?= $tl['type'] === 'education' ? 'primary' : 'success' ?>"><?= $tl['type'] === 'education' ? 'Éducation' : 'Expérience' ?></span></td>
                                <td class="small"><?= he($tl['date_label']) ?></td>
                                <td><div class="fw-semibold"><?= he($tl['title']) ?></div><div class="small text-muted"><?= he(mb_strimwidth($tl['description'], 0, 80, '...')) ?></div></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTl<?= (int)$tl['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette entrée ?')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <button type="submit" name="delete_timeline" value="<?= (int)$tl['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php foreach ($timeline as $tl): ?>
            <div class="modal fade" id="editTl<?= (int)$tl['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content border-0 shadow">
                <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="id" value="<?= (int)$tl['id'] ?>">
                    <div class="modal-header"><h5 class="modal-title">Modifier entrée</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option value="education" <?= $tl['type']==='education'?'selected':'' ?>>Éducation</option><option value="experience" <?= $tl['type']==='experience'?'selected':'' ?>>Expérience</option></select></div>
                        <div class="mb-3"><label class="form-label">Période</label><input type="text" name="date_label" class="form-control" value="<?= he($tl['date_label']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" value="<?= he($tl['title']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5" required><?= he($tl['description']) ?></textarea></div>
                        <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$tl['sort_order'] ?>"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="modifier_timeline" class="btn btn-primary">Enregistrer</button></div>
                </form>
            </div></div></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($tab === 'projects'): ?>
            <div class="panel">
                <div class="panel-header">
                    <h2 class="h6 mb-0">Projets (<?= $countProjects ?>)</h2>
                    <input type="search" id="searchProjects" class="form-control form-control-sm search-input" placeholder="Rechercher...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="projectsTable">
                        <thead class="table-light"><tr><th class="ps-3">Projet</th><th class="d-none d-md-table-cell">Description</th><th class="d-none d-lg-table-cell">Date</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php if (!$projects): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-folder2-open"></i>Aucun projet.</div></td></tr><?php endif; ?>
                        <?php foreach ($projects as $pj): $imgSrc = cheminImageProjet($pj['image']); ?>
                            <tr data-search="<?= he(strtolower($pj['title'].' '.$pj['description'])) ?>">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if ($imgSrc): ?><img src="<?= he($imgSrc) ?>" class="project-thumb" alt=""><?php else: ?><div class="project-thumb d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div><?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?= he($pj['title']) ?></div>
                                            <?php if (!empty($pj['url'])): ?><a href="<?= he($pj['url']) ?>" target="_blank" class="small">Visiter</a><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell small text-muted"><?= he(mb_strimwidth($pj['description'], 0, 90, '...')) ?></td>
                                <td class="d-none d-lg-table-cell small text-muted"><?= date('d/m/Y', strtotime($pj['published_at'])) ?></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProject<?= (int)$pj['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProject<?= (int)$pj['id'] ?>"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php foreach ($projects as $pj): $imgSrc = cheminImageProjet($pj['image']); ?>
            <div class="modal fade" id="editProject<?= (int)$pj['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="id" value="<?= (int)$pj['id'] ?>">
                    <input type="hidden" name="ancienne_image" value="<?= he($pj['image'] ?? '') ?>">
                    <div class="modal-header"><h5 class="modal-title">Modifier projet</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" value="<?= he($pj['title']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4" required><?= he($pj['description']) ?></textarea></div>
                        <div class="mb-3"><label class="form-label">URL</label><input type="url" name="url" class="form-control" value="<?= he($pj['url']) ?>"></div>
                        <div class="mb-3"><label class="form-label">Image</label><?php if ($imgSrc): ?><div class="mb-2"><img src="<?= he($imgSrc) ?>" width="80" class="rounded"></div><?php endif; ?><input type="file" name="image" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="modifier_projet" class="btn btn-primary">Enregistrer</button></div>
                </form>
            </div></div></div>
            <div class="modal fade" id="deleteProject<?= (int)$pj['id'] ?>" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content border-0 shadow">
                <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><input type="hidden" name="delete_project" value="<?= (int)$pj['id'] ?>">
                    <div class="modal-body text-center p-4"><p>Supprimer <strong><?= he($pj['title']) ?></strong> ?</p></div>
                    <div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button class="btn btn-danger">Supprimer</button></div>
                </form>
            </div></div></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($tab === 'messages'): ?>
            <div class="panel">
                <div class="panel-header">
                    <h2 class="h6 mb-0">Messages (<?= $countMessages ?>)</h2>
                    <input type="search" id="searchMessages" class="form-control form-control-sm search-input" placeholder="Rechercher...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="messagesTable">
                        <thead class="table-light"><tr><th class="ps-3">Date</th><th>Expéditeur</th><th class="d-none d-md-table-cell">Aperçu</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php if (!$messages): ?><tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i>Aucun message.</div></td></tr><?php endif; ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr data-search="<?= he(strtolower($msg['prenom'].' '.$msg['nom'].' '.$msg['email'].' '.$msg['message'])) ?>">
                                <td class="ps-3 small text-muted text-nowrap"><?= date('d/m/Y H:i', strtotime($msg['date'])) ?></td>
                                <td><div class="fw-semibold"><?= he($msg['prenom'].' '.$msg['nom']) ?></div><div class="small text-muted"><?= he($msg['email']) ?></div></td>
                                <td class="d-none d-md-table-cell small text-muted"><div class="msg-preview"><?= he($msg['message']) ?></div></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalMsg<?= (int)$msg['id'] ?>"><i class="bi bi-eye"></i></button>
                                    <a class="btn btn-sm btn-outline-primary" href="mailto:<?= he($msg['email']) ?>"><i class="bi bi-reply"></i></a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce message ?')">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <button type="submit" name="delete_msg" value="<?= (int)$msg['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php foreach ($messages as $msg): ?>
            <div class="modal fade" id="modalMsg<?= (int)$msg['id'] ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
                <div class="modal-header"><div><h5 class="modal-title mb-0"><?= he($msg['prenom'].' '.$msg['nom']) ?></h5><div class="small text-muted"><?= he($msg['email']) ?></div></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><p style="white-space:pre-wrap;"><?= he($msg['message']) ?></p></div>
                <div class="modal-footer"><a href="mailto:<?= he($msg['email']) ?>" class="btn btn-primary">Répondre</a></div>
            </div></div></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>

<!-- Modals ajout -->
<div class="modal fade" id="modalAjoutProjet" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
    <form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="modal-header bg-danger text-white"><h5 class="modal-title">Nouveau projet</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
            <div class="mb-3"><label class="form-label">URL</label><input type="url" name="url" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="ajouter_projet" class="btn btn-danger">Publier</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="modalAjoutSkill" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="modal-header"><h5 class="modal-title">Nouvelle compétence</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Pourcentage</label><input type="number" name="percent" class="form-control" min="0" max="100" value="80" required></div>
            <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="ajouter_skill" class="btn btn-danger">Ajouter</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="modalAjoutService" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="modal-header"><h5 class="modal-title">Nouveau service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
            <div class="mb-3"><label class="form-label">Icône</label><input type="text" name="icon" class="form-control" value="fa fa-globe" placeholder="fa fa-globe"></div>
            <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="ajouter_service" class="btn btn-danger">Ajouter</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="modalAjoutCert" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="modal-header"><h5 class="modal-title">Nouvelle certification</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Organisme</label><input type="text" name="issuer" class="form-control" required placeholder="Udemy, Coursera, ForceN..."></div>
            <div class="mb-3"><label class="form-label">Date / période</label><input type="text" name="date_label" class="form-control" placeholder="2024"></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <div class="mb-3"><label class="form-label">Lien du certificat (URL)</label><input type="url" name="url" class="form-control" placeholder="https://..."></div>
            <div class="mb-3"><label class="form-label">Icône</label><input type="text" name="icon" class="form-control" value="fa fa-certificate"></div>
            <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="ajouter_certification" class="btn btn-danger">Ajouter</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="modalAjoutTimeline" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content border-0 shadow">
    <form method="POST"><input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="modal-header"><h5 class="modal-title">Nouvelle entrée parcours</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option value="education">Éducation</option><option value="experience">Expérience</option></select></div>
            <div class="mb-3"><label class="form-label">Période</label><input type="text" name="date_label" class="form-control" placeholder="Ex: Janvier 2024 à nos jours" required></div>
            <div class="mb-3"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4" required></textarea></div>
            <div class="mb-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" name="ajouter_timeline" class="btn btn-danger">Ajouter</button></div>
    </form>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');
    if (toggle) toggle.addEventListener('click', () => { sidebar.classList.toggle('open'); backdrop.classList.toggle('show'); });
    if (backdrop) backdrop.addEventListener('click', () => { sidebar.classList.remove('open'); backdrop.classList.remove('show'); });
    function bindSearch(inputId, tableId) {
        const input = document.getElementById(inputId), table = document.getElementById(tableId);
        if (!input || !table) return;
        input.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            table.querySelectorAll('tbody tr[data-search]').forEach(row => {
                row.style.display = row.getAttribute('data-search').includes(q) ? '' : 'none';
            });
        });
    }
    bindSearch('searchProjects', 'projectsTable');
    bindSearch('searchMessages', 'messagesTable');

    // Rouvre le site sur la dernière section visitée (même onglet navigateur)
    const tabToSection = {
        overview: null,
        profile: 'about',
        skills: 'about',
        services: 'services',
        certifications: 'certifications',
        timeline: 'about',
        projects: 'portfolio',
        messages: 'contact'
    };
    const params = new URLSearchParams(window.location.search);
    const currentTab = params.get('tab') || 'overview';
    const remembered = localStorage.getItem('portfolioActiveSection');
    const mapped = tabToSection[currentTab];
    const section = remembered || mapped || 'home';
    const siteUrl = 'index.php#' + section;
    const voirSite = document.getElementById('voirSiteLink');
    const previewSite = document.getElementById('previewSiteLink');
    if (voirSite) voirSite.href = siteUrl;
    if (previewSite) previewSite.href = siteUrl;
})();
</script>
</body>
</html>
