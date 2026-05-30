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
    require_once '../controller/jeuControlleur.php';
    $controller = new JeuControlleur();

    // Si on rentre sur cette page sans être passé par le bouton jouer, alors on revient au menu
    if (empty($_POST["pion"]) || empty($_POST["pseudo"]) || empty($_POST["premierJoueur"])) {
        header("Location: menu.php");
        exit();
    }

    // Si on a injecté un pion ou un choix non valide, on revient au menu
    $pionsValides = ["croix", "cercle"];
    $premiersJoueursValides = ["joueur", "ordi", "aleatoire"];
    if (!in_array($_POST["pion"], $pionsValides) || !in_array($_POST["premierJoueur"], $premiersJoueursValides)) {
        header("Location: menu.php");
        exit();
    } else {
        $pionJoueur = $_POST["pion"]; // Sinon, on enregistre le pion choisi par le joueur
    }
    
    $pionOrdi = ($pionJoueur == "croix") ? "cercle" : "croix"; // Choix du pion de l'ordi. On prend l'opposé du joueur
    $tailleGrille = (int) $_POST["grilleChoisie"];
    $grille = array_fill(0, $tailleGrille, array_fill(0, $tailleGrille, null)); // Création d'un tableau vide pour pouvoir afficher la grille du début
    $pseudo = htmlspecialchars($_POST["pseudo"]);
    $premierJoueur = $_POST["premierJoueur"];
    $joueur = $controller->demarrerPartie($pseudo); // On démarre la partie en créant ou récupérant le joueur
    
    // Si c'est une requête AJAX (rejouer), on renvoie juste le HTML de la grille
    if (!empty($_POST['rejouer'])) {
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
        exit();
    }
    // Et on enregistre le résultat de la partie précédente
    if (!empty($_POST['resultat'])) {
        $controller->terminerPartie($pseudo, $_POST['resultat'], $tailleGrille);
        exit();
    }
    ?>

    <!-- On affiche les statistiques du joueur -->
    <div class="stats">
        <p class="stats-titre"><?php echo $pseudo; ?></p>
        <div class="stats-grille">
            <div class="stats-carte victoires">
                <span class="stats-nombre" id="nb-victoires"><?php echo $joueur['nb_victoires']; ?></span>
                <span class="stats-label">Victoires</span>
            </div>
            <div class="stats-carte defaites">
                <span class="stats-nombre" id="nb-defaites"><?php echo $joueur['nb_defaites']; ?></span>
                <span class="stats-label">Défaites</span>
            </div>
            <div class="stats-carte nuls">
                <span class="stats-nombre" id="nb-nuls"><?php echo $joueur['nb_nuls']; ?></span>
                <span class="stats-label">Nuls</span>
            </div>
        </div>
    </div>
    <br>

    <?php
    // Sinon, on affiche un tableau vide de base (quand on joue pour la première fois)
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

    <div id="overlay-fond" class="popup-overlay"></div>
    <div id="pop-up" class="popup-modal">
        <div class="alignPopUp">
            <p id="modal-message"></p>
            <div class="popup-actions">
                <a href="menu.php" class="btn-retour">Retour au menu</a>
                <button type="button" id="btn-rejouer" class="btn-rejouer">Rejouer</button>
            </div>
        </div>
    </div>

    <script>
        // On récupère toutes les informations de la partie dans des variables
        let pseudo = "<?php echo $pseudo; ?>";
        let pionJoueur = "<?php echo $pionJoueur; ?>";
        let pionOrdi = "<?php echo $pionOrdi ?>";
        let grille = <?php echo json_encode($grille) ?>;
        let taille = grille.length;
        let premierJoueur = "<?php echo $premierJoueur; ?>";

        // Fonction pour ouvrir le pop-up
        function ouvrirModal(message) {
            document.getElementById('modal-message').textContent = message;
            document.getElementById('pop-up').style.display = 'flex';
            document.getElementById('overlay-fond').style.display = 'flex';
        }

        // Fonction pour fermer le pop-up
        function fermerModal() {
            document.getElementById('pop-up').style.display = 'none';
            document.getElementById('overlay-fond').style.display = 'none';
        }

        // Fonction qui vérifie si un joueur a gagné
        function verifierResultat(pion) {
            // On verifie dans chaque ligne si tout les pions sont alignés
            for (let ligne = 0; ligne < taille; ligne++) {
                if (grille[ligne].every(col => col === pion)) { return true; }
            }

            // On verifie dans chaque colonne si tout les pions sont alignés
            for (let col = 0; col < taille; col++) {
                if (grille.every(ligne => ligne[col] === pion)) { return true; }
            }

            // On vérifie également les diagonales
            if (grille.every((ligne, i) => ligne[i] === pion)) { return true; }
            if (grille.every((ligne, i) => ligne[taille - 1 - i] === pion)) { return true; }

            // Si aucune condition de victoire n'est remplie
            return false;
        }

        // Tour de l'ordinateur
        function tourOrdi() {
            let casesLibres = [];

            // On récupère toutes les cases libres
            for (let ligne = 0; ligne < taille; ligne++) {
                for (let col = 0; col < taille; col++) {
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

            // Après chaque coup, on vérifie si l'ordinateur a gagné
            let victoire = verifierResultat(pionOrdi);
            if (victoire) {
                sauvegarderPartie("defaite"); // On sauvegarde la partie en précisant la défaite
                ouvrirModal("L'ordinateur a gagné !"); // Si oui, on affiche un message comme quoi l'ordinateur a gagné
            } else {
                // On recalcule les cases libres après le coup de l'ordi
                let casesLibresApres = [];
                for (let i = 0; i < taille; i++) {
                    for (let j = 0; j < taille; j++) {
                        if (grille[i][j] === null) casesLibresApres.push({ i, j });
                    }
                }

                if (casesLibresApres.length === 0) {
                    sauvegarderPartie("nul"); // On sauvegarde la partie en précisant le match nul
                    ouvrirModal("Match nul ! Voulez-vous rejouer ?");
                }
            }
        }

        // Tour du joueur
        function tourJoueur() {
            document.querySelectorAll(".caseLibre").forEach(function (bouton) {
                bouton.addEventListener("click", function () {
                    let td = this.closest("td");
                    let ligne = parseInt(td.dataset.row);
                    let col = parseInt(td.dataset.col);
                    let casesLibres = [];

                    // On place le pion du joueur dans le tableau
                    grille[ligne][col] = pionJoueur;

                    // On met à jour l'affichage
                    td.innerHTML = `<img src="../assets/${pionJoueur}.png" alt="${pionJoueur}">`;

                    // Après chaque coup, on vérifie si le joueur a gagné
                    let victoire = verifierResultat(pionJoueur);
                    if (victoire) {
                        sauvegarderPartie("victoire"); // On sauvegarde la partie en précisant la victoire
                        ouvrirModal("Vous avez gagné !");
                    } else {
                        for (let i = 0; i < taille; i++) {
                            for (let j = 0; j < taille; j++) {
                                if (grille[i][j] === null) casesLibres.push({ i, j });
                            }
                        }

                        if (casesLibres.length === 0) {
                            sauvegarderPartie("nul"); // On sauvegarde la partie en précisant le match nul
                            ouvrirModal("Match nul ! Voulez-vous rejouer ?");
                        } else {
                            tourOrdi();
                        }
                    }
                });
            });
        }

        function getXMLHttpRequest() {
            var xhr = null;

            if (window.XMLHttpRequest || window.ActiveXObject) {
                if (window.ActiveXObject) {
                    try {
                        xhr = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch (e) {
                        xhr = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                } else {
                    xhr = new XMLHttpRequest();
                }
            } else {
                alert("Browser doesn't support XMLHTTPRequest...");
                return null;
            }

            return xhr;
        }

        // Quand on clique sur le bouton "rejouer" en AJAX
        document.getElementById('btn-rejouer').addEventListener('click', function () {
            var xhr = getXMLHttpRequest();
            xhr.open("POST", "partie.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("pion=" + pionJoueur + "&pseudo=<?php echo $pseudo; ?>&grilleChoisie=" + taille + "&rejouer=1&premierJoueur=" + premierJoueur);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.querySelector('.grille').outerHTML = xhr.responseText;
                    grille = <?php echo json_encode($grille) ?>;
                    fermerModal();
                    tourJoueur();
                }
            };
        });

        // On sauvegarde la partie en DB et on met à jour les stats affichées
        function sauvegarderPartie(resultat) {
            var xhr = getXMLHttpRequest();
            xhr.open("POST", "partie.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("pion=" + pionJoueur + "&pseudo=" + pseudo + "&grilleChoisie=" + taille + "&resultat=" + resultat + "&premierJoueur=" + premierJoueur);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // On met à jour les stats affichées
                    if (resultat === 'victoire') {
                        document.getElementById('nb-victoires').textContent++;
                    } else if (resultat === 'defaite') {
                        document.getElementById('nb-defaites').textContent++;
                    } else {
                        document.getElementById('nb-nuls').textContent++;
                    }
                }
            };
        }

        if (premierJoueur === "aleatoire") {
            random = Math.random();
            premierJoueur = random < 0.5 ? "joueur" : "ordi";
        }
        if (premierJoueur === "ordi") {
            // Si l'ordinateur commence, il joue son coup immédiatement
            tourOrdi();
            // Puis, c'est au joueur de jouer
            tourJoueur();
        } else {
            // Sinon, c'est au joueur de jouer
            tourJoueur();
        }
    </script>
</body>

</html>