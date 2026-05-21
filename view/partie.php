<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Partie en cours - TicTacToe</title>
</head>

<body>
    <?php
    if (empty($_POST["pion"])) {
        header("Location: menu.php");
        exit();
    } else {
        $pionJoueur = $_POST["pion"];
    }
    $pionOrdi = ($pionJoueur == "croix") ? "cercle" : "croix";
    $grille = array_fill(0, 4, array_fill(0, 4, null));

    echo "<table class='grille'>";
    foreach ($grille as $ligne => $colonnes) {
        echo '<tr>';
        foreach ($colonnes as $col => $entree) {
            if ($entree === "croix") {
                $affichage = '<img src="../assets/croix.png" alt="X">';
            } elseif ($entree === "cercle") {
                $affichage = '<img src="../assets/cercle.png" alt="O">';
            } else {
                $affichage = '';
            }
            echo "<td data-row=\"{$ligne}\" data-col=\"{$col}\">$affichage</td>";
        }
        echo '</tr>';
    }
    echo '</table>';

    ?>

</body>

</html>