DROP TABLE tilbakemeldinger;
DROP TABLE innleveringer;
DROP TABLE ovinger;
DROP TABLE brukere;

CREATE TABLE brukere(
brukerID INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NOT NULL UNIQUE,
navn VARCHAR(50) NOT NULL,
passord VARCHAR(256) NOT NULL,
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
nytteverdi INT NOT NULL DEFAULT 0,
CONSTRAINT tilbakemelding_fk1 FOREIGN KEY(brukerID, ovingsID) REFERENCES innleveringer(brukerID, ovingsID),
CONSTRAINT tilbakemelding_fk2 FOREIGN KEY(vurderingsbruker) REFERENCES brukere(brukerID),
CONSTRAINT tilbakemelding_pk PRIMARY KEY(brukerID, ovingsID, vurderingsbruker),
INDEX(brukerID, ovingsID, vurderingsbruker)
) ENGINE=innoDB;

INSERT INTO brukere VALUES(DEFAULT, 'haakon.jarle.hassel@gmail.com', 'Håkon Jarle Hassel', 'd4e16b4851340746ec83625032353f31c5a6a5bfb62f3eaba3b199ea0047cc0ee9144582229107631ad47d9c68b90aa1c76d51a4621f4a7d53c50bd47bca6b30', 'skjeggvekstitelt', DEFAULT);
INSERT INTO brukere VALUES(DEFAULT, 'test@test.test', 'TirikSan', 'd4e16b4851340746ec83625032353f31c5a6a5bfb62f3eaba3b199ea0047cc0ee9144582229107631ad47d9c68b90aa1c76d51a4621f4a7d53c50bd47bca6b30', 'skjeggvekstitelt', DEFAULT);

INSERT INTO ovinger VALUES(DEFAULT, 'Øving 1', 'Lag ditt første PHP-script. Scriptet skal skrive ut "Hei Verden!" når besøkende klikker seg inn på siden din', '2014-04-30', DEFAULT);
INSERT INTO ovinger VALUES(DEFAULT, 'Øving 3', 'Lag et PHP-script som skriver ut en valgfri sang fra en .txt-fil', '2014-05-05', TRUE);
INSERT INTO ovinger VALUES(DEFAULT, 'Øving 2', 'Lag en PHP-side som er dynamisk.', '2014-05-02', DEFAULT);

INSERT INTO innleveringer VALUES(100, 1, 'Jeg laget dette: www.hassel.in', DEFAULT, DEFAULT);
INSERT INTO innleveringer VALUES(101, 1, 'Er dette godkjent? www.arngrens.no', DEFAULT, DEFAULT);
INSERT INTO innleveringer VALUES(101, 2, 'huehueheuhuehue', DEFAULT, DEFAULT);

select ovinger.`ovingsID`, innleveringer.`brukerID` from innleveringer right outer join ovinger on ovinger.`ovingsID` = innleveringer.`ovingsID` and brukerID = 101;

SELECT innleveringer.ovingsID, innleveringer.brukerID, COUNT(tilbakemeldinger.brukerID) AS tilbakemeldinger 
FROM innleveringer LEFT OUTER JOIN tilbakemeldinger
ON innleveringer.brukerID = tilbakemeldinger.brukerID
AND innleveringer.ovingsID = tilbakemeldinger.ovingsID
WHERE innleveringer.brukerID != 101 AND innleveringer.ovingsID = 1
GROUP BY tilbakemeldinger.ovingsID;
