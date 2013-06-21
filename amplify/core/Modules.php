<?php

class Modules
{

  protected static $active;

  protected static $modules = array();

  public static function activate($name)
  {
    $modules = self::findActive();
    $modules[$name] = self::findModule($name);
    $modules[$name]['active'] = 1;
    Options::update('active_modules', $modules);
  }

  public static function deactivate($name)
  {
    $modules = self::findActive();
    $modules[$name] = self::findModule($name);
    $modules[$name]['active'] = 0;
    Options::update('active_modules', $modules);
  }

  public static function findActive()
  {
    if (empty(self::$active)) {
      self::$active = Options::value('active_modules', array());
    }

    return self::$active;
  }

  public static function loadModules()
  {
    $base = s::config()->get('app_dir') . '/controllers';

    $modules = self::findActive();

    foreach ($modules as $name => $module) {
      if ($module['active']) {
        self::loadModule($name);
      }
    }
  }

  public static function loadModule($name)
  {
    $base = s::config()->get('app_dir') . '/controllers';

    $modules = self::findActive();

    $module = $modules[$name];

    if (empty($module)) {
      s::app()->warnings("Module not found: $name");
      return false;
    }

    if (! file_exists($base . $module['path'])) {
      s::app()->warnings("Module file not found: $name");
      return false;
    }

    require_once($base . $module['path']);

    $class = Simplify_Inflector::camelize($name . '_module');

    $Module = new $class;
    $Module->initialize();

    self::$modules[$name] = $Module;

    return $Module;
  }

  public static function findModule($name)
  {
    $base = s::config()->get('app_dir') . '/controllers';

    $files = self::_findModules($base);

    $active = self::findActive();

    $modules = array();

    $file = $files[$name];

    require_once($base . $file);

    $class = Simplify_Inflector::camelize($name . '_module');

    if (! class_exists($class)) {
      continue;
    }

    $module = array(
      'path' => $file,
      'active' => isset($active[$name])
    );

    return $module;
  }

  public static function findModules()
  {
    $base = s::config()->get('app_dir') . '/controllers';

    $files = self::_findModules($base);

    $active = self::findActive();

    $modules = array();

    foreach ($files as $name => $file) {
      require_once($base . $file);

      $class = Simplify_Inflector::camelize($name . '_module');

      if (! class_exists($class)) {
        continue;
      }

      $modules[$name] = array(
        'path' => $file,
        'active' => (isset($active[$name]) && ! empty($active[$name]['active']))
      );
    }

    return $modules;
  }

  protected static function _findModules($base, $path = '')
  {
    $files = array();

    $it = new DirectoryIterator($base . $path);

    while ($it->valid()) {
      if (! $it->isDot()) {
        if ($it->isDir()) {
          $files += self::_findModules($base, $path . '/' . $it->getFilename());
        }
        elseif (preg_match('/(.+)_module\.php$/', $it->getFilename(), $o)) {
          $files[$o[1]] = $path . '/' . $it->getFilename();
        }
      }

      $it->next();
    }

    return $files;
  }

}
