<?php

sy_debug_level(SY_DEBUG_ERRORS);
//sy_debug_level(SY_DEBUG_NONE);

date_default_timezone_set('America/Sao_Paulo');

$config['database'] = array(
  'default' => array(
    // global
    '*' => array(
      'host' => 'localhost',
      'username' => '',
      'password' => '',
      'name' => 'amplify',
      'charset' => 'utf8'
    ),

    // production
    'production' => array(
      'host' => 'localhost',
      'username' => '',
      'password' => '',
      'name' => 'amplify',
      'charset' => 'utf8'
     )
   )
);

require_once ('routes.php');
