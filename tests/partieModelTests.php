<?php
require_once __DIR__ . '/../model/partieModel.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la classe Partie (partieModel.php)
 * Prérequis     : composer require --dev phpunit/phpunit
 */
class partieModelTests extends TestCase
{
    private PDO $pdo;
    private Partie $partie;

    protected function setUp(): void
    {
        // DB SQLite en mémoire pour isoler les tests de la vraie DB
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo->exec("
            CREATE TABLE Joueur (
                Id_Joueur    INTEGER PRIMARY KEY AUTOINCREMENT,
                pseudo       VARCHAR(50) NOT NULL UNIQUE,
                nb_parties   INT NOT NULL DEFAULT 0,
                nb_victoires INT NOT NULL DEFAULT 0,
                nb_defaites  INT NOT NULL DEFAULT 0,
                nb_nuls      INT NOT NULL DEFAULT 0
            )
        ");

        $this->pdo->exec("
            CREATE TABLE Partie (
                Id_Partie     INTEGER PRIMARY KEY AUTOINCREMENT,
                resultat      INT NOT NULL,
                taille_grille INT NOT NULL,
                Id_Joueur     INT NOT NULL
            )
        ");

        $this->partie = new Partie($this->pdo);
    }

    // Fonctions pour ajouter dans la base de données (pour faciliter les tests)
    private function insertJoueur(string $pseudo)
    {
        $this->pdo->exec("INSERT INTO Joueur (pseudo) VALUES ('$pseudo')");
        return (int) $this->pdo->lastInsertId();
    }

    private function insertPartie(int $idJoueur, string $resultat, int $tailleGrille)
    {
        $this->partie->savePartie($idJoueur, $resultat, $tailleGrille);
    }

    // Tests de la fonction savePartie()
    public function testSauvegarderUneVictoire()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'victoire', 3);

        $classement = $this->partie->getClassement(3);
        $this->assertCount(1, $classement);
        $this->assertEquals('Alessio', $classement[0]['pseudo']);
        $this->assertEquals(1, $classement[0]['nb_victoires']);
    }

    public function testSauvegarderUneDefaite()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'defaite', 3);

        $classement = $this->partie->getClassement(3);
        $this->assertEmpty($classement); // Une défaite n'apparaît pas dans le classement
    }

    public function testSauvegarderUnMatchNul()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'nul', 3);

        $classement = $this->partie->getClassement(3);
        $this->assertEmpty($classement); // Un match nul n'apparaît pas dans le classement
    }

    public function testSauvegarderPlusieursParties()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'victoire', 3);
        $this->insertPartie($idJoueur, 'victoire', 3);
        $this->insertPartie($idJoueur, 'defaite', 3);

        $classement = $this->partie->getClassement(3);
        $this->assertEquals(2, $classement[0]['nb_victoires']);
    }

    public function testSauvegarderPartieGrille4x4()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'victoire', 4);

        $classement = $this->partie->getClassement(4);
        $this->assertCount(1, $classement);
        $this->assertEquals('Alessio', $classement[0]['pseudo']);
        $this->assertEquals(1, $classement[0]['nb_victoires']);
    }

    // Tests de la fonction getClassement()
    public function testClassementVide()
    {
        $classement = $this->partie->getClassement(3);

        $this->assertIsArray($classement);
        $this->assertEmpty($classement);
    }

    public function testClassementAvecUnJoueur()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'victoire', 3);
        $this->insertPartie($idJoueur, 'victoire', 3);

        $classement = $this->partie->getClassement(3);

        $this->assertCount(1, $classement);
        $this->assertEquals('Alessio', $classement[0]['pseudo']);
        $this->assertEquals(2, $classement[0]['nb_victoires']);
    }

    public function testClassementPlusieursJoueurs()
    {
        $idAlessio = $this->insertJoueur('Alessio');
        $idCali = $this->insertJoueur('Cali');

        $this->insertPartie($idAlessio, 'victoire', 3);
        $this->insertPartie($idAlessio, 'victoire', 3);
        $this->insertPartie($idCali, 'victoire', 3);
        $this->insertPartie($idCali, 'victoire', 3);
        $this->insertPartie($idCali, 'victoire', 3);

        $classement = $this->partie->getClassement(3);

        $this->assertEquals('Cali', $classement[0]['pseudo']);
        $this->assertEquals('Alessio', $classement[1]['pseudo']);
    }

    public function testClassementLimite5Joueurs()
    {
        for ($i = 1; $i <= 10; $i++) {
            $idJoueur = $this->insertJoueur("Joueur$i");
            for ($v = 0; $v < $i; $v++) {
                $this->insertPartie($idJoueur, 'victoire', 3);
            }
        }

        $classement = $this->partie->getClassement(3);

        $this->assertCount(5, $classement);
    }

    public function testClassementParTailleGrille()
    {
        $idJoueur = $this->insertJoueur('Alessio');
        $this->insertPartie($idJoueur, 'victoire', 3);
        $this->insertPartie($idJoueur, 'victoire', 3);
        $this->insertPartie($idJoueur, 'victoire', 4);

        $classement3 = $this->partie->getClassement(3);
        $classement4 = $this->partie->getClassement(4);

        $this->assertEquals(2, $classement3[0]['nb_victoires']);
        $this->assertEquals(1, $classement4[0]['nb_victoires']);
    }
}