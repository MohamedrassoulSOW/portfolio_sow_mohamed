<?php
include 'config.php';
session_start();

$s = obtenirReglages();
$projects = recupererProjets();
$skills = recupererCompetences();
$services = recupererServices();
$certifications = recupererCertifications();
$education = recupererParcours('education');
$experience = recupererParcours('experience');
$age = calculerAge($s['birthday'] ?? null);
$typingStrings = array_values(array_filter(array_map('trim', explode("\n", $s['typing_strings'] ?? 'Développeur web'))));
$profileImage = $s['profile_image'] ?? 'photos/mahou.jpg';
$mobileImage = (!empty($s['mobile_image']) && file_exists($s['mobile_image'])) ? $s['mobile_image'] : 'photos/moi.jpeg';
$favicon = (!empty($s['favicon']) && file_exists($s['favicon'])) ? $s['favicon'] : 'photos/moi.jpeg';
$faviconType = 'image/' . (pathinfo($favicon, PATHINFO_EXTENSION) === 'ico' ? 'x-icon' : strtolower(pathinfo($favicon, PATHINFO_EXTENSION) ?: 'jpeg'));
if ($faviconType === 'image/jpg') {
    $faviconType = 'image/jpeg';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if ($prenom && $nom && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        messageUtilisateur($prenom, $nom, $email, $message);

        $to = $s['email'] ?? 'sow.mohamedra@gmail.com';
        $subject = "Nouveau contact portfolio — $prenom $nom";
        $body = "Nouveau message depuis le portfolio :\n\n";
        $body .= "Nom : $prenom $nom\n";
        $body .= "Email : $email\n";
        $body .= "Message : \n$message\n";
        $headers = "From: noreply@sowcoder.com\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();
        @mail($to, $subject, $body, $headers);

        $_SESSION['message_sent'] = true;
        header('Location: index.php?success=sent#contact');
        exit();
    }
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($s['site_title'] ?? 'Portfolio') ?></title>
    <link rel="icon" href="<?= e($favicon) ?>" type="<?= e($faviconType) ?>">
    <link rel="apple-touch-icon" href="<?= e($favicon) ?>">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/color-1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/color-1.css" class="alternate-style" title="color-1" disabled>
    <link rel="stylesheet" href="css/color-2.css" class="alternate-style" title="color-2" disabled>
    <link rel="stylesheet" href="css/color-3.css" class="alternate-style" title="color-3" disabled>
    <link rel="stylesheet" href="css/color-4.css" class="alternate-style" title="color-4" disabled>
    <link rel="stylesheet" href="css/color-5.css" class="alternate-style" title="color-5" disabled>
    <link rel="stylesheet" href="style-switcher.css">
</head>
<body>
    <div class="main-container">
        <div class="aside">
            <div class="logo">
                <a href="#home"><span><?= e($s['logo_letter'] ?? 'M') ?></span><?= e($s['logo_text'] ?? "'Sow") ?></a>
            </div>
            <div class="nav-toggler"><span></span></div>
            <ul class="nav">
                <li><a href="#home" class="active"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="#about"><i class="fa fa-user"></i>À propos de moi</a></li>
                <li><a href="#services"><i class="fa fa-list"></i>Services</a></li>
                <li><a href="#certifications"><i class="fa fa-certificate"></i>Certifications</a></li>
                <li><a href="#portfolio"><i class="fa fa-briefcase"></i>Portfolio</a></li>
                <li><a href="#contact"><i class="fa fa-comments"></i>Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-separator" aria-hidden="true"></li>
                    <li><a href="dashboard.php" class="nav-external"><i class="fa fa-gauge"></i>Dashboard</a></li>
                    <li><a href="logout.php" class="nav-external"><i class="fa fa-right-from-bracket"></i>Déconnexion</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <section class="home active section" id="home">
                <div class="container">
                    <div class="row home-row">
                        <div class="home-img home-img-mobile padd-15">
                            <img src="<?= e($mobileImage) ?>" alt="Portrait de <?= e($s['full_name'] ?? '') ?>">
                        </div>
                        <div class="home-info padd-15">
                            <h3 class="hello">Bonjour, je me nomme <span class="name"><?= e($s['full_name'] ?? '') ?></span></h3>
                            <h3 class="my-profession">Je suis <span class="typing" data-strings="<?= e(implode('|', $typingStrings)) ?>"><?= e($typingStrings[0] ?? 'Développeur web') ?></span></h3>
                            <p><?= nl2br(e($s['home_intro'] ?? '')) ?></p>
                            <?php if (!empty($s['cv_file']) && file_exists($s['cv_file'])): ?>
                                <a href="download-cv.php" class="btn"><?= e($s['cv_button_label'] ?? 'Télécharger mon CV') ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="home-img home-img-desktop padd-15">
                            <?php if ($profileImage && file_exists($profileImage)): ?>
                                <img src="<?= e($profileImage) ?>" alt="Portrait de <?= e($s['full_name'] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="about section" id="about">
                <div class="container">
                    <div class="row">
                        <div class="section-title padd-15"><h2>À propos de moi</h2></div>
                    </div>
                    <div class="row">
                        <div class="about-content padd-15">
                            <div class="row">
                                <div class="about-text padd-15">
                                    <h3><?= e($s['about_title'] ?? '') ?></h3>
                                    <p><?= nl2br(e($s['about_text'] ?? '')) ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="personnel-info padd-15">
                                    <div class="row">
                                        <div class="info-item padd-15"><p>Date de naissance : <span><?= e($s['birthday_display'] ?? '') ?></span></p></div>
                                        <div class="info-item padd-15"><p>Âge : <span><?= (int)$age ?> ans</span></p></div>
                                        <div class="info-item padd-15">
                                            <p>Site web : <span>
                                                <a href="<?= e($s['website_url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer"><?= e($s['website_label'] ?? '') ?></a>
                                            </span></p>
                                        </div>
                                        <div class="info-item padd-15"><p>Email : <span><?= e($s['email'] ?? '') ?></span></p></div>
                                        <div class="info-item padd-15"><p>Diplôme : <span><?= e($s['degree'] ?? '') ?></span></p></div>
                                        <div class="info-item padd-15"><p>Téléphone : <span><?= e($s['phone'] ?? '') ?></span></p></div>
                                        <div class="info-item padd-15"><p>Adresse : <span><?= e($s['address'] ?? '') ?></span></p></div>
                                        <div class="info-item padd-15"><p>Freelance : <span><?= e($s['freelance_status'] ?? '') ?></span></p></div>
                                    </div>
                                    <div class="row">
                                        <div class="buttons padd-15">
                                            <a href="#contact" data-section-index="1" class="btn hire-me">Me contacter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="skills padd-15">
                                    <div class="row">
                                        <?php foreach ($skills as $skill): ?>
                                            <div class="skill-item padd-15">
                                                <h5><?= e($skill['name']) ?></h5>
                                                <div class="progress">
                                                    <div class="progress-in" style="width: <?= (int)$skill['percent'] ?>%;"></div>
                                                    <div class="skill-percent"><?= (int)$skill['percent'] ?>%</div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="education padd-15">
                                    <h3 class="title">Éducation</h3>
                                    <div class="row">
                                        <div class="timline-box padd-15">
                                            <div class="timeline shadow-dark">
                                                <?php foreach ($education as $item): ?>
                                                    <div class="timeline-item">
                                                        <div class="circle-dot"></div>
                                                        <h3 class="timeline-date"><i class="fa fa-calendar"></i><?= e($item['date_label']) ?></h3>
                                                        <h4 class="timeline-title"><?= e($item['title']) ?></h4>
                                                        <p class="timeline-text"><?= nl2br(e($item['description'])) ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="experience padd-15">
                                    <h3 class="title">Expérience</h3>
                                    <div class="row">
                                        <div class="timline-box padd-15">
                                            <div class="timeline shadow-dark">
                                                <?php foreach ($experience as $item): ?>
                                                    <div class="timeline-item">
                                                        <div class="circle-dot"></div>
                                                        <h3 class="timeline-date"><i class="fa fa-calendar"></i><?= e($item['date_label']) ?></h3>
                                                        <h4 class="timeline-title"><?= e($item['title']) ?></h4>
                                                        <p class="timeline-text"><?= nl2br(e($item['description'])) ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="service section" id="services">
                <div class="container">
                    <div class="row"><div class="section-title"><h2>Services</h2></div></div>
                    <div class="row">
                        <?php foreach ($services as $service): ?>
                            <div class="service-item padd-15">
                                <div class="service-item-inner">
                                    <div class="icon"><i class="<?= e($service['icon']) ?>"></i></div>
                                    <h4><?= e($service['title']) ?></h4>
                                    <p><?= e($service['description']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="certifications section" id="certifications">
                <div class="container">
                    <div class="row">
                        <div class="section-title padd-15">
                            <h2>Certifications</h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (empty($certifications)): ?>
                            <div class="padd-15"><p>Aucune certification pour le moment.</p></div>
                        <?php endif; ?>
                        <?php foreach ($certifications as $cert): ?>
                            <div class="certification-item padd-15">
                                <div class="certification-item-inner">
                                    <div class="icon">
                                        <i class="<?= e($cert['icon'] ?: 'fa fa-certificate') ?>"></i>
                                    </div>
                                    <div class="certification-body">
                                        <span class="certification-issuer"><?= e($cert['issuer']) ?></span>
                                        <h4><?= e($cert['title']) ?></h4>
                                        <?php if (!empty($cert['date_label'])): ?>
                                            <span class="certification-date"><i class="fa fa-calendar"></i> <?= e($cert['date_label']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($cert['description'])): ?>
                                            <p><?= e($cert['description']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($cert['url'])): ?>
                                            <a href="<?= e($cert['url']) ?>" target="_blank" rel="noopener noreferrer" class="certification-link">
                                                Voir le certificat <i class="fa fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="portfolio section" id="portfolio">
                <div class="container">
                    <div class="row"><div class="section-title"><h2>Portfolio</h2></div></div>
                    <div class="row">
                        <div class="portfolio-heading padd-15">
                            <h2><?= e($s['portfolio_heading'] ?? 'Mes derniers projets :') ?></h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (empty($projects)): ?>
                            <div class="padd-15"><p>Aucun projet pour le moment.</p></div>
                        <?php endif; ?>
                        <?php foreach ($projects as $project): ?>
                            <div class="portfolio-item padd-15">
                                <div class="portfolio-item-inner shadow-dark">
                                    <div class="portfolio-img">
                                        <?php $src = cheminImageProjet($project['image']); ?>
                                        <?php if ($src): ?>
                                            <img src="<?= e($src) ?>" alt="<?= e($project['title']) ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="portfolio-info">
                                        <h4 class="portfolio-title"><?= e($project['title']) ?></h4>
                                        <p class="portfolio-description"><?= e($project['description']) ?></p>
                                        <div class="portfolio-meta">
                                            <span class="portfolio-date">
                                                <i class="fa fa-calendar"></i> <?= date('d/m/Y', strtotime($project['published_at'])) ?>
                                            </span>
                                            <?php if (!empty($project['url'])): ?>
                                                <a href="<?= e($project['url']) ?>" target="_blank" rel="noopener noreferrer" class="portfolio-link">
                                                    Visiter <i class="fa fa-link"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="contact section" id="contact">
                <div class="container">
                    <div class="row"><div class="section-title"><h2>Contact</h2></div></div>
                    <h3 class="contact-title padd-15"><?= e($s['contact_title'] ?? '') ?></h3>
                    <h4 class="contact-sub-title padd-15"><?= e($s['contact_subtitle'] ?? '') ?></h4>
                    <div class="row">
                        <div class="contact-info-item padd-15">
                            <div class="icon"><i class="fa fa-phone"></i></div>
                            <h4>Appelez-moi</h4>
                            <?php if (!empty($s['phone_secondary'])): ?><p><?= e($s['phone_secondary']) ?></p><?php endif; ?>
                            <?php if (!empty($s['phone'])): ?><p><?= e($s['phone']) ?></p><?php endif; ?>
                        </div>
                        <div class="contact-info-item padd-15">
                            <div class="icon"><i class="fa fa-map-marker-alt"></i></div>
                            <h4>Localisation</h4>
                            <?php if (!empty($s['address'])): ?><p><?= e($s['address']) ?></p><?php endif; ?>
                            <?php if (!empty($s['address_secondary'])): ?><p><?= e($s['address_secondary']) ?></p><?php endif; ?>
                        </div>
                        <div class="contact-info-item padd-15">
                            <div class="icon"><i class="fa fa-envelope"></i></div>
                            <h4>Email</h4>
                            <p><?= e($s['email'] ?? '') ?></p>
                        </div>
                        <div class="contact-info-item padd-15">
                            <div class="icon"><i class="fa fa-globe-europe"></i></div>
                            <h4>Site web</h4>
                            <a href="<?= e($s['website_url'] ?? '#') ?>" target="_blank" rel="noopener noreferrer">
                                <p><?= e($s['website_label'] ?? '') ?></p>
                            </a>
                        </div>
                    </div>
                    <h3 class="contact-title padd-15"><?= e($s['contact_form_title'] ?? '') ?></h3>
                    <h4 class="contact-sub-title padd-15"><?= e($s['contact_form_subtitle'] ?? '') ?></h4>

                    <?php if (isset($_SESSION['message_sent']) || (isset($_GET['success']) && $_GET['success'] == 'sent')): ?>
                        <div class="alert-success padd-15">
                            <i class="fa fa-check-circle"></i> Votre message a été envoyé avec succès !
                        </div>
                        <?php unset($_SESSION['message_sent']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <div class="contact-form padd-15">
                            <form action="index.php#contact" method="POST">
                                <div class="row">
                                    <div class="form-item col-6 padd-15">
                                        <div class="form-group">
                                            <label for="prenom" class="sr-only">Prénom</label>
                                            <input type="text" class="form-control" placeholder="Prénom" name="prenom" id="prenom" required>
                                        </div>
                                    </div>
                                    <div class="form-item col-6 padd-15">
                                        <div class="form-group">
                                            <label for="nom" class="sr-only">Nom</label>
                                            <input type="text" class="form-control" placeholder="Nom" name="nom" id="nom" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-item col-12 padd-15">
                                        <div class="form-group">
                                            <label for="email" class="sr-only">E-mail</label>
                                            <input type="email" class="form-control" placeholder="E-mail" name="email" id="email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-item col-12 padd-15">
                                        <div class="form-group">
                                            <label for="message" class="sr-only">Message</label>
                                            <textarea name="message" class="form-control" id="message" placeholder="Message" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="position: relative;">
                                    <div class="form-item col-12 padd-15">
                                        <button type="submit" class="btn">Envoyer le message</button>
                                        <?php if (!isset($_SESSION['user_id'])): ?>
                                            <button type="button" class="admin-login-btn" onclick="event.stopPropagation(); openLoginModal();" title="Connexion admin" aria-label="Connexion administrateur">
                                                <i class="fa fa-right-to-bracket"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div id="loginModal" class="modal-overlay" style="display: none;" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-labelledby="loginModalTitle">
            <div class="modal-header">
                <h3 id="loginModalTitle"><i class="fa fa-right-to-bracket"></i> Connexion Admin</h3>
                <button type="button" class="close-modal" onclick="closeLoginModal()" aria-label="Fermer">&times;</button>
            </div>
            <?php if (isset($_GET['error']) && $_GET['error'] == 'auth'): ?>
                <p class="login-error">Identifiant ou mot de passe incorrect.</p>
            <?php endif; ?>
            <form action="login_process.php" method="POST" class="login-form">
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input type="text" name="login" placeholder="Identifiant" required autocomplete="username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fa fa-key"></i>
                        <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn-submit">Se connecter</button>
            </form>
        </div>
    </div>

    <div class="style-switcher">
        <div class="style-switcher-toggler s-icon"><i class="fas fa-cog fa-spin"></i></div>
        <div class="day-night s-icon"><i class="fas"></i></div>
        <h4>Couleurs du thème</h4>
        <div class="colors">
            <span class="color-1" onclick="setActiveStyle('color-1')"></span>
            <span class="color-2" onclick="setActiveStyle('color-2')"></span>
            <span class="color-3" onclick="setActiveStyle('color-3')"></span>
            <span class="color-4" onclick="setActiveStyle('color-4')"></span>
            <span class="color-5" onclick="setActiveStyle('color-5')"></span>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.1.0/typed.umd.js" integrity="sha512-+2pW8xXU/rNr7VS+H62aqapfRpqFwnSQh9ap6THjsm41AxgA0MhFRtfrABS+Lx2KHJn82UOrnBKhjZOXpom2LQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/script.js"></script>
    <script src="js/style-switcher.js"></script>
    <script>
    window.openLoginModal = function() {
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            const firstInput = modal.querySelector('input');
            if (firstInput) firstInput.focus();
        }
    };
    function currentSectionHash() {
        const hash = (window.location.hash || '').replace('#', '');
        const remembered = localStorage.getItem('portfolioActiveSection');
        const section = hash || remembered || 'contact';
        return '#' + section;
    }
    window.closeLoginModal = function() {
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
            if (window.location.search) {
                window.history.replaceState({}, document.title, window.location.pathname + currentSectionHash());
            }
        }
    };
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('loginModal')) closeLoginModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLoginModal();
    });
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) openLoginModal();
        if (urlParams.has('success') || urlParams.has('logout')) {
            setTimeout(function() {
                window.history.replaceState({}, document.title, window.location.pathname + currentSectionHash());
            }, 2500);
        }
    });
    </script>
</body>
</html>
