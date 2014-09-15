<?php

define('AMP_DIR', __DIR__ . '/');

//require_once ('vendor/autoload.php');

$config = s::config();

$config['view:helpers:top_menu'] = array('class' => 'TopMenuHelper', 'require' => AMP_DIR . 'helper/TopMenuHelper.php');
$config['view:helpers:menu'] = array('class' => 'MenuHelper', 'require' => AMP_DIR . 'helper/MenuHelper.php');
$config['view:helpers:icon'] = array('class' => 'IconHelper', 'require' => AMP_DIR . 'helper/IconHelper.php');

require_once (AMP_DIR . 'config/amp.php');

if (file_exists(APP_DIR . 'config/amp.php')) {
  require_once (APP_DIR . 'config/amp.php');
}

sy_define_once('ACL_USERS_TABLE', 'users');
sy_define_once('ACL_GROUPS_TABLE', 'groups');
sy_define_once('ACL_GROUPS_USERS_TABLE', 'groups_users');
sy_define_once('ACL_GROUPS_PERMISSIONS_TABLE', 'groups_permissions');
sy_define_once('ACL_PERMISSIONS_TABLE', 'permissions');
sy_define_once('ACL_PERMISSIONS_USERS_TABLE', 'permissions_users');
sy_define_once('OPTIONS_TABLE', 'options');

Simplify_Autoload::registerPath(AMP_DIR . 'lib/');
Simplify_Autoload::registerPath(AMP_DIR . 'model/');

Simplify_View_Twig::getInstance()->addGlobal('Options', Options::getInstance());
//Simplify_View_Twig::getInstance()->addGlobal('URL', Simplify_URL::make());

$twigUrl = new Twig_SimpleFunction('URL', function($route = null, array $params = null, $keepOriginal = null, array $remove = null, $format = null) {
  return new Simplify_URL($route, $params, $keepOriginal, $remove, $format);
});

Simplify_View_Twig::getInstance()->addFunction($twigUrl);

s::app(new ApplicationController());//->dispatch();
