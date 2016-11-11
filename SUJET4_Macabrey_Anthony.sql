DROP TABLE IF EXISTS club;
CREATE TABLE club (
    idClub INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nomClub VARCHAR (20),
    villeClub VARCHAR (20)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO club (idClub, nomClub, villeClub) VALUES (NULL, 'Paris SG', 'Paris');
INSERT INTO club (idClub, nomClub, villeClub) VALUES (NULL, 'Chelsea', 'Londres');
INSERT INTO club (idClub, nomClub, villeClub) VALUES (NULL, 'FC Barcelona', 'Barcelone');

DROP TABLE IF EXISTS joueur;
CREATE TABLE joueur (
    idJoueur INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nomJoueur VARCHAR (20),
    prenomJoueur VARCHAR (20),
    dateNaissance DATE,
    idClub INT (10),
    CONSTRAINT fk_joueur_club FOREIGN KEY (idClub) REFERENCES club(idClub)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO joueur  VALUES (1, 'Hazard', 'Eden', '1991-01-07', 2);
INSERT INTO joueur  VALUES (2, 'Messi', 'Lionel', '1987-06-24', 3);
INSERT INTO joueur  VALUES (3, 'Matuidi', 'Blaise', '1987-04-09', 1);
INSERT INTO joueur  VALUES (4, 'Fabregas', 'Cesc', '1987-05-04', 2);
INSERT INTO joueur  VALUES (5, 'Courtois', 'Thibaut', '1992-05-11', 3);
INSERT INTO joueur  VALUES (6, 'Pique', 'Gerard', '1987-02-02', 1);
INSERT INTO joueur  VALUES (7, 'Terry', 'John', '1980-12-07', 2);
INSERT INTO joueur  VALUES (8, 'Ibrahimovic', 'Zlatan', '1981-10-03', 1);
INSERT INTO joueur  VALUES (9, 'Cahill', 'Gary', '1985-12-19', 2);
INSERT INTO joueur  VALUES (10, 'Rakitic', 'Ivan', '1988-04-10', 3);
INSERT INTO joueur  VALUES (11, 'Digne', 'Lucas', '1993-01-20', 1);
INSERT INTO joueur  VALUES (12, 'Suarez', 'Luis', '1987-01-24', 3);

DROP TABLE IF EXISTS utilisateurs;
CREATE TABLE utilisateurs (
  id int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  login varchar(20) DEFAULT NULL,
  motdepasse varchar(20) DEFAULT NULL,
  email varchar(50),
  droit varchar(20) DEFAULT NULL
);

INSERT INTO utilisateurs (id,login,motdepasse,email,droit) VALUES
(1, 'admin', 'admin', 'admin@gmail.com','DROITadmin'),
(2, 'invite', 'invite', 'admin@gmail.com','DROITinvite'),
(3, 'vendeur', 'vendeur', 'vendeur@gmail.com','DROITadmin'),
(4, 'client', 'client', 'client@gmail.com','DROITclient'),
(5, 'client2', 'client2', 'client2@gmail.com','DROITclient');