SELECT id_avion, COUNT(*) AS total, 
       SUM(CASE WHEN date_cloture IS NULL THEN 1 ELSE 0 END) AS open
FROM defaut
GROUP BY id_avion;

CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(100),
    role VARCHAR(50),
    action VARCHAR(255),
    table_name VARCHAR(50),
    item_id INT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM audit_log;
UPDATE users SET password='$2y$10$op9XKYPsgBleAgIrB156Gutb2SOj4/bfakJXFFIQ228e/crbI9taC' WHERE username='admin';