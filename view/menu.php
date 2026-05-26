<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Menu Principal - TicTacToe</title>
</head>

<body>
    <?php
    require_once '../controller/jeuControlleur.php';
    $controller = new JeuControlleur();
    $tailles = [3, 4]; // Si on veut ajouter un nouveau mode (ex: 5x5), on a juste a ajouté 5 dans ce tableau
    $classements = [];
    foreach ($tailles as $taille) {
        $classements[$taille] = $controller->getClassement($taille);
    }
    ?>

    <h1>TicTacToe</h1>
    <div class="pageMenu">
        <form class="menuPrincipal" action="partie.php" method="post">
            <label class="menuPrincipal-pseudo">Votre pseudo</label> <br>
            <input class="menuPrincpal-texte" type="text" name="pseudo" placeholder="Entrez votre pseudo" required>

            <p>Choisissez votre pion</p>
            <div class="pions">
                <label class="pion-label">
                    <input type="radio" name="pion" value="croix" checked>
                    <img src="../assets/croix.png" alt="Croix">
                </label>
                <label class="pion-label">
                    <input type="radio" name="pion" value="cercle">
                    <img src="../assets/cercle.png" alt="Cercle">
                </label>
            </div>
            <p>Choisissez votre grille</p>
            <div class="pions">
                <label class="pion-label">
                    <input type="radio" name="grilleChoisie" value="3" checked>
                    <img src="../assets/grilleTrois.png" alt="Trois">
                </label>
                <label class="pion-label">
                    <input type="radio" name="grilleChoisie" value="4">
                    <img src="../assets/grilleQuatre.png" alt="Quatre">
                </label>
            </div>

            <p>Qui commence à jouer ?</p>
            <div class="pions">
                <label class="pion-label">
                    <input type="radio" name="premierJoueur" value="joueur" checked>
                    <img src="../assets/person-fill.svg" alt="Moi">
                </label>
                <label class="pion-label">
                    <input type="radio" name="premierJoueur" value="ordi">
                    <img src="../assets/robot.svg" alt="Ordi">
                </label>
                <label class="pion-label">
                    <input type="radio" name="premierJoueur" value="aleatoire">
                    <img src="../assets/shuffle.svg" alt="Random">
                </label>
            </div>

            <br>
            <button type="submit" class="btn-jouer">JOUER</button>
        </form>

        <?php
        foreach ($classements as $taille => $classement) {
            echo "<table class='classement-table'>";
            echo "<caption>Top 5 des parties en " . $taille . "x" . $taille . "</caption>";
            echo "<tr><th>Pseudo</th><th>Nombre de victoires</th></tr>";
            foreach ($classement as $joueur) {
                echo "<tr>";
                echo "<td>" . $joueur['pseudo'] . "</td>";
                echo "<td>" . $joueur['nb_victoires'] . "</td>";
                echo "</tr>";
            }
            if (!$classement) {
                echo "<tr><td colspan=2>Pas de résultats disponibles</td></tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
</body>

</html>