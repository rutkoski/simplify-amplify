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

use Simplify\Password;

/**
 */
class Account
{

    /**
     *
     * @var array
     */
    protected static $user = null;

    /**
     *
     * @var array
     */
    protected static $acl;

    /**
     *
     * @var string
     */
    protected static $locale = 'pt';

    public static function setLocale($locale)
    {
        self::$locale = $locale;
    }

    public static function getLocale()
    {
        return self::$locale;
    }

    /**
     *
     * @param string|string[] $permissions            
     * @param boolean $return            
     * @throws LoginRequiredException if user not logged in
     * @throws SecurityException if user does not have the permissions
     * @return boolean
     */
    public static function validate($permissions = null, $return = false)
    {
        $user = self::getUser();
        
        if (empty($user)) {
            if ($return) {
                return false;
            }
            
            throw new LoginRequiredException('Você precisa fazer login');
        }
        
        if (intval($user['user_id']) === 1 || in_array('admin', self::$acl) !== false) {
            return true;
        }
        
        if (! empty($permissions)) {
            $permissions = (array) $permissions;
            
            $perm = array_intersect($permissions, self::$acl);
            
            if (count($perm) != count($permissions)) {
                if ($return) {
                    return false;
                }
                
                throw new SecurityException("Você não tem permissão para acessar esta área");
            }
        }
        
        return true;
    }

    /**
     *
     * @return array
     */
    public static function getUser()
    {
        if (is_null(self::$user)) {
            $token = session_id();
            
            if (! empty($token)) {
                $user = \Simplify::db()->query()
                    ->from(\Simplify::config()->get('amp:tables:users'))
                    ->select('user_id')
                    ->select('user_email')
                    ->where('access_token = ?')
                    ->execute($token)
                    ->fetchRow();
                
                if ($user) {
                    self::authenticate($user);
                } else {
                    self::$user = false;
                }
            }
        }
        
        return self::$user;
    }

    protected static function loadUserAcl($user)
    {
        $sql = '
      SELECT p.permission_name
      FROM ' . \Simplify::config()->get('amp:tables:permissions') . ' p
      INNER JOIN (
        SELECT gp.permission_id
          FROM ' . \Simplify::config()->get('amp:tables:users') . ' u
          LEFT JOIN ' . \Simplify::config()->get('amp:tables:groups_users') . ' ug ON (ug.user_id = u.user_id)
          LEFT JOIN ' . \Simplify::config()->get('amp:tables:groups_permissions') . ' gp ON (ug.group_id = gp.group_id)
          WHERE u.user_id = ?
        UNION
        SELECT up.permission_id
          FROM ' . \Simplify::config()->get('amp:tables:permissions_users') . ' up
          WHERE up.user_id = ?
      ) q ON (p.permission_id = q.permission_id)
    ';
        
        self::$acl = \Simplify::db()->query($sql)
            ->execute($user['user_id'], $user['user_id'])
            ->fetchCol();
    }

    /**
     *
     * @return void
     */
    public static function requestPasswordChange($email)
    {
        global $config;
        
        $user = \Simplify::db()->query()
            ->from(\Simplify::config()->get('amp:tables:users'))
            ->where('user_email = ?')
            ->execute($email)
            ->fetchRow();
        
        if (empty($user)) {
            throw new \Simplify\ValidationException('Usuário não encontrado');
        }
        
        $user['auth_token'] = sy_random_string(10);
        
        \Simplify\Db\TableDataGateway::getInstance(\Simplify::config()->get('amp:tables:users'), 'user_id')->save($user);
        
        $mail = new \Simplify\Mail();
        $mail->mailFrom = $config['mail_from'];
        $mail->htmlTemplate = 'password_recovery_html';
        $mail->send($email, 'Password Recovery', $user);
    }

    /**
     *
     * @return void
     */
    public static function changePassword($email, $auth, $pass_a, $pass_b)
    {
        $user = \Simplify::db()->query()
            ->from(\Simplify::config()->get('amp:tables:users'))
            ->where('user_email = :email AND auth_token = :auth')
            ->execute(array(
            'email' => $email,
            'auth' => $auth
        ))
            ->fetchRow();
        
        if (empty($user)) {
            throw new \Simplify\ValidationException('Usuário não encontrado ou código de autorização inválido.');
        }
        
        $pass_a = self::hash($pass_a);
        $pass_b = self::hash($pass_b);
        $empty = self::hash('');
        
        if ($pass_a == $empty || $pass_a != $pass_b) {
            throw new \Simplify\ValidationException('A senha informada é inválida ou não confere.');
        }
        
        $user['user_password'] = $pass_a;
        $user['auth_token'] = '';
        
        \Simplify\Db\TableDataGateway::getInstance(\Simplify::config()->get('amp:tables:users'), 'user_id')->save($user);
    }

    /**
     *
     * @param array $user            
     * @return unknown_type
     */
    public static function setUser($user)
    {
        self::$user = $user;
        
        if (isset(self::$user['user_password'])) {
            unset(self::$user['user_password']);
        }
    }

    /**
     *
     * @param string $username            
     * @param string $password            
     * @return array
     */
    public static function login($username, $password)
    {
        $user = \Simplify::db()->query()
            ->from(\Simplify::config()->get('amp:tables:users'))
            ->where('(user_username = :username || user_email = :username)')
            ->execute(array(
            'username' => $username
        ))
            ->fetchRow();
        
        if (empty($user)) {
            throw new LoginException('Nome de usuário/email ou senha inválidos.');
        }
        
        $data = array(
            'user_id' => $user['user_id']
        );
        
        $fields = array();
        
        if (! Password::check($password, $user['user_password'])) {
            if ($user['user_password'] === md5($password)) {
                $data['user_password'] = self::hash($password);
                $fields['user_password'] = 1;
            } else {
                throw new LoginException('Nome de usuário/email ou senha inválidos.');
            }
        }
        
        $user['access_token'] = session_id();
        
        $data['access_token'] = $user['access_token'];
        $fields['access_token'] = 1;
        
        \Simplify::db()->update(\Simplify::config()->get('amp:tables:users'), $fields, 'user_id = :user_id')
            ->execute($data);
        
        self::authenticate($user);
        
        return self::getUser();
    }

    /**
     *
     * @return string
     */
    public static function generateAccessToken($user)
    {
        return self::hash($user['user_id'] . $user['user_email']);
    }

    /**
     *
     * @return string
     */
    public static function hash($password)
    {
        return Password::hash($password);
    }

    /**
     *
     * @return void
     */
    public static function logout()
    {
        \Simplify::session()->del('token');
        \Simplify::session()->destroy();
        
        self::setUser(null);
    }

    /**
     *
     * @param array $user            
     * @return void
     */
    public static function authenticate($user)
    {
        $token = self::generateAccessToken($user);
        
        \Simplify::session()->set('token', $token);
        
        self::setUser($user);
        self::loadUserAcl($user);
    }

    public static function createUser($username, $email, $password)
    {
        $data = array(
            'user_email' => $email,
            'user_username' => $username,
            'user_password' => self::hash($password)
        );
        
        $user_id = \Simplify::db()->query()
            ->from(\Simplify::config()->get('amp:tables:users'))
            ->select('user_id')
            ->where('user_email = ?')
            ->execute($email)
            ->fetchOne();
        
        if (empty($user_id)) {
            \Simplify::db()->insert(\Simplify::config()->get('amp:tables:users'), $data)
                ->execute($data);
            
            $user_id = \Simplify::db()->lastInsertId();
        }
        
        return $user_id;
    }

    public static function createGroup($name, $throw = true)
    {
        $data = array(
            'group_name' => $name
        );
        
        $group_id = \Simplify::db()->query()
            ->from(\Simplify::config()->get('amp:tables:groups'))
            ->select('group_id')
            ->where('group_name = :group_name')
            ->execute($data)
            ->fetchOne();
        
        if (empty($group_id)) {
            \Simplify::db()->insert(\Simplify::config()->get('amp:tables:groups'), $data)
                ->execute($data);
            
            $group_id = \Simplify::db()->lastInsertId();
        }
        
        return $group_id;
    }

    public static function createPermission($name, $description = '')
    {
        $data = array(
            'permission_name' => $name,
            'permission_description' => $description
        );
        
        $permission_id = \Simplify::db()->query()
            ->from(\Simplify::config()->get('amp:tables:permissions'))
            ->select('permission_id')
            ->where('permission_name = ?')
            ->execute($name)
            ->fetchOne();
        
        if (empty($permission_id)) {
            \Simplify::db()->insert(\Simplify::config()->get('amp:tables:permissions'), $data)
                ->execute($data);
            
            $permission_id = \Simplify::db()->lastInsertId();
        }
        
        return $permission_id;
    }

    public static function addUserGroup($user_id, $group_id)
    {
        if (is_string($group_id)) {
            $group_id = \Simplify::db()->query()
                ->from(\Simplify::config()->get('amp:tables:groups'))
                ->select('group_id')
                ->where('group_name = ?')
                ->execute($group_id)
                ->fetchOne();
            
            if (empty($group_id)) {
                throw new \Exception("Could not add user group: invalid group id");
            }
        }
        
        $data = array(
            'group_id' => $group_id,
            'user_id' => $user_id
        );
        
        \Simplify::db()->query('INSERT IGNORE INTO ' . \Simplify::config()->get('amp:tables:groups_users') . ' VALUES (:group_id, :user_id)')
            ->execute($data);
    }

    public static function addUserPermission($user_id, $permission_id)
    {
        if (is_string($permission_id)) {
            $permission_id = \Simplify::db()->query()
                ->from(\Simplify::config()->get('amp:tables:permissions'))
                ->select('permission_id')
                ->where('permission_name = ?')
                ->execute($permission_id)
                ->fetchOne();
            
            if (empty($permission_id)) {
                throw new \Exception("Could not add user permission: invalid permission id");
            }
        }
        
        $data = array(
            'permission_id' => $permission_id,
            'user_id' => $user_id
        );
        
        \Simplify::db()->query('INSERT IGNORE INTO ' . \Simplify::config()->get('amp:tables:permissions_users') . ' VALUES (:permission_id, :user_id)')
            ->execute($data);
    }

    public static function addGroupPermission($group_id, $permission_id)
    {
        if (is_string($permission_id)) {
            $permission_id = \Simplify::db()->query()
                ->from(\Simplify::config()->get('amp:tables:permissions'))
                ->select('permission_id')
                ->where('permission_name = ?')
                ->execute($permission_id)
                ->fetchOne();
            
            if (empty($permission_id)) {
                throw new \Exception("Could not add group permission: invalid permission id");
            }
        }
        
        $data = array(
            'permission_id' => $permission_id,
            'group_id' => $group_id
        );
        
        \Simplify::db()->query('INSERT IGNORE INTO ' . \Simplify::config()->get('amp:tables:groups_permissions') . ' VALUES (:permission_id, :group_id)')
            ->execute($data);
    }
}
