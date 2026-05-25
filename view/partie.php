<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Partie - TicTacToe</title>
</head>

<body>
    <?php
    // Si on rentre sur cette page sans être passé par le bouton jouer, alors on revient au menu
    if (empty($_POST["pion"]) || empty($_POST["pseudo"])) {
        header("Location: menu.php");
        exit();
    } else {
        $pionJoueur = $_POST["pion"]; // Sinon, on enregistre le pion choisi par le joueur
    }
    $pionOrdi = ($pionJoueur == "croix") ? "cercle" : "croix"; // Choix du pion de l'ordi. On prend l'opposé du joueur
    $grille = array_fill(0, 4, array_fill(0, 4, null)); // Création d'un tableau vide pour pouvoir afficher la grille du début
    
    // Affichage du tableau vide de base
    echo "<table class='grille'>";
    foreach ($grille as $ligne => $colonnes) {
        echo '<tr>';
        foreach ($colonnes as $col => $entree) {
            $case = '<button class="caseLibre"></button>';
            echo "<td data-row=\"$ligne\" data-col=\"$col\">$case</td>";
        }
        echo '</tr>';
    }
    echo '</table>';
    ?>
</body>

<script>
    let pionJoueur = "<?php echo $pionJoueur; ?>";
    let pionOrdi = "<?php echo $pionOrdi ?>";
    let grille = <?php echo json_encode($grille) ?>;

    // Tour de l'ordinateur
    function tourOrdi() {
        let casesLibres = [];

        // On récupère toutes les cases libres
        for (let ligne = 0; ligne < 4; ligne++) {
            for (let col = 0; col < 4; col++) {
                if (grille[ligne][col] === null) {
                    casesLibres.push({ ligne, col });
                }
            }
        }

        // On choisit une case au hasard
        let caseRandom = casesLibres[Math.floor(Math.random() * casesLibres.length)];
        grille[caseRandom.ligne][caseRandom.col] = pionOrdi;

        // On met à jour l'affichage
        let td = document.querySelector(`td[data-row="${caseRandom.ligne}"][data-col="${caseRandom.col}"]`);
        td.innerHTML = `<img src="../assets/${pionOrdi}.png" alt="${pionOrdi}">`;
    }

    // Tour du joueur
    document.querySelectorAll(".caseLibre").forEach(function (bouton) {
        bouton.addEventListener("click", function () {
            let td = this.closest("td");
            let ligne = td.dataset.row;
            let col = td.dataset.col;

            // On place le pion du joueur dans le tableau
            grille[ligne][col] = pionJoueur;

            // On met à jour l'affichage
            td.innerHTML = `<img src="../assets/${pionJoueur}.png" alt="${pionJoueur}">`;

            // L'ordi joue après le joueur
            tourOrdi();
        });
    });
</script>

</html>