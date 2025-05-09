-- MySQL Script generated by MySQL Workbench
-- Sun Dec  1 16:07:34 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema eco
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema eco
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `eco` DEFAULT CHARACTER SET utf8mb3 ;
USE `eco` ;

-- -----------------------------------------------------
-- Table `eco`.`endereco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`endereco` (
  `cep` VARCHAR(8) NOT NULL,
  `estado` VARCHAR(45) NOT NULL,
  `cidade` VARCHAR(45) NOT NULL,
  `bairro` VARCHAR(45) NOT NULL,
  `logradouro` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`cep`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`empresa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`empresa` (
  `cnpj` VARCHAR(14) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `telefone` VARCHAR(11) NOT NULL,
  `senha` VARCHAR(45) NOT NULL,
  `endereco_cep` VARCHAR(8) NOT NULL,
  PRIMARY KEY (`cnpj`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `telefone_UNIQUE` (`telefone` ASC) VISIBLE,
  INDEX `fk_empresa_endereco1_idx` (`endereco_cep` ASC) VISIBLE,
  CONSTRAINT `fk_empresa_endereco1`
    FOREIGN KEY (`endereco_cep`)
    REFERENCES `eco`.`endereco` (`cep`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`admin` (
  `codigo` VARCHAR(6) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `telefone` VARCHAR(45) NOT NULL,
  `empresa_cnpj` VARCHAR(14) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`codigo`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `telefone_UNIQUE` (`telefone` ASC) VISIBLE,
  INDEX `fk_admin_empresa1_idx` (`empresa_cnpj` ASC) VISIBLE,
  CONSTRAINT `fk_admin_empresa1`
    FOREIGN KEY (`empresa_cnpj`)
    REFERENCES `eco`.`empresa` (`cnpj`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`usuario` (
  `cpf` VARCHAR(11) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `nickname` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `telefone` VARCHAR(11) NULL DEFAULT NULL,
  `senha` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`cpf`),
  UNIQUE INDEX `usuariocol_UNIQUE` (`nickname` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`denuncia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`denuncia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(45) NOT NULL,
  `descricao` TEXT NOT NULL,
  `status` ENUM('pendente', 'em analise', 'concluido') NULL DEFAULT NULL,
  `anexo` VARCHAR(1000) NULL DEFAULT NULL,
  `data_criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_cpf` VARCHAR(11) NOT NULL,
  `endereco_cep` VARCHAR(8) NOT NULL,
  `prioridade` ENUM('alta', 'media', 'baixa') NULL DEFAULT 'media',
  PRIMARY KEY (`id`),
  INDEX `fk_denuncia_usuario_idx` (`usuario_cpf` ASC) VISIBLE,
  INDEX `fk_denuncia_endereco1_idx` (`endereco_cep` ASC) VISIBLE,
  CONSTRAINT `fk_denuncia_endereco1`
    FOREIGN KEY (`endereco_cep`)
    REFERENCES `eco`.`endereco` (`cep`),
  CONSTRAINT `fk_denuncia_usuario`
    FOREIGN KEY (`usuario_cpf`)
    REFERENCES `eco`.`usuario` (`cpf`),
  CONSTRAINT `fk_endereco_cep`
    FOREIGN KEY (`endereco_cep`)
    REFERENCES `eco`.`endereco` (`cep`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_usuario_cpf`
    FOREIGN KEY (`usuario_cpf`)
    REFERENCES `eco`.`usuario` (`cpf`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 110
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`atendimento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`atendimento` (
  `protocolo` INT NOT NULL AUTO_INCREMENT,
  `data_atendimento` DATE NOT NULL,
  `status` ENUM('aberto', 'em atendimento', 'atendida') NOT NULL DEFAULT 'aberto',
  `admin_codigo` VARCHAR(6) NOT NULL,
  `denuncia_id` INT NOT NULL,
  PRIMARY KEY (`protocolo`),
  INDEX `fk_atendimento_admin1_idx` (`admin_codigo` ASC) VISIBLE,
  INDEX `fk_atendimento_denuncia1_idx` (`denuncia_id` ASC) VISIBLE,
  CONSTRAINT `fk_atendimento_admin1`
    FOREIGN KEY (`admin_codigo`)
    REFERENCES `eco`.`admin` (`codigo`),
  CONSTRAINT `fk_atendimento_denuncia1`
    FOREIGN KEY (`denuncia_id`)
    REFERENCES `eco`.`denuncia` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`jogos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`jogos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `genero` VARCHAR(45) NOT NULL,
  `descricao` TEXT NULL DEFAULT NULL,
  `data_lancamento` DATE NULL DEFAULT NULL,
  `data_criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `desenvolvedor` VARCHAR(100) NULL DEFAULT NULL,
  `editor` VARCHAR(100) NULL DEFAULT NULL,
  `link_jogo` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_nome` (`nome` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 1746
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`avaliacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`avaliacoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_cpf` VARCHAR(11) NOT NULL,
  `jogos_id` INT NOT NULL,
  `avaliacao` ENUM('1', '2', '3', '4', '5') NOT NULL,
  `comentario` TEXT NULL DEFAULT NULL,
  `data_criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_avaliacoes_usuario1_idx` (`usuario_cpf` ASC) VISIBLE,
  INDEX `fk_avaliacoes_jogos1_idx` (`jogos_id` ASC) VISIBLE,
  CONSTRAINT `fk_avaliacoes_jogos1`
    FOREIGN KEY (`jogos_id`)
    REFERENCES `eco`.`jogos` (`id`),
  CONSTRAINT `fk_avaliacoes_usuario1`
    FOREIGN KEY (`usuario_cpf`)
    REFERENCES `eco`.`usuario` (`cpf`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`hierarquia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`hierarquia` (
  `admin_codigo` VARCHAR(6) NOT NULL,
  `hierarquia` ENUM('1', '2', '3', '4', '5') NULL DEFAULT NULL,
  PRIMARY KEY (`admin_codigo`),
  INDEX `fk_hierarquia_admin1_idx` (`admin_codigo` ASC) VISIBLE,
  CONSTRAINT `fk_hierarquia_admin1`
    FOREIGN KEY (`admin_codigo`)
    REFERENCES `eco`.`admin` (`codigo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`horarios_coleta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`horarios_coleta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `dia_semana` ENUM('segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo') NULL DEFAULT NULL,
  `turno` ENUM('matutino', 'vespertino', 'noturno') NULL DEFAULT NULL,
  `endereco_cep` VARCHAR(8) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `endereco_cep` (`endereco_cep` ASC) VISIBLE,
  CONSTRAINT `horarios_coleta_ibfk_1`
    FOREIGN KEY (`endereco_cep`)
    REFERENCES `eco`.`endereco` (`cep`))
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`publicacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`publicacoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `conteudo` TEXT NOT NULL,
  `data_publicacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('ativo', 'inativo', 'rascunho') NULL DEFAULT 'ativo',
  `data_criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `link` VARCHAR(999) NULL DEFAULT NULL,
  `imagem` VARCHAR(999) NULL DEFAULT NULL,
  `admin_codigo` VARCHAR(6) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_publicacoes_admin1_idx` (`admin_codigo` ASC) VISIBLE,
  CONSTRAINT `fk_publicacoes_admin1`
    FOREIGN KEY (`admin_codigo`)
    REFERENCES `eco`.`admin` (`codigo`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`reset_senha`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`reset_senha` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `data_criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`resposta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`resposta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mensagem` TEXT NOT NULL,
  `atendimento_protocolo` INT NOT NULL,
  `data_resposta` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_resposta_atendimento1_idx` (`atendimento_protocolo` ASC) VISIBLE,
  CONSTRAINT `fk_resposta_atendimento1`
    FOREIGN KEY (`atendimento_protocolo`)
    REFERENCES `eco`.`atendimento` (`protocolo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `eco`.`tentativas_login`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eco`.`tentativas_login` (
  `ip` VARCHAR(45) NOT NULL,
  `tentativas` INT NULL DEFAULT '0',
  `ultimo_login` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;

USE `eco`;

DELIMITER $$
USE `eco`$$
CREATE
DEFINER=`root`@`localhost`
TRIGGER `eco`.`aplicar_atendimento`
AFTER INSERT ON `eco`.`atendimento`
FOR EACH ROW
BEGIN
    -- Atualiza o status da denúncia correspondente
    UPDATE `eco`.`denuncia`
    SET `status` = 'em analise'
    WHERE `id` = NEW.denuncia_id AND `status` = 'pendente';
END$$


DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
