<?php

require_once('../vendor/rutkoski/simplify/lib/simplify.php');
require_once('../vendor/rutkoski/simplify-thumb/lib/autoload.php');
require_once('../vendor/rutkoski/simplify-validation/lib/autoload.php');
require_once('../vendor/rutkoski/simplify-form/lib/autoload.php');

define('AMP_DIR', dirname(__file__));

$config['modules:simplify/amp:path'] = AMP_DIR;

s::router()->connect('groups', '/users/groups/:action',
  array('module' => 'simplify/amp', 'controller' => 'groups', 'action' => 'index')
);

s::router()->connect('permissions', '/users/permissions/:action',
  array('module' => 'simplify/amp', 'controller' => 'permissions', 'action' => 'index')
);

s::router()->connect('users', '/users/:action',
  array('module' => 'simplify/amp', 'controller' => 'users', 'action' => 'index')
);

s::router()->connect('home', '/:action',
  array('module' => 'simplify/amp', 'controller' => 'admin', 'action' => 'index'),
  array('action' => 'index|login|logout')
);

$config['view:helpers:top_menu'] = array(
  'class' => 'TopMenuHelper',
  'require' => AMP_DIR . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'TopMenuHelper.php',
);

$config['view:helpers:menu'] = array(
  'class' => 'MenuHelper',
  'require' => AMP_DIR . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'MenuHelper.php',
);

$config['view:helpers:icon'] = array(
  'class' => 'IconHelper',
  'require' => AMP_DIR . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'IconHelper.php',
);

if (file_exists(APP_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'amp.php')) {
  require_once(APP_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'amp.php');
}

if (! defined('ACL_USERS_TABLE')) define('ACL_USERS_TABLE', 'acl_users');
if (! defined('ACL_GROUPS_TABLE')) define('ACL_GROUPS_TABLE', 'acl_groups');
if (! defined('ACL_GROUPS_USERS_TABLE')) define('ACL_GROUPS_USERS_TABLE', 'acl_groups_users');
if (! defined('ACL_GROUPS_PERMISSIONS_TABLE')) define('ACL_GROUPS_PERMISSIONS_TABLE', 'acl_groups_permissions');
if (! defined('ACL_PERMISSIONS_TABLE')) define('ACL_PERMISSIONS_TABLE', 'acl_permissions');
if (! defined('ACL_PERMISSIONS_USERS_TABLE')) define('ACL_PERMISSIONS_USERS_TABLE', 'acl_permissions_users');
if (! defined('OPTIONS_TABLE')) define('OPTIONS_TABLE', 'options');

Simplify_Autoload::registerPath(AMP_DIR);
Simplify_Autoload::registerPath(AMP_DIR . DIRECTORY_SEPARATOR . 'core');

/**
 *
 *
 *
 */

s::app(new AmpApplicationController())->dispatch();
