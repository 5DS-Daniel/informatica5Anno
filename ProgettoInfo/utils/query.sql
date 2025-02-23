
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
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

