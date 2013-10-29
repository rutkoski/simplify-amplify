<?php

define('APP_DIR', dirname(__file__));

require_once ('vendor/autoload.php');

$config = s::config();

$config['view:helpers:top_menu'] = array('class' => 'TopMenuHelper', 'require' => APP_DIR . '/helper/TopMenuHelper.php');
$config['view:helpers:menu'] = array('class' => 'MenuHelper', 'require' => APP_DIR . '/helper/MenuHelper.php');
$config['view:helpers:icon'] = array('class' => 'IconHelper', 'require' => APP_DIR . '/helper/IconHelper.php');

if (file_exists(APP_DIR . '/config/amp.php')) {
  require_once (APP_DIR . '/config/amp.php');
}

sy_define_once('ACL_USERS_TABLE', 'users');
sy_define_once('ACL_GROUPS_TABLE', 'groups');
sy_define_once('ACL_GROUPS_USERS_TABLE', 'groups_users');
sy_define_once('ACL_GROUPS_PERMISSIONS_TABLE', 'groups_permissions');
sy_define_once('ACL_PERMISSIONS_TABLE', 'permissions');
sy_define_once('ACL_PERMISSIONS_USERS_TABLE', 'permissions_users');
sy_define_once('OPTIONS_TABLE', 'options');

Simplify_Autoload::registerPath(APP_DIR . '/lib');
Simplify_Autoload::registerPath(APP_DIR . '/model');

s::app(new ApplicationController())->dispatch();
