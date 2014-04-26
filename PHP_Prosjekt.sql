DROP TABLE tilbakemeldinger;
DROP TABLE innleveringer;
DROP TABLE ovinger;
DROP TABLE brukere;

CREATE TABLE brukere(
brukerID INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NOT NULL UNIQUE,
navn VARCHAR(50) NOT NULL,
passord VARCHAR(128) NOT NULL,
rolle SMALLINT DEFAULT 0,
INDEX(brukerID)
) ENGINE=innoDB, AUTO_INCREMENT = 100;

CREATE TABLE ovinger(
ovingsID INT AUTO_INCREMENT PRIMARY KEY,
oppgavetekst VARCHAR(250) NOT NULL,
innleveringsfrist DATE,
obligatorisk BOOLEAN DEFAULT TRUE,
INDEX(ovingsID)
) ENGINE=innoDB;

CREATE TABLE innleveringer(
brukerID INT NOT NULL,
ovingsID INT NOT NULL,
innlevering VARCHAR(500) NOT NULL,
innleveringsdato TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
tilbakemelding VARCHAR(200) NOT NULL,
nytteverdi INT NOT NULL,
CONSTRAINT tilbakemelding_fk1 FOREIGN KEY(brukerID, ovingsID) REFERENCES innleveringer(brukerID, ovingsID),
CONSTRAINT tilbakemelding_fk2 FOREIGN KEY(vurderingsbruker) REFERENCES brukere(brukerID),
CONSTRAINT tilbakemelding_pk PRIMARY KEY(brukerID, ovingsID, vurderingsbruker),
INDEX(brukerID, ovingsID, vurderingsbruker)
) ENGINE=innoDB;