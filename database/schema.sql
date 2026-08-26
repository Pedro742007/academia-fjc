-- ============================================================
-- Academia FJC - Schema para Vercel + MySQL
-- Execute este SQL no phpMyAdmin ou console MySQL
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Tabela de sessões (serverless-safe)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `data` MEDIUMBLOB NOT NULL,
    `timestamp` INT UNSIGNED NOT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(512) DEFAULT NULL,
    INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela de cursos
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `alunos`;
DROP TABLE IF EXISTS `usuarios`;

DROP TABLE IF EXISTS `cursos`;
CREATE TABLE `cursos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `descricao` TEXT,
    `valor_mensalidade` DECIMAL(10,2) DEFAULT 0.00,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela de alunos
-- ------------------------------------------------------------
CREATE TABLE `alunos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `numero_aluno` VARCHAR(20) UNIQUE NOT NULL,
    `tipo_documento` ENUM('BI', 'Cedula', 'Passaporte') NOT NULL,
    `numero_documento` VARCHAR(90) NOT NULL,
    `nome_completo` VARCHAR(200) NOT NULL,
    `data_nascimento` DATE NOT NULL,
    `idade` TINYINT UNSIGNED,
    `morada` TEXT,
    `provincia` VARCHAR(100),
    `municipio` VARCHAR(100),
    `bairro` VARCHAR(100),
    `possui_irmao` TINYINT(1) DEFAULT 0,
    `nome_irmao` VARCHAR(200),
    `responsavel1_nome` VARCHAR(200),
    `responsavel1_parentesco` VARCHAR(100),
    `responsavel1_contacto` VARCHAR(50),
    `responsavel2_nome` VARCHAR(200),
    `responsavel2_parentesco` VARCHAR(100),
    `responsavel2_contacto` VARCHAR(50),
    `emergencia_nome` VARCHAR(200),
    `emergencia_telefone` VARCHAR(50),
    `curso_id` INT UNSIGNED,
    `data_inscricao` DATE NOT NULL,
    `valor_inscricao` DECIMAL(10,2) DEFAULT 0.00,
    `valor_entregue` DECIMAL(10,2) DEFAULT 0.00,
    `valor_pendente` DECIMAL(10,2) DEFAULT 0.00,
    `valor_total_pago` DECIMAL(10,2) DEFAULT 0.00,
    `observacoes` TEXT,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`) ON DELETE SET NULL,
    INDEX `idx_numero_aluno` (`numero_aluno`),
    INDEX `idx_nome` (`nome_completo`),
    INDEX `idx_documento` (`numero_documento`),
    INDEX `idx_curso` (`curso_id`),
    INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabela de usuários (senha com bcrypt cost 12)
-- Senha padrão: admin123  (TROQUE EM PRODUÇÃO!)
-- ------------------------------------------------------------
CREATE TABLE `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) UNIQUE NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `nivel` ENUM('admin', 'secretaria', 'professor') DEFAULT 'secretaria',
    `ativo` TINYINT(1) DEFAULT 1,
    `ultimo_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `usuarios` (`nome`, `email`, `senha`, `nivel`) VALUES
('Administrador', 'admin@academiafjc.com', '$2y$12$ZuV7ku.CTUwRCpVW8djzluSxlnTIlBG6KbY2VN7dEyIC3/oRqJTE2', 'admin');

-- ------------------------------------------------------------
-- Dados iniciais: cursos
-- ------------------------------------------------------------
INSERT INTO `cursos` (`nome`, `descricao`, `valor_mensalidade`) VALUES
('Guitarra', 'Guitarra elétrica e acústica', 130.00),
('Flauta', 'Flauta transversal e doce', 110.00),
('Canto', 'Técnica vocal e canto coral', 100.00),
('Piano', 'Piano e teclado', 150.00),
('Violão', 'Violão clássico e popular', 120.00),
('Bateria', 'Bateria e percussão', 130.00);

SET FOREIGN_KEY_CHECKS = 1;
