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
    $pseudo = $_POST["pseudo"];

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
                <a href="menu.php" class="pop-up-annuler">Retour au menu</a>
                <button type="button" id="btn-rejouer" class="pop-up-confirmer">Rejouer</button>
            </div>
        </div>
    </div>
</body>

<script>
    let pionJoueur = "<?php echo $pionJoueur; ?>";
    let pionOrdi = "<?php echo $pionOrdi ?>";
    let grille = <?php echo json_encode($grille) ?>;

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
        // On verifie dans chaque ligne si 4 pions sont alignés
        for (let ligne = 0; ligne < 4; ligne++) {
            if (grille[ligne][0] === pion && grille[ligne][1] === pion && grille[ligne][2] === pion && grille[ligne][3] === pion) {
                return true;
            }
        }

        // On vérifie dans chaque colonne si 4 pions sont alignés
        for (let col = 0; col < 4; col++) {
            if (grille[0][col] === pion && grille[1][col] === pion && grille[2][col] === pion && grille[3][col] === pion) {
                return true;
            }
        }
    }

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

        // Après chaque coup, on vérifie si l'ordinateur a gagné
        let victoire = verifierResultat(pionOrdi);
        if (victoire) {
            ouvrirModal("L'ordinateur a gagné !"); // Si oui, on affiche un message comme quoi l'ordinateur a gagné
        }
    }

    // Tour du joueur
    function tourJoueur() {
        document.querySelectorAll(".caseLibre").forEach(function (bouton) {
            bouton.addEventListener("click", function () {
                let td = this.closest("td");
                let ligne = parseInt(td.dataset.row);
                let col = parseInt(td.dataset.col);
 
                // On place le pion du joueur dans le tableau
                grille[ligne][col] = pionJoueur;
 
                // On met à jour l'affichage
                td.innerHTML = `<img src="../assets/${pionJoueur}.png" alt="${pionJoueur}">`;

                // Après chaque coup, on vérifie si le joueur a gagné
                let victoire = verifierResultat(pionJoueur);
                if (victoire) {
                    ouvrirModal("Vous avez gagné !");
                } else {
                    tourOrdi();
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
        xhr.send("pion=" + pionJoueur + "&pseudo=<?php echo $pseudo; ?>&rejouer=1");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.querySelector('.grille').outerHTML = xhr.responseText;
                grille = <?php echo json_encode($grille) ?>;
                fermerModal();
                tourJoueur();
            }
        };
    });

    // Au premier chargement de la partie, c'est au joueur de commencer
    tourJoueur();
</script>

</html>