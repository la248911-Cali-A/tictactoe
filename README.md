# tictactoe

Application web de Morpion jouable contre l'ordinateur, avec sauvegarde des statistiques en base de données.

---

## Table des matières

- [Présentation](#présentation)
- [Fonctionnalités](#fonctionnalités)
- [Technologies utilisées](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration de la base de données](#configuration-de-la-base-de-données)
- [Comment jouer](#comment-jouer)
- [Structure du projet](#structure-du-projet)
- [Lancer les tests](#lancer-les-tests)

---

## Présentation

TicTacToe est une application web permettant de jouer au morpion contre un adversaire contrôlé par l'ordinateur. Le joueur choisit son pseudo, son pion (croix ou cercle), la taille de la grille et qui commence. Les résultats de chaque partie sont enregistrés en base de données, et un classement des meilleurs joueurs est affiché sur la page d'accueil.

---

## Fonctionnalités

- Jeu contre l'ordinateur (IA aléatoire)
- Choix de la taille de la grille : 3x3 ou 4x4
- Choix du pion : croix ou cercle
- Choix de qui commence : le joueur, l'ordinateur, ou aléatoire
- Statistiques personnelles en temps réel (victoires, défaites, nuls)
- Classement des 5 meilleurs joueurs par taille de grille
- Option "Rejouer" sans rechargement de page (requête AJAX)
- Sauvegarde automatique des résultats en base de données MySQL

---

## Technologies utilisées

| Couche          | Technologie                        |
|-----------------|------------------------------------|
| Back-end        | PHP                                |
| Front-end       | HTML, CSS, JavaScript              |
| Base de données | MySQL 8 via phpMyAdmin (XAMPP)     |
| Architecture    | MVC (Model - View - Controller)    |
| Tests           | PHPUnit                            |

---

## Prérequis

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + phpMyAdmin inclus)
- PHP >= 8.1
- [Composer](https://getcomposer.org/) >= 2.0

---

## Installation

### 1. Cloner le dépôt

Placez le projet dans le dossier `htdocs` de XAMPP :

```bash
git clone https://github.com/la248911-Cali-A/tictactoe.git C:/xampp/htdocs/tictactoe
```

### 2. Installer les dépendances

```bash
cd C:/xampp/htdocs/tictactoe
composer install
```

### 3. Démarrer XAMPP

Lancez le **XAMPP Control Panel** et démarrez les modules **Apache** et **MySQL**.

### 4. Accéder à l'application

Ouvrez votre navigateur et rendez-vous sur :

```
http://localhost/tictactoe
```

---

## Configuration de la base de données

### Créer la base via phpMyAdmin

1. Ouvrez [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Cliquez sur **SQL** dans la barre de navigation
3. Copiez-collez le contenu du fichier `scriptDb.sql` et exécutez-le

Ou importez directement le fichier :

1. Cliquez sur **Importer**
2. Choisissez le fichier `scriptDb.sql`
3. Cliquez sur **Exécuter**

Cela crée la base `tictactoe` avec les tables `Joueur` et `Partie`.

### Configurer la connexion

Ouvrez le fichier `model/dbConnection.php` et adaptez les paramètres si besoin :

```php
private string $host     = 'localhost';
private string $dbname   = 'tictactoe';
private string $username = 'root';
private string $password = ''; // Vide par défaut avec XAMPP
```

---

## Comment jouer

1. Rendez-vous sur [http://localhost/tictactoe](http://localhost/tictactoe)
2. Entrez votre pseudo, choisissez votre pion, la taille de la grille, qui commence, puis cliquez sur **Jouer**
3. Cliquez sur une case libre pour placer votre pion, ou attendez que l'ordinateur joue
4. Alignez vos pions en ligne, colonne ou diagonale pour remporter la partie
5. En fin de partie, cliquez sur **Rejouer** pour relancer une partie avec les mêmes paramètres, ou sur **Retour au menu** pour revenir à l'accueil

## Structure du projet

```
tictactoe/
├── assets/                  # Images et icônes
│   ├── cercle.png
│   ├── croix.png
│   ├── grille3.png
│   ├── grille4.png
│   ├── person-fill.svg
│   ├── robot.svg
│   └── shuffle.svg
├── controller/
│   └── jeuControlleur.php   # Contrôleur principal (MVC)
├── css/
│   └── style.css            # Feuille de style générale
├── model/
│   ├── dbConnection.php     # Connexion PDO (Singleton)
│   ├── joueurModel.php      # Modèle joueur (CRUD)
│   └── partieModel.php      # Modèle partie (sauvegarde + classement)
├── tests/
│   ├── joueurModelTests.php # Tests unitaires du modèle joueur
│   └── partieModelTests.php # Tests unitaires du modèle partie
├── view/
│   ├── menu.php             # Page d'accueil / sélection de partie
│   └── partie.php           # Page de jeu
├── vendor/                  # Dépendances Composer (PHPUnit, etc.)
├── .gitignore
├── composer.json
├── index.php                # Point d'entrée (redirige vers menu.php)
├── scriptDb.sql             # Script de création de la base de données
└── README.md
```

---

## Lancer les tests

Les tests unitaires utilisent **PHPUnit** et une base de données SQLite en mémoire. Ils sont totalement isolés et ne touchent pas votre base MySQL.

### 1. Installer PHPUnit via Composer

Si ce n'est pas déjà fait :

```bash
composer require --dev phpunit/phpunit
```

### 2. Lancer tous les tests

Depuis la racine du projet :

```bash
./vendor/bin/phpunit tests/
```

Sur Windows :

```bash
vendor\bin\phpunit tests\
```

### 3. Lancer un fichier de test spécifique

```bash
# Tests du modèle joueur uniquement
./vendor/bin/phpunit tests/joueurModelTests.php

# Tests du modèle partie uniquement
./vendor/bin/phpunit tests/partieModelTests.php
```

### Couverture des tests

| Fichier testé     | Classe testée | Méthodes testées                                            |
|-------------------|---------------|-------------------------------------------------------------|
| `joueurModel.php` | `Joueur`      | `getOrCreateJoueur()`, `getStatsJoueur()`, `updateJoueur()` |
| `partieModel.php` | `Partie`      | `savePartie()`, `getClassement()`                           |