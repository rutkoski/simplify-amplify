<?php

s::router()->connect('admin_groups', '/admin/users/groups/:action',
  array('path' => '/admin', 'controller' => 'groups', 'action' => 'index'));

s::router()->connect('admin_permissions', '/admin/users/permissions/:action',
  array('path' => '/admin', 'controller' => 'permissions', 'action' => 'index'));

s::router()->connect('admin_users', '/admin/users/:action',
  array('path' => '/admin', 'controller' => 'users', 'action' => 'index'));

s::router()->connect('admin_account', '/admin/account',
  array('path' => '/admin', 'controller' => 'users', 'action' => 'account'));

s::router()->connect('admin_options', '/admin/options',
  array('path' => '/admin', 'controller' => 'options', 'action' => 'index'));

s::router()->connect('admin_modules', '/admin/options/modules',
  array('path' => '/admin', 'controller' => 'modules', 'action' => 'index'));

s::router()->connect('admin', '/admin/:action',
  array('path' => '/admin', 'controller' => 'admin', 'action' => 'index'), array('action' => 'index|login|logout'));

s::router()->connect('admin_install', '/admin/install', array('path' => '/admin', 'controller' => 'install'));
