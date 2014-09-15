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
class Options
{

  protected static $instances = array();

  /**
   *
   * @return OptionsImpl
   */
  public static function getInstance($table = OPTIONS_TABLE, $pk = 'option_id')
  {
    if (!isset(self::$instances[$table])) {
      self::$instances[$table] = new OptionsImpl($table, $pk);
    }
    return self::$instances[$table];
  }

  /**
   * Delete an option.
   *
   * @param string $name
   * @return void
   */
  public static function delete($name)
  {
    return self::getInstance()->delete($name);
  }

  /**
   * Check if an option exists
   *
   * @param string $name
   * @return boolean
   */
  public static function exists($name)
  {
    return self::getInstance()->exists($name);
  }

  /**
   * If an option does not exist, create it
   *
   * @param string $name
   * @param mixed $value
   * @return boolean true if option was created, false otherwise
   */
  public static function create($name, $value, $autoload = false)
  {
    return self::getInstance()->create($name, $value, $autoload);
  }

  /**
   * Read an option
   *
   * @param string $name
   * @return Option
   */
  public static function read($name)
  {
    return self::getInstance()->read($name);
  }

  /**
   * Read an option and return it's value
   *
   * @param string $name
   * @param mixed $default default return value if option does not exist
   * @return mixed
   */
  public static function value($name, $default = null)
  {
    return self::getInstance()->value($name, $default);
  }

  /**
   * Create or update an option
   *
   * @param mixed $name option name or instance of Option
   * @param mixed $value
   * @return Option
   */
  public static function update($name, $value = null)
  {
    return self::getInstance()->update($name, $value);
  }

}

class OptionsImpl
{

  /**
   *
   * @var string
   */
  protected $table;

  /**
   *
   * @var string
   */
  protected $pk;

  /**
   *
   * @var Simplify_Db_RepositoryInterface
   */
  protected $repository;

  /**
   *
   * @var mixed[string]
   */
  protected $options;

  /**
   *
   * @param string $table
   * @param string $pk
   */
  public function __construct($table, $pk)
  {
    $this->table = $table;
    $this->pk = $pk;
  }

  protected function autoLoad($reload = false)
  {
    if (is_null($this->options) || $reload) {
      $data = $this->repository()->findAll(array('where' => 'option_autoload = 1'));
      $this->options = sy_array_to_options($data, 'option_name');
    }
    return $this->options;
  }

  /**
   * Delete an option.
   *
   * @param string $name
   * @return void
   */
  public function delete($name)
  {
    s::db()->delete($this->table, 'option_name = ?')->execute($name);

    if (isset($this->options[$name])) {
      unset($this->options[$name]);
    }
  }

  /**
   * Check if an option exists
   *
   * @param string $name
   * @return boolean
   */
  public function exists($name)
  {
    $this->autoLoad();

    $exists = isset($this->options[$name]) ||
       $this->repository()->findCount(array('where' => 'option_name = ?', 'data' => array($name)));

    return (bool) $exists;
  }

  /**
   * If an option does not exist, create it
   *
   * @param string $name
   * @param mixed $value
   * @return boolean true if option was created, false otherwise
   */
  public function create($name, $value, $autoload = false)
  {
    if (!$this->exists($name)) {
      if (!is_string($value)) {
        $value = serialize($value);
      }

      $option = array();
      $option['option_name'] = $name;
      $option['option_value'] = $value;
      $option['option_autoload'] = $autoload ? 1 : 0;

      $this->repository()->save($option);

      return true;
    }

    return false;
  }

  /**
   * Read an option
   *
   * @param string $name
   * @return array
   */
  public function read($name)
  {
    $this->autoLoad();

    if (isset($this->options[$name])) {
      $option = $this->options[$name];
    }
    else {
      $option = s::db()->query()->from($this->table)->where('option_name = ?')->execute($name)->fetchRow();
    }

    return $option;
  }

  /**
   * Read an option and return it's value
   *
   * @param string $name
   * @param mixed $default default return value if option does not exist
   * @return mixed
   */
  public function value($name, $default = null)
  {
    try {
      $option = $this->read($name);

      if (empty($option)) {
        return $default;
      }

      $str = sy_get_param($option, 'option_value', $default);
      $uns = @unserialize($str);

      $value = ($str == serialize(false) || $uns !== false) ? $uns : $str;
    }
    catch (Simplify_Db_TableNotFoundException $e) {
      $value = $default;
    }

    return $value;
  }

  /**
   * Create or update an option
   *
   * @param mixed $name option name
   * @param mixed $value option value
   * @return Option
   */
  public function update($name, $value = null, $autoload = false)
  {
    if (!is_string($value)) {
      $value = serialize($value);
    }

    $option = $this->read($name);

    if (!$option) {
      $option = array();
    }

    $option['option_name'] = $name;
    $option['option_value'] = $value;
    $option['option_autoload'] = $autoload ? 1 : 0;

    $this->repository()->save($option);

    return $option;
  }

  /**
   *
   * @return Simplify_Db_RepositoryInterface
   */
  public function repository()
  {
    if (empty($this->repository)) {
      $this->repository = Simplify_Db_TableDataGateway::getInstance($this->table, $this->pk);
    }
    return $this->repository;
  }

}
