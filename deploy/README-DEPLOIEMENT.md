# Déploiement du portfolio

## Fichiers prêts
- `deploy/portfolio.sql` — export de ta base locale
- `config.db.php` — config BDD (local auto / prod à remplir)
- `.htaccess` — sécurité basique

## Étapes Hostinger (ou hébergeur similaire)

### 1. Base de données
1. Dans hPanel → Bases de données MySQL → créer une BDD + utilisateur
2. Note : hôte, nom BDD, utilisateur, mot de passe
3. phpMyAdmin → Importer → choisir `deploy/portfolio.sql`

### 2. Config production
Édite `config.db.php` et remplis la section PRODUCTION :
- host (souvent `localhost`)
- dbname
- user
- pass

### 3. Fichiers à envoyer (FTP ou Gestionnaire de fichiers)
Envoie tout le contenu du dossier Portfolio **sauf** :
- `dechets/`
- `deploy/` (garde le SQL pour toi, ou importe-le avant)
- fichiers `_tmp*`

Vers le dossier public du site (`public_html` ou sous-domaine).

### 4. Droits d'écriture
Le dossier `uploads/` (et sous-dossiers) doit être accessible en écriture (chmod 755 ou 775).

### 5. Test
1. Ouvre ton domaine
2. Connecte-toi (mêmes identifiants admin qu'en local)
3. Vérifie Contact, Dashboard, upload photo/projet

## Identifiants admin
Ce sont ceux de la table `connexion` de ta BDD locale (exportés dans le SQL).
