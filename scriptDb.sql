CREATE DATABASE IF NOT EXISTS tictactoe;
USE tictactoe;

CREATE TABLE Joueur (
    Id_Joueur   INT          NOT NULL AUTO_INCREMENT,
    pseudo      VARCHAR(50)  NOT NULL,
    nb_parties  INT          NOT NULL DEFAULT 0,
    nb_victoires INT         NOT NULL DEFAULT 0,
    nb_defaites INT          NOT NULL DEFAULT 0,
    nb_nuls     INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (Id_Joueur),
    UNIQUE (pseudo)
);

CREATE TABLE Partie (
    Id_Partie    INT  NOT NULL AUTO_INCREMENT,
    resultat     INT  NOT NULL,
    taille_grille INT NOT NULL,
    Id_Joueur    INT  NOT NULL,
    PRIMARY KEY (Id_Partie),
    FOREIGN KEY (Id_Joueur) REFERENCES Joueur(Id_Joueur)
);