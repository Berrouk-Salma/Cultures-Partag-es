-- CREATION DE LA BASE DE DONNEES culture_connect
CREATE DATABASE art_culture_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;; 

USE culture_connect;

-- CREATION DES TABLES
CREATE TABLE users( 
    id_user INT AUTO_INCREMENT PRIMARY KEY, 
    prenom VARCHAR(50) NOT NULL, 
    nom VARCHAR(50) NOT NULL, 
    telephone VARCHAR(20) NOT NULL, 
    email VARCHAR(100) NOT NULL UNIQUE, 
    password VARCHAR(100) NOT NULL,
    role ENUM('Utilisateur','Auteur','Admin') NOT NULL,
    photo VARCHAR(255) NOT NULL,
    isBanned BOOLEAN DEFAULT 0
);

CREATE TABLE categorie( 
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    nom_categorie VARCHAR(50) NOT NULL UNIQUE, 
    description TEXT, 
    date_creation DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_admin) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE articles ( 
    id_article INT AUTO_INCREMENT PRIMARY KEY, 
    titre VARCHAR(100) NOT NULL, 
    contenu TEXT NOT NULL, 
    couverture VARCHAR(255) NOT NULL,
    date_publication DATE NOT NULL DEFAULT CURRENT_DATE,
    statut ENUM('Accepté','En Attente','Refusé') NOT NULL DEFAULT 'En Attente',
    id_categorie INT NOT NULL, 
    id_auteur INT NOT NULL, 
    FOREIGN KEY (id_categorie) REFERENCES categorie(id_categorie) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (id_auteur) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE commentaires( 
    id_comment INT AUTO_INCREMENT PRIMARY KEY,
    contenu VARCHAR(255) NOT NULL,
    date_soumission DATE DEFAULT CURRENT_DATE,
    id_article INT NOT NULL,
    id_utilisateur INT NOT NULL,
    FOREIGN KEY (id_article) REFERENCES article(id_article) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (id_utilisateur) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tags( 
    id_tag INT AUTO_INCREMENT PRIMARY KEY,
    nom_tag VARCHAR(20) NOT NULL,
    id_admin INT NOT NULL,
    FOREIGN KEY (id_admin) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE favoris( 
    id_article INT NOT NULL,
    id_utilisateur INT NOT NULL,
    PRIMARY KEY (id_article,id_utilisateur),
    FOREIGN KEY (id_utilisateur) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_article) REFERENCES article(id_article) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE article_tag( 
    id_tag INT NOT NULL,
    id_article INT NOT NULL,
    PRIMARY KEY (id_tag,id_article),
    FOREIGN KEY (id_tag) REFERENCES tags(id_tag) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_article) REFERENCES article(id_article) ON DELETE CASCADE ON UPDATE CASCADE
);