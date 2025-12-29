/*---------------------------------------------------- TIPI PER TABELLE -------------------------------------------------------*/
drop type IF exists livello_urgenza CASCADE;
create type livello_urgenza as ENUM(
  'Molto basso',
  'Basso',
  'Medio',
  'Alto',
  'Molto alto'
);

drop type IF exists tipo_utente CASCADE;
create type tipo_utente as ENUM(
  'Compratore',
  'Venditore'
);

drop type IF exists tipologia_servizi CASCADE;
create type tipologia_servizi as ENUM(
  'Fornitura appunti',
  'Decifrazione scrittura del professore',
  'Assistenza a progetto',
  'Ripetizioni',
  'Preparazione all"esame',
  'Sbobine di lezione',
  'Prestito libri di corso'
);

DROP TYPE  IF EXISTS tipologia_laurea CASCADE;
CREATE TYPE tipologia_laurea AS ENUM (
   'Triennale',
   'Magistrale',
   'Dottorato di Ricerca'
);

DROP TYPE  IF EXISTS tipologia_bevanda CASCADE;
CREATE TYPE tipologia_bevanda AS ENUM (
   'Tè',
   'Cioccolato',
   'Infuso'
);

DROP TYPE  IF EXISTS stato_transazione CASCADE;
CREATE TYPE stato_transazione AS ENUM (
   'In Progress',
   'Approvata',
   'Non approvata'
);

DROP TYPE  IF EXISTS tipologia_march CASCADE;
CREATE TYPE tipologia_march AS ENUM (
   'Accessori',
   'Abbigliamento',
   'Prodotti per la casa'
);

/*------------------------------------------------------ TABELLE ------------------------------------------------------------*/
DROP TABLE  IF EXISTS Università CASCADE;
CREATE TABLE Università (
                            nome text NOT NULL,
                            città text NOT NULL,
                            CONSTRAINT Università_pkey PRIMARY KEY (nome)
);

DROP TABLE  IF EXISTS Laurea CASCADE;
CREATE TABLE Laurea (
                        codice_unico uuid NOT NULL DEFAULT gen_random_uuid(),
                        tipologia tipologia_laurea NOT NULL,
                        universita text NOT NULL,
                        nome_laurea text NOT NULL,
                        cod_ministero character varying,
                        CONSTRAINT Laurea_pkey PRIMARY KEY (codice_unico),
                        CONSTRAINT Laurea_universita_fkey FOREIGN KEY (universita) REFERENCES Università(nome)
);

DROP TABLE  IF EXISTS Insegnamento CASCADE;
CREATE TABLE Insegnamento (
                              codice uuid NOT NULL DEFAULT gen_random_uuid(),
                              docente text NOT NULL,
                              laurea uuid NOT NULL DEFAULT gen_random_uuid() UNIQUE,
                              nome text,
                              cfu smallint,
                              CONSTRAINT Insegnamento_pkey PRIMARY KEY (codice),
                              CONSTRAINT Insegnamento_laurea_fkey FOREIGN KEY (laurea) REFERENCES Laurea(codice_unico)
);

DROP TABLE  IF EXISTS Prodotti CASCADE;
CREATE TABLE Prodotti (
                          id uuid NOT NULL DEFAULT gen_random_uuid(),
                          disponibilità bigint NOT NULL,
                          nome text NOT NULL,
                          prezzo real NOT NULL,
                          descrizione character varying NOT NULL,
                          link_modello text,
                          CONSTRAINT Prodotti_pkey PRIMARY KEY (id)
);

DROP TABLE  IF EXISTS Bevande CASCADE;
CREATE TABLE Bevande (
                         id uuid NOT NULL DEFAULT gen_random_uuid(),
                         temp_consigliata smallint NOT NULL,
                         tipologia_bevanda tipologia_bevanda NOT NULL,
                         scoop text NOT NULL UNIQUE,
                         CONSTRAINT Bevande_pkey PRIMARY KEY (id),
                         CONSTRAINT Bevande_id_fkey FOREIGN KEY (id) REFERENCES Prodotti(id)
);

DROP TABLE  IF EXISTS Bundle CASCADE;
CREATE TABLE Bundle (
                        id_bundle uuid NOT NULL DEFAULT gen_random_uuid(),
                        contenuto uuid NOT NULL DEFAULT gen_random_uuid(),
                        precent_sconto smallint NOT NULL,
                        CONSTRAINT Bundle_pkey PRIMARY KEY (id_bundle, contenuto),
                        CONSTRAINT Bundle_id_bundle_fkey FOREIGN KEY (id_bundle) REFERENCES Prodotti(id),
                        CONSTRAINT Bundle_contenuto_fkey FOREIGN KEY (contenuto) REFERENCES Prodotti(id)
);

DROP TABLE  IF EXISTS March_Bevande CASCADE;
CREATE TABLE March_Bevande (
                               id uuid NOT NULL DEFAULT gen_random_uuid(),
                               id_bevanda uuid NOT NULL DEFAULT gen_random_uuid(),
                               tipologia_march tipologia_march NOT NULL,
                               Materiale text NOT NULL,
                               CONSTRAINT March_Bevande_pkey PRIMARY KEY (id),
                               CONSTRAINT March_Bevande_id_bevanda_fkey FOREIGN KEY (id_bevanda) REFERENCES Bevande(id),
                               CONSTRAINT March_Bevande_id_fkey FOREIGN KEY (id) REFERENCES Prodotti(id)
);

DROP TABLE  IF EXISTS Servizi CASCADE;
CREATE TABLE Servizi (
                         id uuid NOT NULL DEFAULT gen_random_uuid(),
                         tipologia_servizi tipologia_servizi NOT NULL,
                         livello_urgenza livello_urgenza NOT NULL,
                         insegnamento uuid DEFAULT gen_random_uuid(),
                         CONSTRAINT Servizi_pkey PRIMARY KEY (id),
                         CONSTRAINT Servizi_id_fkey FOREIGN KEY (id) REFERENCES Prodotti(id),
                         CONSTRAINT Servizi_insegnamento_fkey FOREIGN KEY (insegnamento) REFERENCES Insegnamento(codice)
);

DROP TABLE  IF EXISTS Utente CASCADE;
CREATE TABLE Utente (
                        email text NOT NULL,
                        indirizzo text NOT NULL,
                        username text NOT NULL,
                        telefono numeric NOT NULL UNIQUE,
                        password text NOT NULL,
                        punti_fedelta smallint,
                        tipo_utente tipo_utente NOT NULL,
                        CONSTRAINT Utente_pkey PRIMARY KEY (email)
);

DROP TABLE  IF EXISTS Consumo CASCADE;
CREATE TABLE Consumo (
                         consumatore text NOT NULL,
                         prodotto uuid NOT NULL DEFAULT gen_random_uuid(),
                         quantità smallint NOT NULL,
                         stato_transazione stato_transazione NOT NULL,
                         data_ora timestamp without time zone NOT NULL,
                         CONSTRAINT Consumo_pkey PRIMARY KEY (consumatore, prodotto, data_ora),
                         CONSTRAINT Consumo_prodotto_fkey FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                         CONSTRAINT Consumo_consumatore_fkey FOREIGN KEY (consumatore) REFERENCES Utente(email)
);

DROP TABLE  IF EXISTS Vendita CASCADE;
CREATE TABLE Vendita (
                         venditore text NOT NULL,
                         prodotto uuid NOT NULL DEFAULT gen_random_uuid(),
                         quantita smallint NOT NULL,
                         CONSTRAINT Vendita_pkey PRIMARY KEY (venditore, prodotto),
                         CONSTRAINT Vendita_prodotto_fkey FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                         CONSTRAINT Vendita_venditore_fkey FOREIGN KEY (venditore) REFERENCES Utente(email)
);

DROP TABLE  IF EXISTS Carrello CASCADE;
CREATE TABLE Carrello (
                          consumatore text NOT NULL,
                          prodotto uuid NOT NULL DEFAULT gen_random_uuid(),
                          quantità smallint NOT NULL,
                          CONSTRAINT Carrello_pkey PRIMARY KEY (consumatore, prodotto),
                          CONSTRAINT Carrello_prodotto_fkey FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                          CONSTRAINT Carrello_consumatore_fkey FOREIGN KEY (consumatore) REFERENCES Utente(email)
);

DROP TABLE  IF EXISTS Preferiti CASCADE;
CREATE TABLE Preferiti (
                           consumatore text NOT NULL,
                           prodotto uuid NOT NULL DEFAULT gen_random_uuid(),
                           quantità smallint NOT NULL,
                           CONSTRAINT Preferiti_pkey PRIMARY KEY (consumatore, prodotto),
                           CONSTRAINT Preferiti_prodotto_fkey FOREIGN KEY (prodotto) REFERENCES Prodotti(id),
                           CONSTRAINT Preferiti_consumatore_fkey FOREIGN KEY (consumatore) REFERENCES Utente(email)
);