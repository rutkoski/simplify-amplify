<?php

class Options
{

  protected static $instances = array();

  /**
   *
   * @return OptionsImpl
   */
  public static function getInstance($table = OPTIONS_TABLE, $pk = 'option_id')
  {
    if (! isset(self::$instances[$table])) {
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
    return self::getInstance()->delete($name, $value, $autoload);
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

  protected $table = OPTIONS_TABLE;

  protected $pk = 'option_id';

  /**
   *
   * @var IRepository
   */
  protected $repo;

  protected $options;

  public function __construct($table, $pk)
  {
    $this->table = $table;
    $this->pk = $pk;
  }

  protected function autoLoad()
  {
    if (is_null($this->options)) {
      $data = s::db()->query()->from($this->table)->where('option_autoload = 1')->execute()->fetchAll();

      foreach ($data as $row) {
        $this->options[$row['option_name']] = $row;
      }
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

    $this->autoLoad();

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

    $exists = isset($this->options[$name]) || $this->getRepository()->findCount(array('where' => 'option_name = ?', 'data' => array($name)));

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
    if (! $this->exists($name)) {
      if (! is_string($value)) {
        $value = serialize($value);
      }

      $option = array();
      $option['option_name'] = $name;
      $option['option_value'] = $value;
      $option['option_autoload'] = $autoload ? 1 : 0;

      $this->getRepository()->save($option);

      return true;
    }

    return false;
  }

  /**
   * Read an option
   *
   * @param string $name
   * @return Option
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
    $option = $this->read($name);

    if (empty($option)) {
      return $default;
    }

    $str = sy_get_param($option, 'option_value', $default);
    $uns = @unserialize($str);

    return ($str == serialize(false) || $uns !== false) ? $uns : $str;
  }

  /**
   * Create or update an option
   *
   * @param mixed $name option name or instance of Option
   * @param mixed $value
   * @return Option
   */
  public function update($name, $value = null)
  {
    $args = func_get_args();
    $value = sy_get_param($args, 1);

    if (is_array($name)) {
      $option = $name;
    }
    else {
      $option = $this->read($name);

      if (! $option) {
        $option = array();
      }

      $option['option_name'] = $name;
      $option['option_value'] = $value;
    }

    $option['option_modified_at'] = date('Y-m-d H:i:s');

    $this->getRepository()->save($option);

    return $option;
  }

  /**
   *
   * @return TableDataGateway
   */
  public function getRepository()
  {
    if (empty($this->repo)) {
      $this->repo = TableDataGateway::getInstance($this->table, $this->pk);
    }

    return $this->repo;
  }

}

?>