ALTER TABLE `system_users` 
ADD COLUMN `datetime_create` DATETIME NOT NULL DEFAULT '1970-01-01';

ALTER TABLE `system_users` 
CHANGE COLUMN `datetime_create` `datetime_create` DATETIME NOT NULL ;