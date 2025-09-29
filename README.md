utf8mb4_unicode_ci;

USE oficina_db;

CREATE TABLE IF NOT EXISTS sobre (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    foto_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

user: teste
pass: 123
