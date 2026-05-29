<?php
require_once __DIR__ . '/../model/joueurModel.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la classe Joueur (joueurModel.php)
 * Prérequis     : composer require --dev phpunit/phpunit
 */
class joueurModelTests extends TestCase
{
    private PDO $pdo;
    private Joueur $joueur;

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

        $this->joueur = new Joueur($this->pdo);
    }

    // Tests de la fonction getOrCreateJoueur()
    public function testCreerUnNouveauJoueur()
    {
        $result = $this->joueur->getOrCreateJoueur('Alessio');

        $this->assertEquals('Alessio', $result['pseudo']);
        $this->assertEquals(0, $result['nb_parties']);
        $this->assertEquals(0, $result['nb_victoires']);
        $this->assertEquals(0, $result['nb_defaites']);
        $this->assertEquals(0, $result['nb_nuls']);
    }

    public function testRetourneJoueurExistant()
    {
        $joueurCree = $this->joueur->getOrCreateJoueur('Alessio');
        $joueurAppele = $this->joueur->getOrCreateJoueur('Alessio');

        $this->assertEquals($joueurCree['Id_Joueur'], $joueurAppele['Id_Joueur']);
        $this->assertEquals('Alessio', $joueurAppele['pseudo']);
    }

    public function testCreerDeuxJoueurs()
    {
        $joueur1 = $this->joueur->getOrCreateJoueur('Alessio');
        $joueur2 = $this->joueur->getOrCreateJoueur('Cali');

        $this->assertNotEquals($joueur1['Id_Joueur'], $joueur2['Id_Joueur']);
        $this->assertEquals('Alessio', $joueur1['pseudo']);
        $this->assertEquals('Cali', $joueur2['pseudo']);
    }

    // Tests de la fonction getStatsJoueur()
    public function testRetournerLesStats()
    {
        $this->joueur->getOrCreateJoueur('Alessio');
        $stats = $this->joueur->getStatsJoueur('Alessio');

        $this->assertEquals('Alessio', $stats['pseudo']);
        $this->assertEquals(0, $stats['nb_parties']);
    }

    public function testRetourneFalsePourJoueurInexistant()
    {
        $stats = $this->joueur->getStatsJoueur('Abcd');
        $this->assertFalse($stats);
    }

    // Tests de la fonction updateJoueur()
    public function testVictoireDuJoueur()
    {
        $j = $this->joueur->getOrCreateJoueur('Alessio');
        $this->joueur->updateJoueur($j['Id_Joueur'], 'victoire');

        $stats = $this->joueur->getStatsJoueur('Alessio');
        $this->assertEquals(1, $stats['nb_parties']);
        $this->assertEquals(1, $stats['nb_victoires']);
        $this->assertEquals(0, $stats['nb_defaites']);
        $this->assertEquals(0, $stats['nb_nuls']);
    }

    public function testDefaiteDuJoueur()
    {
        $j = $this->joueur->getOrCreateJoueur('Alessio');
        $this->joueur->updateJoueur($j['Id_Joueur'], 'defaite');

        $stats = $this->joueur->getStatsJoueur('Alessio');
        $this->assertEquals(1, $stats['nb_parties']);
        $this->assertEquals(0, $stats['nb_victoires']);
        $this->assertEquals(1, $stats['nb_defaites']);
        $this->assertEquals(0, $stats['nb_nuls']);
    }

    public function testMatchNul()
    {
        $j = $this->joueur->getOrCreateJoueur('Alessio');
        $this->joueur->updateJoueur($j['Id_Joueur'], 'nul');

        $stats = $this->joueur->getStatsJoueur('Alessio');
        $this->assertEquals(1, $stats['nb_parties']);
        $this->assertEquals(0, $stats['nb_victoires']);
        $this->assertEquals(0, $stats['nb_defaites']);
        $this->assertEquals(1, $stats['nb_nuls']);
    }

    public function testPlusieursParties()
    {
        $j = $this->joueur->getOrCreateJoueur('Grace');
        $id = $j['Id_Joueur'];

        $this->joueur->updateJoueur($id, 'victoire');
        $this->joueur->updateJoueur($id, 'victoire');
        $this->joueur->updateJoueur($id, 'defaite');
        $this->joueur->updateJoueur($id, 'nul');

        $stats = $this->joueur->getStatsJoueur('Grace');
        $this->assertEquals(4, $stats['nb_parties']);
        $this->assertEquals(2, $stats['nb_victoires']);
        $this->assertEquals(1, $stats['nb_defaites']);
        $this->assertEquals(1, $stats['nb_nuls']);
    }
}