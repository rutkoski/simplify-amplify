<?php

/**
 * Amplify
 *
 * This file is part of Amplify.
 *
 * Amplify is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Amplify is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
namespace Amplify;

/**
 */
class Install
{

  /**
   * Check is amp is intalled
   *
   * @return boolean
   */
  public static function installed()
  {
    try {
      $found = \Simplify::db()->query()->select('COUNT(user_id)')->from(\Simplify::config()->get('amp:tables:users'))->where('user_id = ?')->execute(1)->fetchOne();
      
      return $found;
    }
    catch (\Simplify\Db\TableNotFoundException $e) {
      return false;
    }
  }

  /**
   * Installs amp and create admin user
   *
   * @param string $email          
   * @param string $password          
   */
  public static function performInstall($email, $password)
  {
    $_email = new \Simplify\Validation\Email('Invalid email', 'Enter your email');
    $_email->validate($email);
    
    $_passw = new \Simplify\Validation\Regex('Invalid password', '/^[a-zA-Z0-9]{4,}$/');
    $_passw->validate($password);
    
    self::upgrade();
    
    $permissions = array(
        array(
            'admin',
            'Global admin rights'
        ),
        array(
            'manage_accounts',
            'Manage user accounts'
        ),
        array(
            'manage_groups',
            'Manage groups'
        ),
        array(
            'manage_permissions',
            'Manage user and group permissions'
        )
    );
    
    foreach ($permissions as $permission) {
      \Amplify\Account::createPermission($permission[0], $permission[1]);
    }
    
    $user_id = \Amplify\Account::createUser($email, $password);
    
    \Amplify\Account::addUserPermission($user_id, 'admin');
    
    \Amplify\Options::create('site_name', 'Amplify');
  }

  /**
   * Upgrade database tables to last version
   */
  protected static function upgrade()
  {
    $query = 
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:options') . '` (
          `option_id` mediumint(8) NOT NULL AUTO_INCREMENT,
          `option_name` varchar(255) NOT NULL,
          `option_value` text NOT NULL,
          `option_modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `option_autoload` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`option_id`),
          UNIQUE KEY `option_name` (`option_name`),
          KEY `option_autoload` (`option_autoload`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

    \Simplify::db()->query($query)->executeRaw();

    $version = intval(\Amplify\Options::value('db_version', 0));
    
    if ($version <= 0) {
      $queries = array(
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:groups') . '` (
          `group_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
          `group_name` varchar(255) NOT NULL,
          PRIMARY KEY (`group_id`),
          UNIQUE KEY `group_name` (`group_name`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
          
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:groups_permissions') . '` (
          `group_id` mediumint(8) unsigned NOT NULL,
          `permission_id` mediumint(8) unsigned NOT NULL,
          UNIQUE KEY `group_id_permission_id` (`group_id`,`permission_id`),
          KEY `FK_groups_permissions_permissions` (`permission_id`),
          CONSTRAINT `FK_groups_permissions_groups` FOREIGN KEY (`group_id`) REFERENCES `' . \Simplify::config()->get('amp:tables:groups') . '` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_groups_permissions_permissions` FOREIGN KEY (`permission_id`) REFERENCES `' . \Simplify::config()->get('amp:tables:permissions') . '` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
          
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:groups_users') . '` (
          `user_id` mediumint(8) unsigned NOT NULL,
          `group_id` mediumint(8) unsigned NOT NULL,
          UNIQUE KEY `user_id_group_id` (`user_id`,`group_id`),
          KEY `FK_groups_users_groups` (`group_id`),
          CONSTRAINT `FK_groups_users_groups` FOREIGN KEY (`group_id`) REFERENCES `' . \Simplify::config()->get('amp:tables:groups') . '` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_groups_users_users` FOREIGN KEY (`user_id`) REFERENCES `' . \Simplify::config()->get('amp:tables:users') . '` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;',
          
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:permissions') . '` (
          `permission_id` mediumint(255) unsigned NOT NULL AUTO_INCREMENT,
          `permission_name` varchar(255) NOT NULL,
          `permission_description` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`permission_id`),
          UNIQUE KEY `permission_name` (`permission_name`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
          
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:permissions_users') . '` (
          `user_id` mediumint(8) unsigned NOT NULL,
          `permission_id` mediumint(8) unsigned NOT NULL,
          UNIQUE KEY `user_id_permission_id` (`user_id`,`permission_id`),
          KEY `FK_permissions_users_permissions` (`permission_id`),
          CONSTRAINT `FK_permissions_users_permissions` FOREIGN KEY (`permission_id`) REFERENCES `' . \Simplify::config()->get('amp:tables:permissions') . '` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_permissions_users_users` FOREIGN KEY (`user_id`) REFERENCES `' . \Simplify::config()->get('amp:tables:users') . '` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
          
          'CREATE TABLE IF NOT EXISTS `' . \Simplify::config()->get('amp:tables:users') . '` (
          `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
          `user_email` varchar(255) NOT NULL,
          `user_password` varchar(40) NOT NULL,
          `access_token` varchar(40) DEFAULT NULL,
          PRIMARY KEY (`user_id`),
        	INDEX `user_password` (`user_password`),
        	INDEX `access_token` (`access_token`),
          UNIQUE KEY `user_email` (`user_email`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
      );
      
      \Simplify::db()->beginTransaction();
      \Simplify::db()->query('SET FOREIGN_KEY_CHECKS=0')->executeRaw();
      foreach ($queries as $query) {
        \Simplify::db()->query($query)->executeRaw();
      }
      \Simplify::db()->commit();
    }
  }

}
