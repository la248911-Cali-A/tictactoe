<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Menu Principal - TicTacToe</title>
</head>

<body>
    <h1>TicTacToe</h1>
    <form class="menuPrincipal" action="partie.php" method="post">
        <label class="menuPrincipal-pseudo">Votre pseudo</label> <br>
        <input class="menuPrincpal-texte" type="text" name="pseudo" placeholder="Entrez votre pseudo">

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

        <br>
        <button type="submit" class="btn-jouer">JOUER</button>
    </form>
</body>

</html>