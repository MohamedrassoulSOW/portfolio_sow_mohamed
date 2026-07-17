-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: portfolio
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connexion`
--

LOCK TABLES `connexion` WRITE;
/*!40000 ALTER TABLE `connexion` DISABLE KEYS */;
INSERT INTO `connexion` VALUES (1,'Mohamed','SOW','sow.mohamedra@gmail.com','45f7beed2bdcb4f05bec150df544f0fe');
/*!40000 ALTER TABLE `connexion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL,
  `action` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (5,'Mohamed','Sow','souleymane@gmail.com','tout dois bien se passer','0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_certifications`
--

DROP TABLE IF EXISTS `site_certifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_certifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `issuer` varchar(150) NOT NULL,
  `date_label` varchar(120) NOT NULL DEFAULT '',
  `description` text,
  `url` varchar(500) DEFAULT NULL,
  `icon` varchar(80) NOT NULL DEFAULT 'fa fa-certificate',
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_certifications`
--

LOCK TABLES `site_certifications` WRITE;
/*!40000 ALTER TABLE `site_certifications` DISABLE KEYS */;
INSERT INTO `site_certifications` VALUES (1,'Développement Web','Udemy','2024','Certification en développement web couvrant le front-end et le back-end.','','fa fa-certificate',1),(2,'Développement Web','Coursera','2024','Formation certifiante en développement web sur Coursera.','','fa fa-award',2),(3,'Entrepreneuriat Numérique','ForceN','2024','Programme ForceN en entrepreneuriat numérique.','','fa fa-medal',3);
/*!40000 ALTER TABLE `site_certifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_services`
--

DROP TABLE IF EXISTS `site_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(80) NOT NULL DEFAULT 'fa fa-globe',
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_services`
--

LOCK TABLES `site_services` WRITE;
/*!40000 ALTER TABLE `site_services` DISABLE KEYS */;
INSERT INTO `site_services` VALUES (1,'Création de sites web','Conception et réalisation de sites internet sur mesure, du cahier des charges jusqu\'à la mise en ligne.','fa fa-globe',1),(2,'Intégration et design (Front-end)','Interfaces modernes, responsives et accessibles : HTML, CSS, JavaScript et frameworks front-end.','fa fa-display',2),(3,'Développement Back-end','Logique serveur, API et bases de données pour des applications web fiables et évolutives.','fa fa-database',3),(4,'Maintenance et optimisation','Suivi, corrections, mises à jour et amélioration des performances après la mise en ligne.','fa fa-screwdriver-wrench',4),(5,'Marketing digital','Accompagnement pour améliorer la visibilité, le référencement et l\'impact de votre présence en ligne.','fa fa-chart-line',5),(6,'Design graphique','Création d\'éléments visuels (affiches, flyers, bannières, interfaces) au service de votre identité.','fa fa-wand-magic-sparkles',6);
/*!40000 ALTER TABLE `site_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES ('about_text','Je suis titulaire d\'une licence professionnelle en Informatique, Réseaux et Sécurité, ce qui m\'a permis d\'acquérir une solide base technique dans le domaine informatique. Cependant, ma passion m\'a orienté vers le développement web, un univers dynamique et en constante évolution.\r\n\r\nJ\'ai également obtenu des certifications en Développement web sur différentes plateformes comme Coursera, Udemy et le programme ForceN, qui attestent de mes compétences à concevoir et développer des solutions complètes, depuis l\'interface utilisateur jusqu\'à la gestion des bases de données. Grâce à cette expertise, je suis capable de transformer des idées en projets concrets et fonctionnels.\r\n\r\nAvec une première expérience dans le domaine informatique, je continue à perfectionner mes compétences techniques et à relever de nouveaux défis pour répondre aux besoins variés du développement web. Je suis motivé par l\'idée de créer des expériences utilisateur intuitives et des applications performantes.\r\n\r\nN\'hésitez pas à parcourir mes projets pour en savoir plus sur mon travail ou à me contacter pour discuter de vos idées.'),('about_title','Bienvenue sur mon portfolio !'),('address','Casablanca / Maroc'),('address_secondary','Sangalkam, Dakar, Sénégal'),('birthday','1998-09-27'),('birthday_display','27 Sep 1998'),('contact_form_subtitle','JE SUIS TRÈS RÉACTIF AUX MESSAGES'),('contact_form_title','ENVOYEZ-MOI UN EMAIL'),('contact_subtitle','Je suis à votre service'),('contact_title','Avez-vous des questions ?'),('cv_button_label','Télécharger mon CV'),('cv_file','photos/CV-Mohamed-SOW.pdf'),('degree','Licence Professionnelle'),('email','sow.mohamedra@gmail.com'),('favicon','photos/moi.jpeg'),('freelance_status','Disponible'),('full_name','Mohamed SOW'),('home_intro','Je suis un développeur web avec un peu plus de 3 ans d\'expérience dans le domaine du web. Ma profession est la création de sites et d\'applications web. J\'ai également de l\'expérience en marketing digital.'),('logo_letter','M'),('logo_text','\'Sow'),('mobile_image','photos/moi.jpeg'),('phone','+212 684 088765'),('phone_secondary','+221 77 790 14 60'),('portfolio_heading','Mes derniers projets :'),('profile_image','photos/mahou.jpg'),('site_title','Mohamed SOW — Portfolio'),('typing_strings','Développeur FullStack\r\nDigital Marketer\r\nIntégrateur web\r\nUI & UX Designer'),('website_label','www.sowcoder.com'),('website_url','https://sowcoder.com/');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_skills`
--

DROP TABLE IF EXISTS `site_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `percent` tinyint unsigned NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_skills`
--

LOCK TABLES `site_skills` WRITE;
/*!40000 ALTER TABLE `site_skills` DISABLE KEYS */;
INSERT INTO `site_skills` VALUES (1,'HTML',96,1),(2,'CSS / Tailwind CSS',88,2),(3,'Bootstrap',89,3),(4,'JavaScript',84,4),(5,'PHP',92,5),(6,'Symfony',89,6),(7,'SQL',91,7),(8,'React.js / Vite.js',83,8),(9,'GitHub / Git',76,9);
/*!40000 ALTER TABLE `site_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_timeline`
--

DROP TABLE IF EXISTS `site_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_timeline` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` enum('education','experience') NOT NULL,
  `date_label` varchar(120) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_timeline`
--

LOCK TABLES `site_timeline` WRITE;
/*!40000 ALTER TABLE `site_timeline` DISABLE KEYS */;
INSERT INTO `site_timeline` VALUES (1,'education','Février 2024 à nos jours','Certifications Développeur web','Ces certifications Développeur web attestent de mes compétences à concevoir, développer et déployer des applications web complètes. Elles couvrent à la fois le développement front-end et back-end.\n\nPlateformes : Udemy (Développement Web), Coursera (Développement Web), ForceN (Entrepreneuriat Numérique).',1),(2,'education','Octobre 2022 - Juillet 2023','Diplôme licence professionnelle en Informatique, Réseaux et Sécurité','Titulaire d\'une Licence Professionnelle en Informatique, Réseaux et Sécurité, j\'ai acquis des compétences techniques solides dans la gestion des infrastructures et la sécurité informatique.',2),(3,'education','Octobre 2020 à Juillet 2022','Technicien Développement Informatique','En tant que Technicien en Développement Informatique, j\'ai acquis des compétences dans la conception, le développement et la maintenance d\'applications logicielles.',3),(4,'experience','Depuis Novembre 2024','Responsable et Fondateur — Sowcoder','En tant que fondateur et responsable de Sowcoder, j\'ai créé et développé une entreprise spécialisée dans les services informatiques, notamment dans le domaine du développement web.',1),(5,'experience','Août 2022 à Janvier 2025','Assistant technique — machine Van De Wiele','J\'ai assisté un ingénieur dans l\'installation et la mise en service d\'une machine Van de Wiele, spécialisée dans le tissage industriel.',2),(6,'experience','Juin à Septembre 2021','Stage à ABH Call Center en Gestion Relation Client','Lors de mon stage en gestion de la relation client, j\'ai acquis des compétences clés dans l\'utilisation d\'outils CRM.',3);
/*!40000 ALTER TABLE `site_timeline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'portfolio'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-17 23:09:31
