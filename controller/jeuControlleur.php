<?php
require_once __DIR__ . '/../model/joueurModel.php';
require_once __DIR__ . '/../model/partieModel.php';

class JeuControlleur
{
    private Joueur $joueurModel;
    private Partie $partieModel;

    public function __construct()
    {
        $this->joueurModel = new Joueur();
        $this->partieModel = new Partie();
    }

    // Appelé au début de la partie pour récupérer ou créer le joueur
    public function demarrerPartie(string $pseudo)
    {
        return $this->joueurModel->getOrCreateJoueur($pseudo);
    }

    // Appelé à la fin de la partie pour sauvegarder et mettre à jour les stats
    public function terminerPartie(string $pseudo, string $resultat, int $tailleGrille)
    {
        $joueur = $this->joueurModel->getOrCreateJoueur($pseudo);
        $id = $joueur['Id_Joueur'];
        $this->partieModel->savePartie($id, $resultat, $tailleGrille);
        $this->joueurModel->updateJoueur($id, $resultat);
    }

    // Appelé pour afficher le classement général des meilleurs joueurs
    public function getClassement(int $tailleGrille)
    {
        return $this->partieModel->getClassement($tailleGrille);
    }

    // Appelé pour afficher nos propres statistiques pendant la partie
    public function getStatsJoueur(string $pseudo)
    {
        return $this->joueurModel->getStatsJoueur($pseudo);
    }
}