<?php

session_name('sid');

$config = \Simplify::config();

if (file_exists($config['app:dir'] . 'config/amplify.php')) {
    require_once($config['app:dir'] . 'config/amplify.php');
}

$config['amp:dir'] = preg_replace('#[\\\/]+#', '/', __dir__ . '/');

if (empty($config['amp:prefix']) && $config['amp:prefix'] !== false) {
    $config['amp:prefix'] = '/admin';
}

if (! $config['amp:tables_prefix'] && $config['amp:tables_prefix'] !== false) {
    $config['amp:tables_prefix'] = 'amp_';
}

\Simplify::router()->match($config['amp:prefix'] . '/install', array(
    'controller' => 'Amplify\Controller\InstallController',
    'action' => 'index',
    'as' => 'admin_install'
));

\Simplify::router()->filter($config['amp:prefix'] . '/*')->parse(function ($extra) use($config)
{
    \Simplify::router()->match($config['amp:prefix'] . '/404', array(
        'controller' => 'Amplify\Controller\HomeController',
        'action' => 'page_not_found',
        'as' => 'admin_page_not_found'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/login', array(
        'controller' => 'Amplify\Controller\LoginController',
        'action' => 'index',
        'as' => 'admin_login'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/logout', array(
        'controller' => 'Amplify\Controller\LoginController',
        'action' => 'logout',
        'as' => 'admin_logout'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] ? $config['amp:prefix'] : '/', array(
        'controller' => 'Amplify\Controller\HomeController',
        'as' => 'admin'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/account', array(
        'controller' => 'Amplify\Controller\UsersController',
        'action' => 'account',
        'as' => 'admin_account'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/users', array(
        'controller' => 'Amplify\Controller\UsersController',
        'as' => 'admin_users'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/groups', array(
        'controller' => 'Amplify\Controller\GroupsController',
        'as' => 'admin_groups'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/permissions', array(
        'controller' => 'Amplify\Controller\PermissionsController',
        'as' => 'admin_permissions'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/options', array(
        'controller' => 'Amplify\Controller\OptionsController',
        'as' => 'admin_options'
    ));
    
    \Simplify::router()->match($config['amp:prefix'] . '/modules', array(
        'controller' => 'Amplify\Controller\ModulesController',
        'as' => 'admin_modules'
    ));

    \Simplify::router()->match($config['amp:prefix'] . '/options/email', array(
        'controller' => 'Amplify\Controller\EmailController',
        'as' => 'admin_options_email'
    ));
    
    \Simplify::config()->set('theme', 'amplify');
    
    /*if ($extra !== '/login' && $extra !== '/install') {
        try {
            \Amplify\Account::validate('admin');
        } catch (Amplify\LoginRequiredException $e) {
            \Simplify::response()->redirect(\Simplify::config()->get('amp:prefix') . '/login');
        }
    }*/
    
    return \Simplify::request()->route();
});

$config['amp:tables:users'] = '{amp:tables_prefix}users';
$config['amp:tables:groups'] = '{amp:tables_prefix}groups';
$config['amp:tables:groups_users'] = '{amp:tables_prefix}groups_users';
$config['amp:tables:groups_permissions'] = '{amp:tables_prefix}groups_permissions';
$config['amp:tables:permissions'] = '{amp:tables_prefix}permissions';
$config['amp:tables:permissions_users'] = '{amp:tables_prefix}permissions_users';
$config['amp:tables:options'] = '{amp:tables_prefix}options';

if (preg_match('#^' . $config['amp:prefix'] . '(/.*)?$#', Simplify::request()->route())) {
    define('SY_IN_ADMIN', true);
    
    $config['amp:modules_dir'] = '{app:dir}modules/';
    
    $config['templates:path:'] = '{app:dir}templates/amplify/';
    $config['templates:path:'] = '{amp:dir}templates/amplify/';
    $config['templates:path:'] = '{amp:dir}templates/';
    
    $config['app:assets:path:'] = 'vendor/rutkoski/amplify/assets/';
}

\Simplify::app(new \Amplify\Application());

$install = \Simplify::router()->make('admin_install');

if (! \Amplify\Install::installed() && \Simplify::request()->route() !== $install) {
    \Simplify::response()->redirect($install);
}
