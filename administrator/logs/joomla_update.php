#
#<?php die('Forbidden.'); ?>
#Date: 2022-06-02 08:24:32 UTC
#Software: Joomla! 4.0.6 Stable [ Furaha ] 15-January-2022 17:06 GMT

#Fields: datetime	priority clientip	category	message
2022-06-02T08:24:32+00:00	INFO ::1	update	Update started by user webspaceconfigdemo (552). Old version is 4.0.6.
2022-06-02T08:24:34+00:00	INFO ::1	update	Downloading update file from https://downloads.joomla.org/cms/joomla4/4-1-4/Joomla_4.1.4-Stable-Update_Package.zip.
2022-06-02T08:24:44+00:00	INFO ::1	update	File Joomla_4.1.4-Stable-Update_Package.zip downloaded.
2022-06-02T08:24:44+00:00	INFO ::1	update	Starting installation of new version.
2022-06-02T08:24:53+00:00	INFO ::1	update	Finalising installation.
2022-06-02T08:24:54+00:00	INFO ::1	update	Start of SQL updates.
2022-06-02T08:24:54+00:00	INFO ::1	update	The current database version (schema) is 4.0.6-2021-12-23.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-20. Query text: CREATE TABLE IF NOT EXISTS `#__scheduler_tasks` (   `id` int unsigned NOT NULL A.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-20. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-20. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-20. Query text: INSERT INTO `#__action_logs_extensions` (`extension`) VALUES ('com_scheduler');.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-20. Query text: INSERT INTO `#__action_log_config` (`type_title`, `type_alias`, `id_holder`, `ti.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-20. Query text: INSERT INTO `#__mail_templates` (`template_id`, `extension`, `language`, `subjec.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-28. Query text: UPDATE `#__template_styles` SET `inheritable` = 1 WHERE `template` = 'atum' AND .
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-11-28. Query text: UPDATE `#__template_styles`    SET `params` = REPLACE(`params`,'"useFontScheme":.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2021-12-29. Query text: INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2022-01-08. Query text: UPDATE `#__mail_templates`    SET `params` = '{"tags": ["task_id", "task_title"].
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2022-01-19. Query text: INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2022-01-24. Query text: ALTER TABLE `#__redirect_links` DROP INDEX `idx_link_modifed`;.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.0-2022-01-24. Query text: ALTER TABLE `#__redirect_links` ADD INDEX `idx_link_modified` (`modified_date`);.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.1-2022-02-20. Query text: DELETE FROM `#__postinstall_messages` WHERE `title_key` = 'COM_ADMIN_POSTINSTALL.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.3-2022-04-07. Query text: UPDATE `#__mail_templates`    SET `params` = '{"tags":["message","date","extensi.
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.3-2022-04-07. Query text: UPDATE `#__mail_templates`    SET `params` = '{"tags":["sitename","name","email".
2022-06-02T08:24:54+00:00	INFO ::1	update	Ran query from file 4.1.3-2022-04-08. Query text: UPDATE `#__update_sites`    SET `name` = 'Joomla! Update Component'  WHERE `name.
2022-06-02T08:24:54+00:00	INFO ::1	update	End of SQL updates.
2022-06-02T08:24:54+00:00	INFO ::1	update	Deleting removed files and folders.
2022-06-02T08:24:56+00:00	INFO ::1	update	Cleaning up after installation.
2022-06-02T08:24:56+00:00	INFO ::1	update	Update to version 4.1.4 is complete.
