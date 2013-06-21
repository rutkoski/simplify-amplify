<?php

class Model_Migrations_AmpMigrations extends Model_Migrations
{

  public function execute($version)
  {
    if ($version == 0) {
      $queries = array(
        'CREATE TABLE IF NOT EXISTS `'.ACL_GROUPS_TABLE.'` (
          `group_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
          `group_name` varchar(255) NOT NULL,
          PRIMARY KEY (`group_id`),
          UNIQUE KEY `group_name` (`group_name`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `'.ACL_GROUPS_PERMISSIONS_TABLE.'` (
          `group_id` mediumint(8) unsigned NOT NULL,
          `permission_id` mediumint(8) unsigned NOT NULL,
          UNIQUE KEY `group_id_permission_id` (`group_id`,`permission_id`),
          KEY `permission_id` (`permission_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `'.ACL_GROUPS_USERS_TABLE.'` (
          `user_id` mediumint(8) unsigned NOT NULL,
          `group_id` mediumint(8) unsigned NOT NULL,
          UNIQUE KEY `user_id_group_id` (`user_id`,`group_id`),
          KEY `group_id` (`group_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;',

        'CREATE TABLE IF NOT EXISTS `'.ACL_PERMISSIONS_TABLE.'` (
          `permission_id` mediumint(255) unsigned NOT NULL AUTO_INCREMENT,
          `permission_name` varchar(255) NOT NULL,
          `permission_description` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`permission_id`),
          UNIQUE KEY `permission_name` (`permission_name`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `'.ACL_PERMISSIONS_USERS_TABLE.'` (
          `user_id` mediumint(8) unsigned NOT NULL,
          `permission_id` mediumint(8) unsigned NOT NULL,
          UNIQUE KEY `user_id_permission_id` (`user_id`,`permission_id`),
          KEY `permission_id` (`permission_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `'.OPTIONS_TABLE.'` (
          `option_id` mediumint(8) NOT NULL AUTO_INCREMENT,
          `option_name` varchar(255) NOT NULL,
          `option_value` text NOT NULL,
          `option_modified_at` datetime NOT NULL,
          `option_autoload` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`option_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `'.ACL_USERS_TABLE.'` (
          `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
          `user_email` varchar(255) NOT NULL,
          `user_password` varchar(40) NOT NULL,
          `access_token` varchar(40) DEFAULT NULL,
          PRIMARY KEY (`user_id`),
          UNIQUE KEY `user_email` (`user_email`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;',

        'ALTER TABLE `'.ACL_GROUPS_PERMISSIONS_TABLE.'`
          ADD CONSTRAINT `acl_groups_permissions_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `'.ACL_GROUPS_TABLE.'` (`group_id`) ON DELETE CASCADE,
          ADD CONSTRAINT `acl_groups_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `'.ACL_PERMISSIONS_TABLE.'` (`permission_id`) ON DELETE CASCADE;',

        'ALTER TABLE `'.ACL_GROUPS_USERS_TABLE.'`
          ADD CONSTRAINT `acl_groups_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `'.ACL_USERS_TABLE.'` (`user_id`) ON DELETE CASCADE,
          ADD CONSTRAINT `acl_groups_users_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `'.ACL_GROUPS_TABLE.'` (`group_id`) ON DELETE CASCADE;',

        'ALTER TABLE `'.ACL_PERMISSIONS_USERS_TABLE.'`
          ADD CONSTRAINT `acl_permissions_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `'.ACL_USERS_TABLE.'` (`user_id`) ON DELETE CASCADE,
          ADD CONSTRAINT `acl_permissions_users_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `'.ACL_PERMISSIONS_TABLE.'` (`permission_id`) ON DELETE CASCADE;',

        'INSERT INTO `'.OPTIONS_TABLE.'` (`option_id`, `option_name`, `option_value`, `option_autoload`)
          VALUES (1, \'db_version\', \'1\', 1)'
      );

      foreach ($queries as $query) {
        s::db()->query($query)->execute();
      }
    }
  }

}
