USE tceron;


DROP TABLE IF EXISTS Carrello;
DROP TABLE IF EXISTS Preferiti;
DROP TABLE IF EXISTS Vendita;
DROP TABLE IF EXISTS Consumo;
DROP TABLE IF EXISTS Utente;
DROP TABLE IF EXISTS Servizi;
DROP TABLE IF EXISTS March_Bevande;
DROP TABLE IF EXISTS Bundle;
DROP TABLE IF EXISTS Bevande;
DROP TABLE IF EXISTS Prodotti;
DROP TABLE IF EXISTS Insegnamento;
DROP TABLE IF EXISTS Laurea;
DROP TABLE IF EXISTS Università;

-- TABELLE

CREATE TABLE Università (
                            nome VARCHAR(255) NOT NULL,
                            città VARCHAR(255) NOT NULL,
                            PRIMARY KEY (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Laurea (
                        codice_unico CHAR(36) NOT NULL DEFAULT (UUID()),
                        tipologia ENUM('Triennale', 'Magistrale', 'Dottorato di Ricerca') NOT NULL,
                        universita VARCHAR(255) NOT NULL,
                        nome_laurea VARCHAR(255) NOT NULL,
                        cod_ministero VARCHAR(255),
                        PRIMARY KEY (codice_unico),
                        FOREIGN KEY (universita) REFERENCES Università(nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Insegnamento (
                              codice CHAR(36) NOT NULL DEFAULT (UUID()),
                              docente VARCHAR(255) NOT NULL,
                              laurea CHAR(36) NOT NULL UNIQUE,
                              nome VARCHAR(255),
                              cfu SMALLINT,
                              PRIMARY KEY (codice),
                              FOREIGN KEY (laurea) REFERENCES Laurea(codice_unico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Prodotti (
                          id CHAR(36) NOT NULL DEFAULT (UUID()),
                          disponibilità BIGINT NOT NULL,
                          nome VARCHAR(255) NOT NULL,
                          prezzo FLOAT NOT NULL,
                          descrizione TEXT NOT NULL,
                          link_modello TEXT,
                          PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Bevande (
                         id CHAR(36) NOT NULL,
                         temp_consigliata SMALLINT NOT NULL,
                         tipologia_bevanda ENUM('Tè', 'Cioccolato', 'Infuso') NOT NULL,
                         scoop VARCHAR(255) NOT NULL UNIQUE,
                         PRIMARY KEY (id),
                         FOREIGN KEY (id) REFERENCES Prodotti(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Bundle (
                        id_bundle CHAR(36) NOT NULL,
                        contenuto CHAR(36) NOT NULL,
                        precent_sconto SMALLINT NOT NULL,
                        PRIMARY KEY (id_bundle, contenuto),
                        FOREIGN KEY (id_bundle) REFERENCES Prodotti(id),
                        FOREIGN KEY (contenuto) REFERENCES Prodotti(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE March_Bevande (
                               id CHAR(36) NOT NULL,
                               id_bevanda CHAR(36) NOT NULL,
                               tipologia_march ENUM('Accessori', 'Abbigliamento', 'Prodotti per la casa') NOT NULL,
                               Materiale VARCHAR(255) NOT NULL,
                               PRIMARY KEY (id),
                               FOREIGN KEY (id_bevanda) REFERENCES Bevande(id),
                               FOREIGN KEY (id) REFERENCES Prodotti(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Servizi (
                         id CHAR(36) NOT NULL,
                         tipologia_servizi ENUM(
        'Fornitura appunti',
        'Decifrazione scrittura del professore',
        'Assistenza a progetto',
        'Ripetizioni',
        'Preparazione all"esame',
        'Sbobine di lezione',
        'Prestito libri di corso'
    ) NOT NULL,
                         livello_urgenza ENUM('Molto basso', 'Basso', 'Medio', 'Alto', 'Molto alto') NOT NULL,
                         insegnamento CHAR(36),
                         PRIMARY KEY (id),
                         FOREIGN KEY (id) REFERENCES Prodotti(id),
                         FOREIGN KEY (insegnamento) REFERENCES Insegnamento(codice)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Utente (
                        email VARCHAR(255) NOT NULL,
                        indirizzo TEXT NOT NULL,
                        username VARCHAR(255) NOT NULL UNIQUE ,
                        telefono DECIMAL(15,0) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        punti_fedelta SMALLINT,
                        tipo_utente ENUM('Compratore', 'Venditore') NOT NULL,
                        PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Consumo (
                         consumatore VARCHAR(255) NOT NULL,
                         prodotto CHAR(36) NOT NULL,
                         quantità SMALLINT NOT NULL,
                         stato_transazione ENUM('In Progress', 'Approvata', 'Non approvata') NOT NULL,
                         data_ora DATETIME NOT NULL,
                         PRIMARY KEY (consumatore, prodotto, data_ora),
                         FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                         FOREIGN KEY (consumatore) REFERENCES Utente(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Vendita (
                         venditore VARCHAR(255) NOT NULL,
                         prodotto CHAR(36) NOT NULL,
                         quantita SMALLINT NOT NULL,
                         PRIMARY KEY (venditore, prodotto),
                         FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                         FOREIGN KEY (venditore) REFERENCES Utente(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Carrello (
                          consumatore VARCHAR(255) NOT NULL,
                          prodotto CHAR(36) NOT NULL,
                          quantità SMALLINT NOT NULL,
                          PRIMARY KEY (consumatore, prodotto),
                          FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                          FOREIGN KEY (consumatore) REFERENCES Utente(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Preferiti (
                           consumatore VARCHAR(255) NOT NULL,
                           prodotto CHAR(36) NOT NULL,
                           quantità SMALLINT NOT NULL,
                           PRIMARY KEY (consumatore, prodotto),
                           FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                           FOREIGN KEY (consumatore) REFERENCES Utente(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;