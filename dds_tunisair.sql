USE dds_tunisair;

-- DROP existing tables if any
DROP TABLE IF EXISTS limitation;
DROP TABLE IF EXISTS recap;
DROP TABLE IF EXISTS defaut;
DROP TABLE IF EXISTS avion;
DROP TABLE IF EXISTS detail;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS users;



CREATE TABLE avion (
    id_avion INT PRIMARY KEY AUTO_INCREMENT,
    code_avion VARCHAR(10) UNIQUE NOT NULL,
    station VARCHAR(50)
);

CREATE TABLE defaut (
    id_defaut INT PRIMARY KEY AUTO_INCREMENT,
    id_avion INT,
    numero_dds INT,
    defect TEXT,
    date_signalement DATE,
    situation VARCHAR(20),
    zone_ VARCHAR(50),
    flight_hours INT,
    flight_cycles INT,
    date_cloture DATE,
    technicien VARCHAR(100),
    oe_reference VARCHAR(50),
    work_order VARCHAR(50),
    closure_work_order VARCHAR(50),
    part_number VARCHAR(50),
    expiry_condition VARCHAR(100),
    FOREIGN KEY (id_avion) REFERENCES avion(id_avion)
);

CREATE TABLE limitation (
    id_limitation INT PRIMARY KEY AUTO_INCREMENT,
    id_defaut INT,
    lim_fh INT,
    lim_fc INT,
    lim_day INT,
    reste_fh INT,
    reste_fc INT,
    reste_jours INT,
    fh_jour INT,
    fc_jour INT,
    date_param DATE,
    FOREIGN KEY (id_defaut) REFERENCES defaut(id_defaut)
);

CREATE TABLE recap (
    id_recap INT PRIMARY KEY AUTO_INCREMENT,
    id_avion INT,
    total_defauts INT,
    open_defauts INT,
    station VARCHAR(50),
    date_maj DATE,
    FOREIGN KEY (id_avion) REFERENCES avion(id_avion)
);


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    role_ ENUM('admin', 'technician', 'viewer') NOT NULL,
    password VARCHAR(255) NOT NULL ,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20)
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(20),
    table_name VARCHAR(50),
    record_id INT,
    details TEXT,
    timestamp DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO avion (id_avion, code_avion,station) VALUES (1, 'IFM','BTC');
INSERT INTO avion (id_avion, code_avion,station) VALUES (2, 'IFN','HANGAR');
INSERT INTO avion (id_avion, code_avion,station) VALUES (3, 'IMA','HANGAR');
INSERT INTO avion (id_avion, code_avion,station) VALUES (4, 'IMB','BTC');
INSERT INTO avion (id_avion, code_avion,station) VALUES (5, 'IMR','HANGAR');
INSERT INTO avion (id_avion, code_avion,station) VALUES (6, 'IMX','BTC');
INSERT INTO avion (id_avion, code_avion,station) VALUES (7, 'IMY','BTC');
INSERT INTO avion (id_avion, code_avion,station) VALUES (8, 'IMZ','BTC');


 INSERT INTO users (username, password,role_,full_name,email,phone)
 VALUES ('admin', '$2y$10$4jnDqsFy8YDuxjQF9wSAju8oxtWPLFCEgedvYzVnDiy8BIdHo5cpy','admin','admin no1','admin@http.org','99568563'); 

 INSERT INTO users (username, password,role_,full_name,email,phone)
VALUES ('ali abdi', '$2y$10$4jnDqsFy8YDuxjQF9wSAju8oxtWPLFCEgedvYzVnDiy8BIdHo5cpy','technician','aloulou','ali@http.org','25658695');

 INSERT INTO users (username, password,role_,full_name,email,phone)
VALUES ('ali', '$2y$10$4jnDqsFy8YDuxjQF9wSAju8oxtWPLFCEgedvYzVnDiy8BIdHo5cpy','viewer','ala','ala51@http.org','47581694');





SELECT * FROM avion;
SELECT * FROM recap;
SELECT * FROM users;
SELECT * FROM defaut;
SELECT * FROM limitation;