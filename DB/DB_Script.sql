CREATE DATABASE IF NOT EXISTS Euro_Soccer_DB;

USE Euro_Soccer_DB;

CREATE TABLE Campionato (

	ID_C					INT UNSIGNED AUTO_INCREMENT,

	Nome					VARCHAR(255) NOT NULL UNIQUE,

	Nazione					VARCHAR(255),

	Categoria				TINYINT UNSIGNED,

	Num_Edizione			TINYINT UNSIGNED,

    PRIMARY KEY(ID_C)

);

CREATE TABLE Stadio (

	Nome					VARCHAR(255),

	Inaugurazione			DATE NOT NULL,

	Costo					SMALLINT UNSIGNED,

	Posti					MEDIUMINT UNSIGNED NOT NULL,

    PRIMARY KEY(Nome)

);

CREATE TABLE Squadra (

	ID_C					INT UNSIGNED NOT NULL,

	ID_S					INT UNSIGNED AUTO_INCREMENT,

	Nome					VARCHAR(255) UNIQUE NOT NULL,

	Città					VARCHAR(255) NOT NULL,

	Fondazione				DATE NOT NULL,

	Stadio					VARCHAR(255) NOT NULL,

	Scudetti				TINYINT UNSIGNED NOT NULL DEFAULT 0,

    PRIMARY KEY(ID_S),

    FOREIGN KEY(ID_C) REFERENCES Campionato(ID_C) ON UPDATE NO ACTION,

    FOREIGN KEY(Stadio) REFERENCES Stadio(Nome) ON UPDATE NO ACTION

);

CREATE TABLE Membro (

	ID_M					INT UNSIGNED AUTO_INCREMENT,

	Squadra					INT UNSIGNED NOT NULL,

	Ruolo					VARCHAR(255) NOT NULL,

	Nome					VARCHAR(255) NOT NULL,

	Nascita					DATE NOT NULL,

	Nazione					VARCHAR(255),

	Ruolo_Squadra			VARCHAR(255),

	Ruolo_Preferito			VARCHAR(255),

	Piede_Preferito			VARCHAR(255),

	Altezza					DECIMAL(5, 2),

	Peso					TINYINT UNSIGNED,

	TOT                     TINYINT UNSIGNED,

	VEL                     TINYINT UNSIGNED,

	DRI                     TINYINT UNSIGNED,

	TIR                     TINYINT UNSIGNED,

	DIF                     TINYINT UNSIGNED,

	PAS                     TINYINT UNSIGNED,

	FIS                     TINYINT UNSIGNED,

	POR                     TINYINT UNSIGNED,

	ACC                     TINYINT UNSIGNED,

	VEL_S                   TINYINT UNSIGNED,

	AGI                     TINYINT UNSIGNED,

	EQU                     TINYINT UNSIGNED,

	RIF                     TINYINT UNSIGNED,

	CON                     TINYINT UNSIGNED,

	DRIB                    TINYINT UNSIGNED,

	FRE                     TINYINT UNSIGNED,

	PIA                     TINYINT UNSIGNED,

	FIN                     TINYINT UNSIGNED,

	POT_T                   TINYINT UNSIGNED,

	TIR_D                   TINYINT UNSIGNED,

	VOL                     TINYINT UNSIGNED,

	RIG                     TINYINT UNSIGNED,

	`INT`                   TINYINT UNSIGNED,

	PT                      TINYINT UNSIGNED,

	MAR                     TINYINT UNSIGNED,

	CON_P                   TINYINT UNSIGNED,

	SCI                     TINYINT UNSIGNED,

	VIS                     TINYINT UNSIGNED,

	CROS                    TINYINT UNSIGNED,

	PCP                     TINYINT UNSIGNED,

	PAS_C                   TINYINT UNSIGNED,

	PAS_L                   TINYINT UNSIGNED,

	EFF                     TINYINT UNSIGNED,

	ELE                     TINYINT UNSIGNED,

	RES                     TINYINT UNSIGNED,

	`FOR`                   TINYINT UNSIGNED,

	AGG                     TINYINT UNSIGNED,

	POR_TUF                 TINYINT UNSIGNED,

	POR_RIF                 TINYINT UNSIGNED,

	POR_PRE                 TINYINT UNSIGNED,

	POR_RIN                 TINYINT UNSIGNED,

	POR_PIA                 TINYINT UNSIGNED,

    PRIMARY KEY(ID_M),

    FOREIGN KEY(Squadra) REFERENCES Squadra(ID_S) ON UPDATE NO ACTION,

    CHECK(Piede_Preferito = 'Sinistro' OR Piede_Preferito = 'Destro'),

    CHECK(TOT <= 100 AND VEL <= 100 AND DRI <= 100 AND TIR <= 100 AND

          DIF <= 100 AND PAS <= 100 AND FIS <= 100 AND POR <= 100 AND

          ACC <= 100 AND VEL_S <= 100 AND AGI <= 100 AND EQU <= 100 AND

          RIF <= 100 AND CON <= 100 AND DRIB <= 100 AND FRE <= 100 AND 

          PIA <= 100 AND FIN <= 100 AND POT_T <= 100 AND TIR_D <= 100 AND 

          VOL <= 100 AND RIG <= 100 AND `INT` <= 100 AND PT <= 100 AND 

          MAR <= 100 AND CON_P <= 100 AND SCI <= 100 AND VIS <= 100 AND 

          CROS <= 100 AND PCP <= 100 AND PAS_C <= 100 AND PAS_L <= 100 AND 

          EFF <= 100 AND ELE <= 100 AND RES <= 100 AND `FOR` <= 100 AND 

          AGG <= 100 AND POR_TUF <= 100 AND POR_RIF <= 100 AND 

          POR_PRE <= 100 AND POR_RIN <= 100 AND POR_PIA <= 100)

);

CREATE TABLE Partita (

	ID_P					INT UNSIGNED AUTO_INCREMENT,

	`Data`					DATE NOT NULL,

	Settimana				TINYINT UNSIGNED NOT NULL,

	Squadra_C				INT UNSIGNED NOT NULL,

	Squadra_T				INT UNSIGNED NOT NULL,

	Possesso_C				TINYINT UNSIGNED NOT NULL,

	Possesso_T				TINYINT UNSIGNED NOT NULL,

    PRIMARY KEY(ID_P),

	UNIQUE(Squadra_C, Squadra_T),

    FOREIGN KEY(Squadra_C) REFERENCES Squadra(ID_S) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra_T) REFERENCES Squadra(ID_S) ON UPDATE NO ACTION,

    CHECK(Possesso_C + Possesso_T = 100)

);

CREATE TABLE Evento (

	ID_E					INT UNSIGNED AUTO_INCREMENT,

	ID_P					INT UNSIGNED NOT NULL,

	Minuti					TINYINT UNSIGNED NOT NULL,

	Minuti_Recupero			TINYINT UNSIGNED,

	Tipo					VARCHAR(255) NOT NULL,

	Sotto_Tipo				VARCHAR(255),

	Giocatore_Attivo		INT UNSIGNED,

	Giocatore_Passivo		INT UNSIGNED,

	Squadra					INT UNSIGNED NOT NULL,

    PRIMARY KEY(ID_E),

    FOREIGN KEY(ID_P) REFERENCES Partita(ID_P) ON UPDATE NO ACTION,

    FOREIGN KEY(Giocatore_Attivo) REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    FOREIGN KEY(Giocatore_Passivo) REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra) REFERENCES Squadra(ID_S) ON UPDATE NO ACTION,

    CHECK(Minuti BETWEEN 1 AND 90),

    CHECK(Minuti_Recupero IS NOT NULL AND (Minuti = 45 OR Minuti = 90))

);

CREATE TABLE Formazione (

	ID_P					INT UNSIGNED,

	Squadra_C				INT UNSIGNED NOT NULL,

	Squadra_T				INT UNSIGNED NOT NULL,

	Giocatore_C_1			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

	Giocatore_C_2			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

	Giocatore_C_3			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

	Giocatore_C_4			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

	Giocatore_C_5			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_6			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_7			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_8			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_9			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_10			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_11			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_1			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_2			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_3			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_4			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_5			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_6			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_7			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_8			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_9			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_10			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_T_11			INT UNSIGNED REFERENCES Membro(ID_M) ON UPDATE NO ACTION,

    Giocatore_C_X1          TINYINT UNSIGNED,

    Giocatore_C_X2          TINYINT UNSIGNED,

    Giocatore_C_X3          TINYINT UNSIGNED,

    Giocatore_C_X4          TINYINT UNSIGNED,

    Giocatore_C_X5          TINYINT UNSIGNED,

    Giocatore_C_X6          TINYINT UNSIGNED,

    Giocatore_C_X7          TINYINT UNSIGNED,

    Giocatore_C_X8          TINYINT UNSIGNED,

    Giocatore_C_X9          TINYINT UNSIGNED,

    Giocatore_C_X10         TINYINT UNSIGNED,

    Giocatore_C_X11         TINYINT UNSIGNED,

    Giocatore_T_X1          TINYINT UNSIGNED,

    Giocatore_T_X2          TINYINT UNSIGNED,

    Giocatore_T_X3          TINYINT UNSIGNED,

    Giocatore_T_X4          TINYINT UNSIGNED,

    Giocatore_T_X5          TINYINT UNSIGNED,

    Giocatore_T_X6          TINYINT UNSIGNED,

    Giocatore_T_X7          TINYINT UNSIGNED,

    Giocatore_T_X8          TINYINT UNSIGNED,

    Giocatore_T_X9          TINYINT UNSIGNED,

    Giocatore_T_X10         TINYINT UNSIGNED,

    Giocatore_T_X11         TINYINT UNSIGNED,

    Giocatore_C_Y1          TINYINT UNSIGNED,

    Giocatore_C_Y2          TINYINT UNSIGNED,

    Giocatore_C_Y3          TINYINT UNSIGNED,

    Giocatore_C_Y4          TINYINT UNSIGNED,

    Giocatore_C_Y5          TINYINT UNSIGNED,

    Giocatore_C_Y6          TINYINT UNSIGNED,

    Giocatore_C_Y7          TINYINT UNSIGNED,

    Giocatore_C_Y8          TINYINT UNSIGNED,

    Giocatore_C_Y9          TINYINT UNSIGNED,

    Giocatore_C_Y10         TINYINT UNSIGNED,

    Giocatore_C_Y11         TINYINT UNSIGNED,

    Giocatore_T_Y1          TINYINT UNSIGNED,

    Giocatore_T_Y2          TINYINT UNSIGNED,

    Giocatore_T_Y3          TINYINT UNSIGNED,

    Giocatore_T_Y4          TINYINT UNSIGNED,

    Giocatore_T_Y5          TINYINT UNSIGNED,

    Giocatore_T_Y6          TINYINT UNSIGNED,

    Giocatore_T_Y7          TINYINT UNSIGNED,

    Giocatore_T_Y8          TINYINT UNSIGNED,

    Giocatore_T_Y9          TINYINT UNSIGNED,

    Giocatore_T_Y10         TINYINT UNSIGNED,

    Giocatore_T_Y11         TINYINT UNSIGNED,

    PRIMARY KEY(ID_P),

    UNIQUE(Squadra_C, Squadra_T),

    FOREIGN KEY(ID_P) REFERENCES Partita(ID_P) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra_C) REFERENCES Squadra(ID_S) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra_T) REFERENCES Squadra(ID_S) ON UPDATE NO ACTION,

    CHECK(Giocatore_C_X1 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X2 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X3 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X4 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X5 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X6 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X7 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X8 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X9 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X10 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_X11 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X1 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X2 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X3 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X4 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X5 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X6 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X7 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X8 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X9 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X10 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_X11 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y1 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y2 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y3 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y4 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y5 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y6 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y7 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y8 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y9 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y10 BETWEEN 1 AND 11),

    CHECK(Giocatore_C_Y11 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y1 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y2 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y3 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y4 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y5 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y6 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y7 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y8 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y9 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y10 BETWEEN 1 AND 11),

    CHECK(Giocatore_T_Y11 BETWEEN 1 AND 11)

);

CREATE TABLE CalendarioRisultati (

    Campionato              VARCHAR(255) NOT NULL,

    DA                      DATE NOT NULL,

    SA                      TINYINT UNSIGNED NOT NULL,

    RA                      VARCHAR(255) NOT NULL,

    Squadre                 VARCHAR(255) NOT NULL,

    RR                      VARCHAR(255) NOT NULL,

    SR                      TINYINT UNSIGNED NOT NULL,

    DR                      DATE NOT NULL,

    FOREIGN KEY(Campionato) REFERENCES Campionato(Nome) ON UPDATE NO ACTION

);

CREATE TABLE Classifica (

    Campionato              VARCHAR(255) NOT NULL,

    Squadra                 VARCHAR(255) NOT NULL,

    Punti                   TINYINT UNSIGNED NOT NULL,

    P_Vinte                 TINYINT UNSIGNED NOT NULL,

    P_Pareggiate            TINYINT UNSIGNED NOT NULL,

    P_Perse                 TINYINT UNSIGNED NOT NULL,

    G_Fatti                 TINYINT UNSIGNED NOT NULL,

    G_Subiti                TINYINT UNSIGNED NOT NULL,

    Diff_Reti               TINYINT NOT NULL,

    PRIMARY KEY(Squadra),

    UNIQUE(Campionato, Squadra),

    FOREIGN KEY(Campionato) REFERENCES Campionato(Nome) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra) REFERENCES Squadra(Nome) ON UPDATE NO ACTION

);

CREATE TABLE DettaglioCampionato (

    Nome                    VARCHAR(255) NOT NULL UNIQUE,

    Nazione                 VARCHAR(255) NOT NULL,

    Categoria               TINYINT UNSIGNED NOT NULL,

    Edizione                VARCHAR(255) NOT NULL,

    Num_Partite             SMALLINT UNSIGNED NOT NULL,

    Num_Squadre             TINYINT UNSIGNED NOT NULL,

    Num_Giornate            TINYINT UNSIGNED NOT NULL,

    Data_Inizio             DATE NOT NULL,

    Data_Fine               DATE NOT NULL,

    Goal_Segnati            SMALLINT UNSIGNED NOT NULL,

    MGP                     DECIMAL(2, 1),

    PRIMARY KEY(Nome),

    FOREIGN KEY(Nome) REFERENCES Campionato(Nome) ON UPDATE NO ACTION

);

CREATE TABLE DettaglioPartita (

    Campionato              VARCHAR(255) NOT NULL,

    ID_P                    INT UNSIGNED NOT NULL,

    `Data`                  DATE NOT NULL,

    Sett                    TINYINT UNSIGNED NOT NULL,

    Squadra_C               VARCHAR(255) NOT NULL,

    Squadra_T               VARCHAR(255) NOT NULL,

    GC                      TINYINT UNSIGNED NOT NULL,

    GT                      TINYINT UNSIGNED NOT NULL,

    TIPC                    TINYINT UNSIGNED NOT NULL,

    TIPT                    TINYINT UNSIGNED NOT NULL,

    TFC                     TINYINT UNSIGNED NOT NULL,

    TFT                     TINYINT UNSIGNED NOT NULL,

    CorC                    TINYINT UNSIGNED NOT NULL,

    CorT                    TINYINT UNSIGNED NOT NULL,

    FC                      TINYINT UNSIGNED NOT NULL,

    FT                      TINYINT UNSIGNED NOT NULL,

    CGC                     TINYINT UNSIGNED NOT NULL,

    CGT                     TINYINT UNSIGNED NOT NULL,

    CRC                     TINYINT UNSIGNED NOT NULL,

    CRT                     TINYINT UNSIGNED NOT NULL,

    CroC                    TINYINT UNSIGNED NOT NULL,

    CroT                    TINYINT UNSIGNED NOT NULL,

    PoC                     TINYINT UNSIGNED NOT NULL,

    PoT                     TINYINT UNSIGNED NOT NULL,

    PRIMARY KEY(ID_P),

    FOREIGN KEY(Campionato) REFERENCES Campionato(Nome) ON UPDATE NO ACTION,

    FOREIGN KEY(ID_P) REFERENCES Partita(ID_P) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra_C) REFERENCES Squadra(Nome) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra_T) REFERENCES Squadra(Nome) ON UPDATE NO ACTION

);

CREATE TABLE DettaglioSquadra (

    Campionato              VARCHAR(255) NOT NULL,

    Squadra                 VARCHAR(255) NOT NULL,

    PIC                     TINYINT UNSIGNED NOT NULL,

    Qualificazione          VARCHAR(255),

    Presidente              VARCHAR(255),

    Capocannoniere          VARCHAR(255) NOT NULL,

    VMG                     DECIMAL(3, 1) NOT NULL,

    PS                      MEDIUMINT UNSIGNED,

    CS                      MEDIUMINT UNSIGNED NOT NULL,

    PRIMARY KEY(Squadra),

    UNIQUE(Campionato, Squadra),

    FOREIGN KEY(Campionato) REFERENCES Campionato(Nome) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra) REFERENCES Squadra(Nome) ON UPDATE NO ACTION

);

CREATE TABLE PrestazioneGiocatore (

    Campionato              VARCHAR(255) NOT NULL,

    Squadra                 VARCHAR(255) NOT NULL,

    Giocatore               VARCHAR(255) NOT NULL,

    GS                      TINYINT UNSIGNED NOT NULL,

    GSR                     TINYINT UNSIGNED NOT NULL,

    GAU                     TINYINT UNSIGNED NOT NULL,

    GSCP                    TINYINT UNSIGNED NOT NULL,

    CPB                     TINYINT UNSIGNED NOT NULL,

    ASS                     TINYINT UNSIGNED NOT NULL,

    FC                      TINYINT UNSIGNED NOT NULL,

    FS                      TINYINT UNSIGNED NOT NULL,

    CAG                     TINYINT UNSIGNED NOT NULL,

    CAR                     TINYINT UNSIGNED NOT NULL,

    TP                      TINYINT UNSIGNED NOT NULL,

    TF                      TINYINT UNSIGNED NOT NULL,

    CRO                     TINYINT UNSIGNED NOT NULL,

    COR                     TINYINT UNSIGNED NOT NULL,

    PRIMARY KEY(Squadra, Giocatore),

    FOREIGN KEY(Campionato) REFERENCES Campionato(Nome) ON UPDATE NO ACTION,

    FOREIGN KEY(Squadra) REFERENCES Squadra(Nome) ON UPDATE NO ACTION

);

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Campionato.csv' 

INTO TABLE Campionato FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Stadio.csv' 

INTO TABLE Stadio FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Squadra.csv' 

INTO TABLE Squadra FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Membro.csv' 

INTO TABLE Membro FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Partita.csv' 

INTO TABLE Partita FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Evento.csv' 

INTO TABLE Evento FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Formazione.csv' 

INTO TABLE Formazione FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/CalendarioRisultati.csv' 

INTO TABLE CalendarioRisultati FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/Classifica.csv' 

INTO TABLE Classifica FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/DettaglioCampionato.csv' 

INTO TABLE DettaglioCampionato FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/DettaglioPartita.csv' 

INTO TABLE DettaglioPartita FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/DettaglioSquadra.csv' 

INTO TABLE DettaglioSquadra FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;

LOAD DATA LOCAL INFILE '/home/ricc/Documenti/Materiale\ Didattico/Progetto\ TecWeb/Database/Dataset/PrestazioneGiocatore.csv' 

INTO TABLE PrestazioneGiocatore FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 ROWS;
