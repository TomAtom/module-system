
-- -----------------------------------------------------
-- Table `system_users`
-- -----------------------------------------------------
CREATE TABLE `system_users` (
  `id_user` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `surname` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `password` VARCHAR(33) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `last_login` DATETIME NULL ,
  `is_admin` TINYINT(1) NOT NULL DEFAULT FALSE ,
  `is_active` TINYINT(1) NOT NULL DEFAULT TRUE ,
  PRIMARY KEY (`id_user`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `system_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `system_roles` (
  `id_role` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(25) NOT NULL ,
  PRIMARY KEY (`id_role`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `system_rights`
-- -----------------------------------------------------
CREATE TABLE `system_rights` (
  `id_role` MEDIUMINT UNSIGNED NOT NULL ,
  `action` VARCHAR(20) NOT NULL ,
  `controller` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`action`, `id_role`, `controller`) ,
  INDEX `fk_rights_roles1_idx` (`id_role` ASC) ,
  CONSTRAINT `fk_rights_roles1`
    FOREIGN KEY (`id_role` )
    REFERENCES `system_roles` (`id_role` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `system_users_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `system_users_roles` (
  `id_role` MEDIUMINT UNSIGNED NOT NULL ,
  `id_user` MEDIUMINT UNSIGNED NOT NULL ,
  INDEX `fk_system_users_roles_system_roles1_idx` (`id_role` ASC) ,
  INDEX `fk_system_users_roles_system_users1_idx` (`id_user` ASC) ,
  PRIMARY KEY (`id_role`, `id_user`) ,
  CONSTRAINT `fk_system_users_roles_system_roles1`
    FOREIGN KEY (`id_role` )
    REFERENCES `system_roles` (`id_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_users_roles_system_users1`
    FOREIGN KEY (`id_user` )
    REFERENCES `system_users` (`id_user` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Data for table `system_users`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_users` (`id_user`, `name`, `surname`, `email`, `password`, `last_login`, `is_admin`, `is_active`) VALUES (1, '', '', 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, TRUE, TRUE);

COMMIT;

-- -----------------------------------------------------
-- Data for table `system_roles`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_roles` (`id_role`, `name`) VALUES (1, 'host');
INSERT INTO `system_roles` (`id_role`, `name`) VALUES (2, 'administrátor');

COMMIT;

-- -----------------------------------------------------
-- Data for table `system_rights`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (1, 'index', 'Application\Controller\Index');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', 'System\Controller\Role');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', 'System\Controller\User');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'add', 'System\Controller\User');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', 'System\Controller\Authentification');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'add', 'System\Controller\Role');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'rights', 'System\Controller\Role');
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', 'Application\Controller\Index');

COMMIT;

-- -----------------------------------------------------
-- Data for table `system_users_roles`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_users_roles` (`id_role`, `id_user`) VALUES (2, 1);

COMMIT;
