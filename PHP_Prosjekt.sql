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

ALTER DATABASE MOOC CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE ovinger CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE brukere CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE innleveringer CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE tilbakemeldinger CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- SELECT innleveringer.ovingsID, innleveringer.brukerID, COUNT(tilbakemeldinger.brukerID) AS tilbakemeldinger 
-- FROM innleveringer LEFT OUTER JOIN tilbakemeldinger
-- ON innleveringer.brukerID = tilbakemeldinger.brukerID
-- AND innleveringer.ovingsID = tilbakemeldinger.ovingsID
-- WHERE innleveringer.brukerID != 102 
-- AND (vurderingsbruker != 102 OR vurderingsbruker IS NULL)
-- AND innleveringer.ovingsID = 1 
-- GROUP BY tilbakemeldinger.ovingsID;
