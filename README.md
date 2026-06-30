# FitConnect — Application de gestion du réseau de salles de sport

Application backend PHP / PDO / MySQL pour la gestion du réseau FitConnect
(4 salles de sport au Maroc), développée pour Karim Benslimane.

L'application remplace le suivi manuel des séances (fichiers Excel par salle)
par une base de données relationnelle centralisée et une application en
couches, facilement reprenable par une équipe technique.

## Architecture

L'application suit une architecture en couches strictement séparées :

```
fitconnect/
├── config/
│   └── Database.php          # Connexion PDO centralisée (Singleton)
├── app/
│   ├── Entities/              # Adherent, Abonnement, Seance, Salle
│   ├── Repositories/          # Accès aux données (requêtes paramétrées)
│   ├── Services/              # Règles de gestion métier
│   └── Controllers/           # Orchestration Services + Repositories
├── views/
│   ├── layouts/                # header.php / footer.php communs
│   ├── dashboard/
│   ├── adherents/
│   ├── abonnements/
│   └── seances/
├── public/
│   ├── index.php               # Point d'entrée unique (front controller)
│   ├── test.php                # Tests rapides des couches (hors UI)
│   └── assets/css/style.css
├── .gitignore
└── README.md
```

### Flux de données

```
Vue (views/) → Controller (app/Controllers/) → Service (app/Services/)
            → Repository (app/Repositories/) → PDO → MySQL
```

Aucune logique métier n'est mélangée dans les vues ou le point d'entrée :
les règles de gestion vivent exclusivement dans la couche `Services`.

## Règles de gestion implémentées

- Un adhérent est inscrit dans une seule salle du réseau (`adherent.id_salle`).
- Un adhérent ne détient qu'**un seul abonnement actif à la fois**
  (vérifié dans `AbonnementService::creerAbonnement()`).
- Une séance ne peut être enregistrée que si l'abonnement de l'adhérent
  est **valide à la date du jour** (`SeanceService::enregistrerSeance()`
  appelle `AbonnementService::estAbonnementValide()`).
- Un adhérent **ne peut pas être supprimé** s'il possède des séances
  enregistrées ou un abonnement en cours
  (`AdherentService::deleteAdherent()`).
- Toutes les requêtes SQL sont **paramétrées** (PDO prepared statements)
  pour éviter les injections SQL.
- L'intégrité référentielle est garantie par les contraintes `FOREIGN KEY`
  définies au niveau du MLD / de la base MySQL.

## Base de données

La base `fitconnect_db` comprend 4 tables :

| Table         | Description                                        |
|---------------|-----------------------------------------------------|
| `salle`       | Les 4 salles du réseau (nom, ville, adresse)        |
| `adherent`    | Les membres inscrits, liés à une salle              |
| `abonnement`  | Les abonnements des adhérents (Mensuel/Trim./Annuel)|
| `seance`      | Les séances d'activité physique enregistrées        |

Le script SQL d'origine (structure + jeu de données de test) est fourni
séparément (`fitconnect_db.sql`) et doit être importé dans MySQL/MariaDB
avant de lancer l'application.

## Installation

1. Importer la base de données :
   ```bash
   mysql -u root -p < fitconnect_db.sql
   ```
2. Configurer les identifiants de connexion dans `config/Database.php`
   (constantes `HOST`, `DBNAME`, `USER`, `PASS`).
3. Lancer un serveur PHP local depuis le dossier `public/` :
   ```bash
   php -S localhost:8000 -t public
   ```
4. Ouvrir le navigateur sur `http://localhost:8000/index.php`.

## Tests

Le fichier `public/test.php` permet de valider chaque couche
(Entities, Repositories, Services) indépendamment de l'interface :

```bash
php public/test.php
```

ou via le navigateur : `http://localhost:8000/test.php`

## Pages disponibles

| Page         | URL                                  | Description                          |
|--------------|---------------------------------------|---------------------------------------|
| Dashboard    | `index.php?page=dashboard`            | Vue d'ensemble du réseau              |
| Adhérents    | `index.php?page=adherents`            | Liste, création, modification, suppression |
| Abonnements  | `index.php?page=abonnements`          | Liste, création, résiliation, historique |
| Séances      | `index.php?page=seances`              | Liste, enregistrement, suppression    |

## Stack technique

- PHP 8.2+
- PDO (MySQL / MariaDB)
- Architecture en couches : Entities → Repositories → Services → Controllers → Views
- Aucun framework externe (vanilla PHP)
