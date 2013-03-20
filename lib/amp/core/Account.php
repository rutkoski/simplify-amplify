<?php

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
   * @return void
   */
  public static function validate($permissions = null)
  {
    $user = self::getUser();

    if (empty($user)) {
      throw new LoginRequiredException('Login required');
    }

    if (intval($user['user_id']) === 1) {
      return;
    }

    if (! empty($permissions)) {
      $permissions = (array) $permissions;

      $perm = array_intersect($permissions, self::$acl);

      if (count($perm) != count($permissions)) {
        throw new SecurityException("You don't have permission to access this area");
      }
    }
  }

  /**
   *
   * @return array
   */
  public static function getUser()
  {
    if (is_null(self::$user)) {
      $token = s::session()->get('access_token');

      if (! empty($token)) {
        $user = s::db()->query()->from(ACL_USERS_TABLE)->where('access_token = ?')->execute($token)->fetchRow();

        if ($user && $token == self::generateAccessToken($user)) {
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
      FROM '.ACL_PERMISSIONS_TABLE.' p
      INNER JOIN (
        SELECT gp.permission_id
          FROM '.ACL_USERS_TABLE.' u
          LEFT JOIN '.ACL_GROUPS_USERS_TABLE.' ug ON (ug.user_id = u.user_id)
          LEFT JOIN '.ACL_GROUPS_PERMISSIONS_TABLE.' gp ON (ug.group_id = gp.group_id)
          WHERE u.user_id = ?
        UNION
        SELECT up.permission_id
          FROM '.ACL_PERMISSIONS_USERS_TABLE.' up
          WHERE up.user_id = ?
      ) q ON (p.permission_id = q.permission_id)
    ';

    self::$acl = s::db()->query($sql)->execute($user['user_id'], $user['user_id'])->fetchCol();
  }

  /**
   *
   * @return void
   */
  public static function requestPasswordChange($email)
  {
    global $config;

    $user = s::db()->query()->from(ACL_USERS_TABLE)->where('user_email = ?')->execute($email)->fetchRow();

    if (empty($user)) {
      throw new Simplify_Validation_ValidationException('User not found');
    }

    $user['auth_token'] = sy_random_string(10);

    Simplify_Db_TableDataGateway::getInstance(ACL_USERS_TABLE, 'user_id')->save($user);

    $mail = new Simplify_Mail();
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
    $user = s::db()->query()->from(ACL_USERS_TABLE)
      ->where('user_email = :email AND auth_token = :auth')
      ->execute(array('email' => $email, 'auth' => $auth))->fetchRow();

    if (empty($user)) {
      throw new Simplify_Validation_ValidationException('User not found or wrong authorization code.');
    }

    $pass_a = self::hash($pass_a);
    $pass_b = self::hash($pass_b);
    $empty = self::hash('');

    if ($pass_a == $empty || $pass_a != $pass_b) {
      throw new Simplify_Validation_ValidationException('Invalid password or passwords do not match.');
    }

    $user['user_password'] = $pass_a;
    $user['auth_token'] = '';

    Simplify_Db_TableDataGateway::getInstance(ACL_USERS_TABLE, 'user_id')->save($user);
  }

  /**
   *
   * @param array $user
   * @return unknown_type
   */
  public static function setUser($user)
  {
    self::$user = $user;
  }

  /**
   *
   * @param string $username
   * @param string $password
   * @return array
   */
  public static function login($email, $password)
  {
    $password = self::hash($password);

    $user = s::db()->query()->from(ACL_USERS_TABLE)
      ->where('user_email = :email and user_password = :password')
      ->execute(array('email' => $email, 'password' => $password))->fetchRow();

    if (empty($user)) {
      throw new LoginException('Wrong username/password');
    }

    $user['access_token'] = self::generateAccessToken($user);

    $data = array('user_id' => $user['user_id'], 'access_token' => $user['access_token']);

    s::db()->update(ACL_USERS_TABLE, array('access_token' => 1), 'user_id = :user_id')->execute($data);

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
    return md5($password);
  }

  /**
   *
   * @return void
   */
  public static function logout()
  {
    s::session()->del('access_token');
    s::session()->destroy();

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

    s::session()->set('access_token', $token);

    self::setUser($user);
    self::loadUserAcl($user);
  }

  public static function createUser($email, $password)
  {
    $data = array(
      'user_email' => $email,
      'user_password' => self::hash($password)
    );

    $user_id = s::db()->query()->from(ACL_USERS_TABLE)->select('user_id')->where('user_email = ?')->execute($email)->fetchOne();

    if (! empty($user_id)) {
      throw new LoginException('User already exists');
    }

    s::db()->insert(ACL_USERS_TABLE, $data)->execute($data);

    $user_id = s::db()->lastInsertId();

    return $user_id;
  }

  public static function createGroup($name)
  {
    $data = array(
      'group_name' => $name
    );

    $group_id = s::db()->query()->from(ACL_GROUPS_TABLE)->select('group_id')->where('group_name = :group_name')->execute($data)->fetchOne();

    if (empty($group_id)) {
      s::db()->insert(ACL_GROUPS_TABLE, $data)->execute($data);

      $group_id = s::db()->lastInsertId();
    }

    return $group_id;
  }

  public static function createPermission($name, $description = '')
  {
    $data = array(
      'permission_name' => $name,
      'permission_description' => $description
    );

    $permission_id = s::db()->query()->from(ACL_PERMISSIONS_TABLE)->select('permission_id')->where('permission_name = ?')->execute($name)->fetchOne();

    if (empty($permission_id)) {
      s::db()->insert(ACL_PERMISSIONS_TABLE, $data)->execute($data);

      $permission_id = s::db()->lastInsertId();
    }

    return $permission_id;
  }

  public static function addUserGroup($user_id, $group_id)
  {
    $data = array(
      'group_id' => $group_id,
      'user_id' => $user_id
    );

    s::db()->query('INSERT IGNORE INTO '.ACL_GROUPS_USERS_TABLE.' VALUES (:group_id, :user_id)')->execute($data);
  }

  public static function addUserPermission($user_id, $permission_id)
  {
    $data = array(
      'permission_id' => $permission_id,
      'user_id' => $user_id
    );

    s::db()->query('INSERT IGNORE INTO '.ACL_PERMISSIONS_USERS_TABLE.' VALUES (:permission_id, :user_id)')->execute($data);
  }

  public static function addGroupPermission($group_id, $permission_id)
  {
    $data = array(
      'permission_id' => $permission_id,
      'group_id' => $group_id
    );

    s::db()->query('INSERT IGNORE INTO '.ACL_GROUPS_PERMISSIONS_TABLE.' VALUES (:permission_id, :group_id)')->execute($data);
  }

}
