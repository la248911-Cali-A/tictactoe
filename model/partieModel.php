<?php
require_once 'dbConnection.php';

class Partie
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->connect();
    }

    public function savePartie(int $idJoueur, string $resultat, int $tailleGrille)
    {
        try {
            switch ($resultat) {
                case 'victoire':
                    $resultatConverti = 1;
                    break;
                case 'defaite':
                    $resultatConverti = 2;
                    break;
                default:
                    $resultatConverti = 3;
                    break;
            }

            $sql = "INSERT INTO Partie (resultat, taille_grille, Id_Joueur) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$resultatConverti, $tailleGrille, $idJoueur]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Une erreur est survenue");
        }
    }

    public function getClassement(int $tailleGrille)
    {
        try {
            $sql = "SELECT j.pseudo, COUNT(*) as nb_victoires
                    FROM Partie p
                    JOIN Joueur j ON p.Id_Joueur = j.Id_Joueur
                    WHERE taille_grille = ? AND resultat = 1
                    GROUP BY j.pseudo
                    ORDER BY nb_victoires DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$tailleGrille]);
            $resultat = $stmt->fetchAll();
            return $resultat;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Une erreur est survenue");
        }
    }
}