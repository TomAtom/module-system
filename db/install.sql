-- -----------------------------------------------------
-- Table `system_roles`
-- -----------------------------------------------------
CREATE TABLE `system_roles` (
  `id_role` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(25) NOT NULL ,
  PRIMARY KEY (`id_role`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `system_users`
-- -----------------------------------------------------
CREATE TABLE `system_users` (
  `id_user` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `surname` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(25) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `password` VARCHAR(33) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `last_login` DATETIME NULL ,
  `id_role` MEDIUMINT UNSIGNED NOT NULL ,
  `is_admin` TINYINT(1) NOT NULL DEFAULT FALSE ,
  PRIMARY KEY (`id_user`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  INDEX `fk_users_roles1` (`id_role` ASC) ,
  CONSTRAINT `fk_users_roles1`
    FOREIGN KEY (`id_role` )
    REFERENCES `system_roles` (`id_role` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `photogallery_collections`
-- -----------------------------------------------------
CREATE TABLE `photogallery_collections` (
  `id_collection` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,
  `album_order` SMALLINT UNSIGNED NULL ,
  PRIMARY KEY (`id_collection`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `photogallery_photos`
-- -----------------------------------------------------
CREATE TABLE `photogallery_photos` (
  `id_photo` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `file_photo` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `file_thumb` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `datetime_insert` DATETIME NOT NULL ,
  `id_collection` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL ,
  `id_user` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `comment` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `border` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id_photo`) ,
  INDEX `fk_photos_collection` (`id_collection` ASC) ,
  INDEX `fk_photos_users1` (`id_user` ASC) ,
  CONSTRAINT `fk_photos_collection`
    FOREIGN KEY (`id_collection` )
    REFERENCES `photogallery_collections` (`id_collection` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_photos_users1`
    FOREIGN KEY (`id_user` )
    REFERENCES `system_users` (`id_user` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `guestbook_guestbook`
-- -----------------------------------------------------
CREATE TABLE `guestbook_guestbook` (
  `id_post` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(35) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(35) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,
  `web` VARCHAR(35) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,
  `date` DATETIME NOT NULL ,
  `text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `answer` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL ,
  PRIMARY KEY (`id_post`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `photogallery_photo_comments`
-- -----------------------------------------------------
CREATE TABLE `photogallery_photo_comments` (
  `id_comment` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_photo` MEDIUMINT UNSIGNED NOT NULL ,
  `date` DATETIME NOT NULL ,
  `text` TEXT NOT NULL ,
  `name` VARCHAR(35) NOT NULL ,
  `web` VARCHAR(35) NULL ,
  `email` VARCHAR(35) NULL ,
  `answer` TEXT NULL ,
  PRIMARY KEY (`id_comment`) ,
  INDEX `fk_photo_comments_1` (`id_photo` ASC) ,
  CONSTRAINT `fk_photo_comments_1`
    FOREIGN KEY (`id_photo` )
    REFERENCES `photogallery_photos` (`id_photo` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
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
  INDEX `fk_rights_roles1` (`id_role` ASC) ,
  CONSTRAINT `fk_rights_roles1`
    FOREIGN KEY (`id_role` )
    REFERENCES `system_roles` (`id_role` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `article_articles`
-- -----------------------------------------------------
CREATE TABLE `article_articles` (
  `id_article` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_user` MEDIUMINT UNSIGNED NOT NULL ,
  `is_visible` TINYINT(1) NOT NULL ,
  `datetime_create` DATETIME NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `content` TEXT NULL ,
  PRIMARY KEY (`id_article`) ,
  INDEX `fk_articles_system_users1` (`id_user` ASC) ,
  CONSTRAINT `fk_articles_system_users1`
    FOREIGN KEY (`id_user` )
    REFERENCES `system_users` (`id_user` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;



-- -----------------------------------------------------
-- Data for table `system_roles`
-- -----------------------------------------------------
START TRANSACTION;
USE `tomovofoto9`;
INSERT INTO `system_roles` (`id_role`, `name`) VALUES (1, 'host');
INSERT INTO `system_roles` (`id_role`, `name`) VALUES (2, 'administrátor');

COMMIT;

-- -----------------------------------------------------
-- Data for table `system_users`
-- -----------------------------------------------------
START TRANSACTION;
USE `tomovofoto9`;
INSERT INTO `system_users` (`id_user`, `name`, `surname`, `email`, `password`, `last_login`, `id_role`, `is_admin`) VALUES (NULL, '', '', 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, 2, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `system_rights`
-- -----------------------------------------------------
START TRANSACTION;
USE `tomovofoto9`;
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (1, 'index', NULL);
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', NULL);
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', NULL);
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'add', NULL);
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'index', NULL);
INSERT INTO `system_rights` (`id_role`, `action`, `controller`) VALUES (2, 'add', NULL);

COMMIT;

