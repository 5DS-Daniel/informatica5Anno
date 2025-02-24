
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB;

DELIMITER //
CREATE TRIGGER check_username_before_insert
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF LENGTH(NEW.username) < 3 OR LENGTH(NEW.username) > 20 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente deve avere tra 3 e 20 caratteri.';
    END IF;
    
    IF NEW.username NOT REGEXP '^[a-zA-Z0-9_]+$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente può contenere solo lettere, numeri e underscore.';
    END IF;
END;
//
DELIMITER;


DELIMITER //
CREATE TRIGGER check_email_before_insert
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.email NOT REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email non è valida.';
    END IF;
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER check_username_before_update
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF LENGTH(NEW.username) < 3 OR LENGTH(NEW.username) > 20 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente deve avere tra 3 e 20 caratteri.';
    END IF;
    
    IF NEW.username NOT REGEXP '^[a-zA-Z0-9_]+$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente può contenere solo lettere, numeri e underscore.';
    END IF;
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER check_email_before_update
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.email NOT REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email non è valida.';
    END IF;
END;
//
DELIMITER ;


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descrizione TEXT NOT NULL,
    prezzo DECIMAL(10,2) NOT NULL,
    immagine VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



INSERT INTO products (user_id, nome, descrizione, prezzo, immagine) VALUES
(17, 'Smartphone XYZ', 'Smartphone con schermo OLED e fotocamera 108MP.', 599.99, 'https://via.placeholder.com/200'),
(17, 'Laptop UltraBook', 'Notebook leggero e potente con processore Intel i7 e 16GB di RAM.', 999.99, 'https://via.placeholder.com/200'),
(17, 'Auricolari Wireless Pro', 'Auricolari con cancellazione attiva del rumore e autonomia di 24 ore.', 149.99, 'https://via.placeholder.com/200'),
(17, 'Smartwatch FitBand', 'Smartwatch con monitoraggio del battito cardiaco e GPS.', 199.99, 'https://via.placeholder.com/200'),
(17, 'Monitor 4K UltraHD', 'Monitor da 27 pollici con risoluzione 4K UltraHD e HDR.', 349.99, 'https://via.placeholder.com/200');
