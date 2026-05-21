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
    if (empty($_POST["pion"])) {
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

    document.querySelectorAll(".caseLibre").forEach(function (bouton) {
        bouton.addEventListener("click", function () {
            let td = this.closest("td");
            let ligne = td.dataset.row;
            let col = td.dataset.col;

            // On place le pion du joueur dans le tableau
            grille[ligne][col] = pionJoueur;

            // On met à jour l'affichage
            td.innerHTML = `<img src="../assets/${pionJoueur}.png" alt="${pionJoueur}">`;
        });
    });
</script>

</html>