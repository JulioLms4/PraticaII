CREATE DATABASE praticaII;
USE praticaII;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    cpf VARCHAR(11) UNIQUE NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(15)
);

CREATE TABLE funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    email VARCHAR(100),
    telefone VARCHAR(15)
);

CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    funcionario_id INT DEFAULT NULL,
    descricao TEXT NOT NULL,
    urgencia ENUM('baixa', 'media', 'alta') NOT NULL,
    status ENUM('pendente', 'em andamento', 'finalizada') DEFAULT 'pendente',
    data_abertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);
