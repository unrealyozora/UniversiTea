-- =======================================================
-- 1. DATI STRUTTURALI
-- (UUID generati staticamente per mantenere le relazioni)
-- =======================================================

INSERT INTO Università (nome, città) VALUES
                                         ('Politecnico di Milano', 'Milano'),
                                         ('Università di Bologna', 'Bologna'),
                                         ('Università degli Studi di Padova', 'Padova');

-- Lauree (UUID pattern: 1000...)
INSERT INTO Laurea (codice_unico, tipologia, universita, nome_laurea, cod_ministero) VALUES
        ('10000000-0000-0000-0000-000000000001', 'Triennale', 'Politecnico di Milano', 'Ingegneria Informatica', 'L-8'),
        ('10000000-0000-0000-0000-000000000002', 'Magistrale', 'Università di Bologna', 'Digital Humanities', 'LM-43'),
        ('10000000-0000-0000-0000-000000000003', 'Triennale', 'Università degli Studi di Padova', 'Informatica', 'L-31');

-- Insegnamenti (UUID pattern: 2000...)
INSERT INTO Insegnamento (codice, docente, laurea, nome, cfu) VALUES
            ('20000000-0000-0000-0000-000000000001', 'Prof. Rossi', '10000000-0000-0000-0000-000000000001', 'Basi di Dati', 5),
            ('20000000-0000-0000-0000-000000000002', 'Prof. Verdi', '10000000-0000-0000-0000-000000000002', 'Editoria Digitale', 6),
            ('20000000-0000-0000-0000-000000000003', 'Prof. Algoritmi', '10000000-0000-0000-0000-000000000003', 'Algoritmi e Strutture Dati', 9),
            ('20000000-0000-0000-0000-000000000004', 'Prof. Sistemi', '10000000-0000-0000-0000-000000000003', 'Sistemi Operativi', 9);

-- Utenti
INSERT INTO Utente (email, indirizzo, username, telefono, password, punti_fedelta, tipo_utente) VALUES
           ('venditore@test.it', 'Via Roma 1, Milano', 'BestSellerPro', 3331234567, 'pass1234', 100, 'Venditore'),
           ('studente@test.it', 'Via Po 2, Torino', 'StudyGuy99', 3337654321, 'pass5678', 10, 'Compratore'),
           ('tutor.padova@test.it', 'Via Belzoni 7, Padova', 'AlgoGuru', 3330000001, 'passPadova', 250, 'Venditore'),
           ('matricola.pd@test.it', 'Via Luzzati 1, Padova', 'FreshmanPD', 3330000002, 'passStud', 0, 'Compratore');


-- =======================================================
-- 2. CATALOGO PRODOTTI
-- (Tutti i prodotti vanno in 'Prodotti', poi nelle tabelle specifiche)
-- =======================================================

-- ---------------------
-- BEVANDE (UUID pattern: 3000...)
-- ---------------------

-- Prodotto 1: Tè Matcha
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('30000000-0000-0000-0000-000000000001', 50, 'Tè Matcha Cerimoniale', 25.50, 'Tè verde giapponese di alta qualità.', 'http://modello/matcha');
INSERT INTO Bevande (id, temp_consigliata, tipologia_bevanda, scoop) VALUES
    ('30000000-0000-0000-0000-000000000001', 80, 'Tè', 'Tè adatto per i riti pre esame');

-- Prodotto 2: Cioccolata Fondente
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('30000000-0000-0000-0000-000000000002', 30, 'Cioccolata Fondente 80%', 5.00, 'Cioccolata densa per studio notturno.', NULL);
INSERT INTO Bevande (id, temp_consigliata, tipologia_bevanda, scoop) VALUES
    ('30000000-0000-0000-0000-000000000002', 90, 'Cioccolato', 'Cioccolato immancabile per sparlare dei compagni di corso nelle fredde giornate invernali');

-- Prodotto 3: Infuso Frutti Rossi
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('30000000-0000-0000-0000-000000000003', 40, 'Infuso Frutti Rossi', 4.50, 'Infuso energetico naturale.', NULL);
INSERT INTO Bevande (id, temp_consigliata, tipologia_bevanda, scoop) VALUES
    ('30000000-0000-0000-0000-000000000003', 95, 'Infuso', 'Non saprei cosa mettere, viva la primavera');


-- ---------------------
-- SERVIZI (UUID pattern: 4000...)
-- ---------------------

-- Prodotto 4: Ripetizioni SQL
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('40000000-0000-0000-0000-000000000001', 1, 'Ripetizioni SQL Avanzato', 20.00, 'Lezione su query complesse.', NULL);
INSERT INTO Servizi (id, tipologia_servizi, livello_urgenza, insegnamento) VALUES
    ('40000000-0000-0000-0000-000000000001', 'Ripetizioni', 'Alto', '20000000-0000-0000-0000-000000000001');

-- Prodotto 5: Sbobine Basi di Dati
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('40000000-0000-0000-0000-000000000002', 10, 'Sbobine Basi di Dati 2024', 15.00, 'Trascrizione lezioni Prof. Rossi.', NULL);
INSERT INTO Servizi (id, tipologia_servizi, livello_urgenza, insegnamento) VALUES
    ('40000000-0000-0000-0000-000000000002', 'Sbobine di lezione', 'Medio', '20000000-0000-0000-0000-000000000001');

-- Prodotto 6: Appunti Algoritmi
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('40000000-0000-0000-0000-000000000003', 100, 'Appunti completi Algoritmi', 10.00, 'Schemi e pseudocodice.', 'http://preview/algo');
INSERT INTO Servizi (id, tipologia_servizi, livello_urgenza, insegnamento) VALUES
    ('40000000-0000-0000-0000-000000000003', 'Fornitura appunti', 'Basso', '20000000-0000-0000-0000-000000000003');


-- ---------------------
-- MERCHANDISING (UUID pattern: 5000...)
-- ---------------------

-- Prodotto 7: Tazza Matcha (Accessorio per Tè Matcha - ID: ...300...1)
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('50000000-0000-0000-0000-000000000001', 20, 'Set Tazza Matcha', 12.00, 'Tazza e frustino.', 'http://modello/tazza');
INSERT INTO March_Bevande (id, id_bevanda, tipologia_march, Materiale) VALUES
    ('50000000-0000-0000-0000-000000000001', '30000000-0000-0000-0000-000000000001', 'Accessori', 'Ceramica e Bambù');

-- Prodotto 8: Felpa Unipd (Legata fittiziamente all'infuso - ID: ...300...3)
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('50000000-0000-0000-0000-000000000002', 50, 'Felpa Informatica Unipd', 35.00, 'Felpa rossa dipartimento.', 'http://img/felpa');
INSERT INTO March_Bevande (id, id_bevanda, tipologia_march, Materiale) VALUES
    ('50000000-0000-0000-0000-000000000002', '30000000-0000-0000-0000-000000000003', 'Abbigliamento', 'Cotone');


-- ---------------------
-- BUNDLE (UUID pattern: 6000...)
-- ---------------------

-- Prodotto 9: Starter Pack Polimi
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('60000000-0000-0000-0000-000000000001', 5, 'Starter Pack Basi di Dati', 35.00, 'Sbobine + Tè Matcha.', NULL);

-- Prodotto 10: Kit Salva-Esame Padova
INSERT INTO Prodotti (id, disponibilità, nome, prezzo, descrizione, link_modello) VALUES
    ('60000000-0000-0000-0000-000000000002', 10, 'Kit Salva-Esame Algoritmi', 40.00, 'Appunti + Felpa.', NULL);


-- =======================================================
-- 3. COMPOSIZIONE DEI BUNDLE & VENDITE
-- =======================================================

-- Definizione contenuto Bundle 1 (Starter Pack = Sbobine + Tè)
INSERT INTO Bundle (id_bundle, contenuto, precent_sconto) VALUES
    ('60000000-0000-0000-0000-000000000001', '40000000-0000-0000-0000-000000000002', 10), -- Sbobine
    ('60000000-0000-0000-0000-000000000001', '30000000-0000-0000-0000-000000000001', 15);  -- Tè

-- Definizione contenuto Bundle 2 (Kit Padova = Appunti + Felpa)
INSERT INTO Bundle (id_bundle, contenuto, precent_sconto) VALUES
        ('60000000-0000-0000-0000-000000000002', '40000000-0000-0000-0000-000000000003', 20), -- Appunti
        ('60000000-0000-0000-0000-000000000002', '50000000-0000-0000-0000-000000000002', 10); -- Felpa

-- Assegnazione Vendite
INSERT INTO Vendita (venditore, prodotto, quantita) VALUES
-- Venditore Milano vende: Tè, Sbobine, Bundle Polimi
('venditore@test.it', '30000000-0000-0000-0000-000000000001', 50),
('venditore@test.it', '40000000-0000-0000-0000-000000000002', 10),
('venditore@test.it', '60000000-0000-0000-0000-000000000001', 5),
-- Venditore Padova vende: Appunti, Felpa, Bundle Padova
('tutor.padova@test.it', '40000000-0000-0000-0000-000000000003', 100),
('tutor.padova@test.it', '50000000-0000-0000-0000-000000000002', 50),
('tutor.padova@test.it', '60000000-0000-0000-0000-000000000002', 10);

-- Esempio Carrello
INSERT INTO Carrello (consumatore, prodotto, quantità) VALUES
                                                           ('matricola.pd@test.it', '60000000-0000-0000-0000-000000000002', 1),
                                                           ('matricola.pd@test.it', '30000000-0000-0000-0000-000000000003', 3);