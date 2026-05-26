<?php
require_once 'dbConnection.php';

class Joueur
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->connect();
    }

    public function getOrCreateJoueur(string $pseudo)
    {
        try {
            $sql = "SELECT * FROM Joueur WHERE pseudo = ?"; // On recherche le joueur en base de données
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$pseudo]);
            $joueur = $stmt->fetch(); // S'il existe, on l'enregistre avec ses données

            if (!$joueur) { // Si pas, on le crée
                $sql = " INSERT INTO Joueur (pseudo, nb_parties, nb_victoires, nb_defaites, nb_nuls) VALUES (?, 0, 0, 0, 0) ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$pseudo]);
                // On a inséré un nouveau joueur et on récupère ses informations
                $joueur = [
                    'Id_Joueur' => $this->pdo->lastInsertId(),
                    'pseudo' => $pseudo,
                    'nb_parties' => 0,
                    'nb_victoires' => 0,
                    'nb_defaites' => 0,
                    'nb_nuls' => 0,
                ];
            }

            return $joueur;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Une erreur est survenue");
        }
    }

    public function getStatsJoueur(string $pseudo)
    {
        try {
            $sql = "SELECT * FROM Joueur WHERE pseudo = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$pseudo]);
            $resultat = $stmt->fetch();
            return $resultat;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Une erreur est survenue");
        }
    }

    public function updateJoueur(int $id, string $resultat)
    {
        try {
            // On regarde quel résultat on a obtenu.
            // Puis, on enregistre la colonne à modifier
            switch ($resultat) {
                case "victoire":
                    $colonne = "nb_victoires";
                    break;
                case "defaite":
                    $colonne = "nb_defaites";
                    break;
                default:
                    $colonne = "nb_nuls";
                    break;
            }

            // On ajoute le résultat à la page du joueur
            $sql = "UPDATE Joueur SET nb_parties = nb_parties + 1, $colonne = $colonne + 1 WHERE Id_Joueur = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Une erreur est survenue");
        }
    }
}