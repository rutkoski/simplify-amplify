<?php

/**
 * Amplify
 *
 * This file is part of Amplify.
 *
 * Amplify is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Amplify is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

/**
 *
 */
class Amplify_Modules
{

  protected static $modules = false;

  public static function executeCallback($callback)
  {
    $modules = self::getActiveModules();

    $args = func_get_args();
    unset($args[0]);

    foreach ($modules as $module) {
      call_user_func_array(array($module, $callback), $args);
    }
  }

  public static function isActive($path)
  {
    $modules = (array) Options::value('amp_active_modules');
    return in_array($path, $modules);
  }

  public static function activateModule($path)
  {
    $modules = (array) Options::value('amp_active_modules');
    if (!in_array($path, $modules)) {
      $modules[] = $path;
      Options::update('amp_active_modules', $modules);
    }
  }

  public static function deactivateModule($path)
  {
    $modules = (array) Options::value('amp_active_modules');
    if (in_array($path, $modules)) {
      $modules = array_diff($modules, array($path));
      Options::update('amp_active_modules', $modules);
    }
  }

  public static function getActiveModules()
  {
    if (self::$modules === false) {
      self::$modules = array();

      $modules = Options::value('amp_active_modules', array());

      foreach ($modules as &$path) {
        $class = substr($path, ($p = strrpos($path, '/')) === false ? 0 : $p + 1, -4);

        $filename = s::config()->get('app_dir') . '/modules' . $path;

        if (!file_exists($filename)) {
          s::session()->warnings("Could not load module: file not found: <b>{$path}</b>");
        }
        else {
          require_once ($filename);

          if (!class_exists($class)) {
            s::session()->warnings("Could not load module: class not found: <b>{$class}</b>");
          }
          elseif (!is_subclass_of($class, 'Amplify_Module')) {
            s::session()->warnings("Could not load module: <b>{$class}</b> is not a subclass of Amplify_Module");
          }
          else {
            self::$modules[$path] = new $class();
          }
        }
      }
    }

    return self::$modules;
  }

  public static function getAllModules()
  {
    $modules = array();

    $base = s::config()->get('app_dir') . '/modules';
    $path = '/';

    if (is_dir($base . $path)) {
      $it = new DirectoryIterator($base . $path);

      while ($it->valid()) {
        if (!$it->isDot()) {
          if ($it->isDir()) {
            $class = Simplify_Inflector::camelize($it->getFilename()) . 'Module';

            $filename = $path . $it->getFilename() . '/' . $class . '.php';

            if (file_exists($base . $filename)) {
              require_once ($base . $filename);

              if (class_exists($class) && is_subclass_of($class, 'Amplify_Module')) {
                $modules[$filename] = new $class();
              }
            }
          }
          elseif (strrpos($it->getFilename(), 'Module.php') !== false) {
            $filename = $path . $it->getFilename();

            require_once ($base . $filename);

            $class = $it->getBasename('.php');

            if (class_exists($class) && is_subclass_of($class, 'Amplify_Module')) {
              $modules[$filename] = new $class();
            }
          }
        }

        $it->next();
      }
    }

    return $modules;
  }

}