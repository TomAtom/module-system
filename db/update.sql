ALTER TABLE `system_users` CHANGE COLUMN `is_admin` `is_admin` TINYINT(1) NOT NULL DEFAULT FALSE  
, DROP INDEX `fk_users_roles1` 
, ADD INDEX `fk_users_roles1_idx` (`id_role` ASC) ;

ALTER TABLE `photogallery_photos` 
DROP INDEX `fk_photos_collection` 
, ADD INDEX `fk_photos_collection_idx` (`id_collection` ASC) 
, DROP INDEX `fk_photos_users1` 
, ADD INDEX `fk_photos_users1_idx` (`id_user` ASC) ;

ALTER TABLE `photogallery_photo_comments` 
DROP INDEX `fk_photo_comments_1` 
, ADD INDEX `fk_photo_comments_1_idx` (`id_photo` ASC) ;

ALTER TABLE `system_rights` 
DROP INDEX `fk_rights_roles1` 
, ADD INDEX `fk_rights_roles1_idx` (`id_role` ASC) ;

ALTER TABLE `system_users` DROP FOREIGN KEY `fk_users_roles1` ;

ALTER TABLE `system_users` DROP COLUMN `id_role` , CHANGE COLUMN `is_admin` `is_admin` TINYINT(1) NOT NULL DEFAULT FALSE  
, DROP INDEX `fk_users_roles1_idx` ;

ALTER TABLE `article_articles` 
ADD INDEX `fk_articles_system_users1_idx` (`id_user` ASC) 
, DROP INDEX `fk_articles_system_users1` ;

CREATE  TABLE IF NOT EXISTS `system_users_roles` (
  `id_role` MEDIUMINT(8) UNSIGNED NOT NULL ,
  `id_user` MEDIUMINT(8) UNSIGNED NOT NULL ,
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
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;