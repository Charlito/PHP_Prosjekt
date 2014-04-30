DROP TABLE tilbakemeldinger;
DROP TABLE innleveringer;
DROP TABLE ovinger;
DROP TABLE brukere;

CREATE TABLE brukere(
brukerID INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NOT NULL UNIQUE,
navn VARCHAR(50) NOT NULL,
passord VARCHAR(512) NOT NULL,
salt VARCHAR(16) NOT NULL,
rolle SMALLINT DEFAULT 0,
INDEX(brukerID)
) ENGINE=innoDB, AUTO_INCREMENT = 100;

CREATE TABLE ovinger(
ovingsID INT AUTO_INCREMENT PRIMARY KEY,
navn VARCHAR(64) NOT NULL,
oppgavetekst VARCHAR(1024) NOT NULL,
innleveringsfrist DATE,
obligatorisk BOOLEAN DEFAULT TRUE,
INDEX(ovingsID)
) ENGINE=innoDB;

CREATE TABLE innleveringer(
brukerID INT NOT NULL,
ovingsID INT NOT NULL,
innlevering VARCHAR(1000) NOT NULL,
innleveringsdato TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
rettet BOOLEAN DEFAULT FALSE,
godkjent BOOLEAN DEFAULT FALSE,
CONSTRAINT innlevering_fk1 FOREIGN KEY(brukerID) REFERENCES brukere(brukerID) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT innlevering_fk2 FOREIGN KEY(ovingsID) REFERENCES ovinger(ovingsID) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT innlevering_pk PRIMARY KEY(brukerID, ovingsID),
INDEX(brukerID, ovingsID)
) ENGINE=innoDB;

CREATE TABLE tilbakemeldinger(
brukerID INT NOT NULL,
ovingsID INT NOT NULL,
vurderingsbruker INT NOT NULL,
tilbakemelding VARCHAR(1000) NOT NULL,
godkjent BOOLEAN,
nytteverdi INT NOT NULL DEFAULT 0,
CONSTRAINT tilbakemelding_fk1 FOREIGN KEY(brukerID, ovingsID) REFERENCES innleveringer(brukerID, ovingsID),
CONSTRAINT tilbakemelding_fk2 FOREIGN KEY(vurderingsbruker) REFERENCES brukere(brukerID),
CONSTRAINT tilbakemelding_pk PRIMARY KEY(brukerID, ovingsID, vurderingsbruker),
INDEX(brukerID, ovingsID, vurderingsbruker)
) ENGINE=innoDB;

ALTER TABLE MOOC.innleveringer MODIFY innlevering VARCHAR(1000) NOT NULL;
ALTER TABLE MOOC.tilbakemeldinger MODIFY tilbakemelding VARCHAR(1000) NOT NULL;
ALTER TABLE MOOC.innleveringer ADD rettet BOOLEAN DEFAULT FALSE AFTER innleveringsdato;
ALTER TABLE MOOC.tilbakemeldinger ADD godkjent BOOLEAN AFTER tilbakemelding;

ALTER DATABASE MOOC CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE ovinger CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE brukere CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE innleveringer CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE tilbakemeldinger CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- Insert data --
INSERT INTO brukere VALUES(DEFAULT, 'haakon.jarle.hassel@gmail.com', 'HÃ¥kon Jarle Hassel', 'd4e16b4851340746ec83625032353f31c5a6a5bfb62f3eaba3b199ea0047cc0ee9144582229107631ad47d9c68b90aa1c76d51a4621f4a7d53c50bd47bca6b30', 'skjeggvekstitelt', DEFAULT);
INSERT INTO brukere VALUES(DEFAULT, 'kristian.aabrekk@gmail.com', 'Kristian Aabrekk', 'd4e16b4851340746ec83625032353f31c5a6a5bfb62f3eaba3b199ea0047cc0ee9144582229107631ad47d9c68b90aa1c76d51a4621f4a7d53c50bd47bca6b30', 'skjeggvekstitelt', DEFAULT);
INSERT INTO brukere VALUES(DEFAULT, 'test@test.test', 'Testbruker', 'd4e16b4851340746ec83625032353f31c5a6a5bfb62f3eaba3b199ea0047cc0ee9144582229107631ad47d9c68b90aa1c76d51a4621f4a7d53c50bd47bca6b30', 'skjeggvekstitelt', DEFAULT);
INSERT INTO brukere VALUES(DEFAULT, 'luke@skywalker.com', 'Luke Skywalker', 'a3421fcec4e1924d844bb812a02ef5c4bcaa9eacc66ddfa4280c6ad1b1afedbbf38a197f048cc8e35ede48dd828671f63ebf1b84872cd729532ab38eff47af94', 'fbeca8e06868af2e', DEFAULT);
INSERT INTO brukere VALUES(DEFAULT, 'anakin@skywalker.com', 'Anakin Skywalker', '4ddd0c3865bf3de0012d97bfa1ea76118f51a5d9be6bb81e7bce8b817333bda415eccb055a4270167443cb2c1a96b3f6d9a3f8c9d77b65463cd133024726004a', 'ff8e4c85858f61a0', DEFAULT);
INSERT INTO brukere VALUES(DEFAULT, 'obi-wan@kenobi.com', 'Obi-Wan Kenobi', '6c24eff407add4e426fd9dba5cc8ff1c939437e7cedfee721b128ee7b3e9d292a4526b823e1ef0468467ac0b7655b2a81b04e2ecbe01492dccbe40d2eef1e51d', '9f056cb1ce1fc794', 1);

INSERT INTO ovinger VALUES(DEFAULT, 'Øving 1', 'Lag ditt første PHP-script. Scriptet skal skrive ut "Hei Verden!" når besøkende klikker seg inn på siden din', '2014-04-30', DEFAULT);
INSERT INTO ovinger VALUES(DEFAULT, 'Øving 3', 'Lag et PHP-script som skriver ut en valgfri sang fra en .txt-fil', '2014-05-05', TRUE);
INSERT INTO ovinger VALUES(DEFAULT, 'Øving 2', 'Lag en PHP-side som er dynamisk.', '2014-05-02', DEFAULT);
INSERT INTO ovinger VALUES(DEFAULT, 'Øving 4', 'Lag en PHP-side som heter STAS.', '2014-05-08', DEFAULT);
INSERT INTO ovinger VALUES(DEFAULT, 'Ekstraøving', 'Lag noe imponerende i AngularJS.', '2014-05-15', DEFAULT);

INSERT INTO innleveringer VALUES(100, 1, 'Jeg laget dette: www.hassel.in', DEFAULT, DEFAULT, DEFAULT);
INSERT INTO innleveringer VALUES(101, 1, 'Er dette godkjent? www.arngrens.no', DEFAULT, DEFAULT, DEFAULT);
INSERT INTO innleveringer VALUES(101, 2, 'Jeg har levert en fin besvarelse her: URL #1', DEFAULT, DEFAULT, DEFAULT);

INSERT INTO tilbakemeldinger VALUES(101, 1, 100, 'Nei dette var ikke bra...', DEFAULT, DEFAULT);
INSERT INTO tilbakemeldinger VALUES(101, 2, 100, 'Mye bedre', DEFAULT, DEFAULT);

-- Select sentences --
-- SELECT innleveringer.ovingsID, innleveringer.brukerID, COUNT(tilbakemeldinger.brukerID) AS tilbakemeldinger 
-- FROM innleveringer LEFT OUTER JOIN tilbakemeldinger
-- ON innleveringer.brukerID = tilbakemeldinger.brukerID
-- AND innleveringer.ovingsID = tilbakemeldinger.ovingsID
-- WHERE innleveringer.brukerID != 102 
-- AND (vurderingsbruker != 102 OR vurderingsbruker IS NULL)
-- AND innleveringer.ovingsID = 1 
-- GROUP BY tilbakemeldinger.ovingsID;
