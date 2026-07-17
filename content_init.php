<?php
/**
 * Initialise les tables de contenu du portfolio et injecte les données actuelles.
 * Peut être appelé plusieurs fois sans écraser les données existantes.
 */
function initialiserContenuSite(): void {
    $cnx = Connexion();

    $cnx->exec("CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $cnx->exec("CREATE TABLE IF NOT EXISTS site_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
        sort_order INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $cnx->exec("CREATE TABLE IF NOT EXISTS site_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        icon VARCHAR(80) NOT NULL DEFAULT 'fa fa-globe',
        sort_order INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $cnx->exec("CREATE TABLE IF NOT EXISTS site_timeline (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('education','experience') NOT NULL,
        date_label VARCHAR(120) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        sort_order INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $cnx->exec("CREATE TABLE IF NOT EXISTS site_certifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        issuer VARCHAR(150) NOT NULL,
        date_label VARCHAR(120) NOT NULL DEFAULT '',
        description TEXT NULL,
        url VARCHAR(500) NULL,
        icon VARCHAR(80) NOT NULL DEFAULT 'fa fa-certificate',
        sort_order INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $certCount = (int) $cnx->query("SELECT COUNT(*) FROM site_certifications")->fetchColumn();
    if ($certCount === 0) {
        $certs = [
            ['Développement Web', 'Udemy', '2024', 'Certification en développement web couvrant le front-end et le back-end.', '', 'fa fa-certificate', 1],
            ['Développement Web', 'Coursera', '2024', 'Formation certifiante en développement web sur Coursera.', '', 'fa fa-award', 2],
            ['Entrepreneuriat Numérique', 'ForceN', '2024', 'Programme ForceN en entrepreneuriat numérique.', '', 'fa fa-medal', 3],
        ];
        $certStmt = $cnx->prepare("INSERT INTO site_certifications (title, issuer, date_label, description, url, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($certs as $c) {
            $certStmt->execute($c);
        }
    }

    // Ajoute les réglages manquants sans écraser les existants
    $extras = [
        'mobile_image' => 'photos/moi.jpeg',
        'favicon' => 'photos/moi.jpeg',
    ];
    $insertExtra = $cnx->prepare(
        "INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)"
    );
    foreach ($extras as $key => $value) {
        $insertExtra->execute([$key, $value]);
    }

    $count = (int) $cnx->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
    if ($count > 0) {
        return;
    }

    $defaults = [
        'site_title' => 'Mohamed SOW — Portfolio',
        'logo_letter' => 'M',
        'logo_text' => "'Sow",
        'full_name' => 'Mohamed SOW',
        'birthday' => '1998-09-27',
        'birthday_display' => '27 Sep 1998',
        'website_url' => 'https://sowcoder.com/',
        'website_label' => 'www.sowcoder.com',
        'email' => 'sow.mohamedra@gmail.com',
        'degree' => 'Licence Professionnelle',
        'phone' => '+212 684 088765',
        'phone_secondary' => '+221 77 790 14 60',
        'address' => 'Casablanca / Maroc',
        'address_secondary' => 'Sangalkam, Dakar, Sénégal',
        'freelance_status' => 'Disponible',
        'profile_image' => 'photos/mahou.jpg',
        'mobile_image' => 'photos/moi.jpeg',
        'favicon' => 'photos/moi.jpeg',
        'cv_file' => 'photos/CV-Mohamed-SOW.pdf',
        'home_intro' => "Je suis un développeur web avec un peu plus de 3 ans d'expérience dans le domaine du web. Ma profession est la création de sites et d'applications web. J'ai également de l'expérience en marketing digital.",
        'typing_strings' => "Développeur web\nDigital Marketer\nIntégrateur web",
        'about_title' => 'Bienvenue sur mon portfolio !',
        'about_text' => "Je suis titulaire d'une licence professionnelle en Informatique, Réseaux et Sécurité, ce qui m'a permis d'acquérir une solide base technique dans le domaine informatique. Cependant, ma passion m'a orienté vers le développement web, un univers dynamique et en constante évolution.\n\nJ'ai également obtenu des certifications en Développement web sur différentes plateformes comme Coursera, Udemy et le programme ForceN, qui attestent de mes compétences à concevoir et développer des solutions complètes, depuis l'interface utilisateur jusqu'à la gestion des bases de données. Grâce à cette expertise, je suis capable de transformer des idées en projets concrets et fonctionnels.\n\nAvec une première expérience dans le domaine informatique, je continue à perfectionner mes compétences techniques et à relever de nouveaux défis pour répondre aux besoins variés du développement web. Je suis motivé par l'idée de créer des expériences utilisateur intuitives et des applications performantes.\n\nN'hésitez pas à parcourir mes projets pour en savoir plus sur mon travail ou à me contacter pour discuter de vos idées.",
        'contact_title' => 'Avez-vous des questions ?',
        'contact_subtitle' => 'Je suis à votre service',
        'contact_form_title' => 'ENVOYEZ-MOI UN EMAIL',
        'contact_form_subtitle' => 'JE SUIS TRÈS RÉACTIF AUX MESSAGES',
        'portfolio_heading' => 'Mes derniers projets :',
        'cv_button_label' => 'Télécharger mon CV',
    ];

    $stmt = $cnx->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    $skills = [
        ['HTML', 96, 1],
        ['CSS / Tailwind CSS', 88, 2],
        ['Bootstrap', 89, 3],
        ['JavaScript', 84, 4],
        ['PHP', 92, 5],
        ['Symfony', 89, 6],
        ['SQL', 91, 7],
        ['React.js / Vite.js', 83, 8],
        ['GitHub / Git', 76, 9],
    ];
    $skillStmt = $cnx->prepare("INSERT INTO site_skills (name, percent, sort_order) VALUES (?, ?, ?)");
    foreach ($skills as $s) {
        $skillStmt->execute($s);
    }

    $services = [
        ['Création de sites web', "Conception et réalisation de sites internet sur mesure, du cahier des charges jusqu'à la mise en ligne.", 'fa fa-globe', 1],
        ['Intégration et design (Front-end)', 'Interfaces modernes, responsives et accessibles : HTML, CSS, JavaScript et frameworks front-end.', 'fa fa-display', 2],
        ['Développement Back-end', 'Logique serveur, API et bases de données pour des applications web fiables et évolutives.', 'fa fa-database', 3],
        ['Maintenance et optimisation', 'Suivi, corrections, mises à jour et amélioration des performances après la mise en ligne.', 'fa fa-screwdriver-wrench', 4],
        ['Marketing digital', "Accompagnement pour améliorer la visibilité, le référencement et l'impact de votre présence en ligne.", 'fa fa-chart-line', 5],
        ['Design graphique', "Création d'éléments visuels (affiches, flyers, bannières, interfaces) au service de votre identité.", 'fa fa-wand-magic-sparkles', 6],
    ];
    $serviceStmt = $cnx->prepare("INSERT INTO site_services (title, description, icon, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($services as $s) {
        $serviceStmt->execute($s);
    }

    $timeline = [
        ['education', 'Février 2024 à nos jours', 'Certifications Développeur web', "Ces certifications Développeur web attestent de mes compétences à concevoir, développer et déployer des applications web complètes. Elles couvrent à la fois le développement front-end et back-end.\n\nPlateformes : Udemy (Développement Web), Coursera (Développement Web), ForceN (Entrepreneuriat Numérique).", 1],
        ['education', 'Octobre 2022 - Juillet 2023', 'Diplôme licence professionnelle en Informatique, Réseaux et Sécurité', "Titulaire d'une Licence Professionnelle en Informatique, Réseaux et Sécurité, j'ai acquis des compétences techniques solides dans la gestion des infrastructures et la sécurité informatique.", 2],
        ['education', 'Octobre 2020 à Juillet 2022', 'Technicien Développement Informatique', "En tant que Technicien en Développement Informatique, j'ai acquis des compétences dans la conception, le développement et la maintenance d'applications logicielles.", 3],
        ['experience', 'Depuis Novembre 2024', 'Responsable et Fondateur — Sowcoder', "En tant que fondateur et responsable de Sowcoder, j'ai créé et développé une entreprise spécialisée dans les services informatiques, notamment dans le domaine du développement web.", 1],
        ['experience', 'Août 2022 à Janvier 2025', 'Assistant technique — machine Van De Wiele', "J'ai assisté un ingénieur dans l'installation et la mise en service d'une machine Van de Wiele, spécialisée dans le tissage industriel.", 2],
        ['experience', 'Juin à Septembre 2021', 'Stage à ABH Call Center en Gestion Relation Client', "Lors de mon stage en gestion de la relation client, j'ai acquis des compétences clés dans l'utilisation d'outils CRM.", 3],
    ];
    $tlStmt = $cnx->prepare("INSERT INTO site_timeline (type, date_label, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($timeline as $t) {
        $tlStmt->execute($t);
    }
}
