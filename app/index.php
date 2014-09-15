<?php

define('APP_DIR', __DIR__ . '/');

require_once ('vendor/autoload.php');

require_once ('modules/amp/lib/autoload.php');

s::app()->dispatch();
