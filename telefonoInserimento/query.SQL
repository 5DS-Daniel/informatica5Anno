CREATE DATABASE IF NOT EXISTS `my_franchini` 
DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `my_franchini`;

-- Creazione della tabella tcontatti
DROP TABLE IF EXISTS `tcontatti`;
CREATE TABLE IF NOT EXISTS `tcontatti` (
    id_contatti     BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(20) NOT NULL,
    cognome         VARCHAR(20) NOT NULL,
    codice_fiscale  CHAR(16) NOT NULL UNIQUE,
    matricola       CHAR(5) NOT NULL UNIQUE,
    data_nascita    DATE,
    ora_nascita     TIME,    
    attivo          BOOLEAN DEFAULT TRUE,
    CONSTRAINT chk_nome_cognome CHECK (CHAR_LENGTH(nome) >= 3 AND CHAR_LENGTH(cognome) >= 3),
    INDEX idx_contatti (nome, cognome)
) ENGINE=InnoDB;

-- Trigger per validare codice fiscale
DROP TRIGGER IF EXISTS `trg_codice_fiscale_insert`;
DELIMITER $$
CREATE TRIGGER `trg_codice_fiscale_insert` 
BEFORE INSERT ON `tcontatti` 
FOR EACH ROW 
BEGIN    
    IF NOT NEW.codice_fiscale REGEXP '^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'codice_fiscale';
    END IF;
END$$
DELIMITER ;

-- Trigger per controllare la data di nascita
DROP TRIGGER IF EXISTS `trg_data_nascita_insert`;
DELIMITER $$
CREATE TRIGGER `trg_data_nascita_insert` 
BEFORE INSERT ON `tcontatti` 
FOR EACH ROW 
BEGIN
    IF NEW.data_nascita > CURDATE() THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'data';
    END IF;
END$$
DELIMITER ;

-- Trigger per la validazione della matricola
DROP TRIGGER IF EXISTS `trg_matricola_insert`;
DELIMITER $$
CREATE TRIGGER `trg_matricola_insert` 
BEFORE INSERT ON `tcontatti` 
FOR EACH ROW 
BEGIN    
    IF NOT NEW.matricola REGEXP '^[A-Z]{2}[0-9]{3}$' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'matricola';
    END IF;
END$$
DELIMITER ;

-- Trigger per controllare la lunghezza del nome e cognome
DROP TRIGGER IF EXISTS `trg_nome_cognome_insert`;
DELIMITER $$
CREATE TRIGGER `trg_nome_cognome_insert` 
BEFORE INSERT ON `tcontatti` 
FOR EACH ROW 
BEGIN
	IF CHAR_LENGTH(NEW.nome) < 3 AND CHAR_LENGTH(NEW.cognome) < 3 THEN
    	SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'entrmabi';
     END IF;   
    IF CHAR_LENGTH(NEW.nome) < 3 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'nome';
    END IF;
    IF CHAR_LENGTH(NEW.cognome) < 3 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'cognome';
    END IF;
END$$
DELIMITER ;

-- Creazione della tabella operatori telefonici
DROP TABLE IF EXISTS `toperatori`;
CREATE TABLE IF NOT EXISTS `toperatori` (
    id_operatori INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(20),
    INDEX idx_operatori (nome)
) ENGINE=InnoDB;

-- Creazione della tabella telefoni
DROP TABLE IF EXISTS `ttelefoni`;
CREATE TABLE IF NOT EXISTS `ttelefoni` (
    id  BIGINT AUTO_INCREMENT PRIMARY KEY,
    numero       CHAR(10) NOT NULL,
    tipo         ENUM('Personale', 'Casa', 'Lavoro') NOT NULL, -- Personale, Casa, Lavoro
    contatto_id  BIGINT NULL, 
    operatore_id INT NOT NULL,
    INDEX idx_telefoni (numero),
    FOREIGN KEY (contatto_id) REFERENCES `tcontatti` (id_contatti)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    FOREIGN KEY (operatore_id) REFERENCES `toperatori` (id_operatori)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Trigger per validare il numero di telefono
DROP TRIGGER IF EXISTS `trg_numero_insert`;
DELIMITER $$
CREATE TRIGGER `trg_numero_insert` 
BEFORE INSERT ON `ttelefoni` 
FOR EACH ROW 
BEGIN   
    IF NOT NEW.numero REGEXP '^[0-9]{10}$' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'numero';
    END IF;
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `trg_operatore_esiste`;
DELIMITER $$
CREATE TRIGGER `trg_operatore_esiste` 
BEFORE INSERT ON `ttelefoni` 
FOR EACH ROW 
BEGIN
    DECLARE operatore_count INT;
    SELECT COUNT(*) INTO operatore_count FROM `toperatori` WHERE id_operatori = NEW.operatore_id;
    IF operatore_count = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'operatore_inesistente';
    END IF;
END$$

DELIMITER ;
