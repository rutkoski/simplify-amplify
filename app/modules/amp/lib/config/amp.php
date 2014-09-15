<?php

$config = s::config();

$config['modules:amp:dir'] = AMP_DIR;

s::router()->connect('admin_groups', '/admin/users/groups/:action',
    array('module' => 'amp', 'controller' => 'groups', 'action' => 'index'));

s::router()->connect('admin_permissions', '/admin/users/permissions/:action',
    array('module' => 'amp', 'controller' => 'permissions', 'action' => 'index'));

s::router()->connect('admin_users', '/admin/users/:action',
    array('module' => 'amp', 'controller' => 'users', 'action' => 'index'));

s::router()->connect('admin_account', '/admin/account',
    array('module' => 'amp', 'controller' => 'users', 'action' => 'account'));

s::router()->connect('admin_options', '/admin/options',
    array('module' => 'amp', 'controller' => 'options', 'action' => 'index'));

s::router()->connect('admin_modules', '/admin/options/modules',
    array('module' => 'amp', 'controller' => 'modules', 'action' => 'index'));

s::router()->connect('admin', '/admin/:action', array('module' => 'amp', 'controller' => 'admin', 'action' => 'index'),
    array('action' => 'index|login|logout'));

s::router()->connect('admin_install', '/admin/install', array('module' => 'amp', 'controller' => 'install'));
