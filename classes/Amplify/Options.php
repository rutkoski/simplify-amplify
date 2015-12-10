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
namespace Amplify;

class Options
{

    /**
     *
     * @var \Simplify\Db\RepositoryInterface
     */
    protected static $repository;

    /**
     *
     * @var mixed[string]
     */
    protected static $options;

    public static function autoLoad($reload = false)
    {
        if (is_null(self::$options) || $reload) {
            $data = self::repository()->findAll(array(
                'where' => 'option_autoload = 1'
            ));
            self::$options = sy_array_to_options($data, 'option_name');
        }
        return self::$options;
    }

    /**
     * Delete an option.
     *
     * @param string $name            
     * @return void
     */
    public static function delete($name)
    {
        \Simplify::db()->delete(\Simplify::config()->get('amp:tables:options'), 'option_name = ?')
            ->execute($name);
        
        if (isset(self::$options[$name])) {
            unset(self::$options[$name]);
        }
    }

    /**
     * Check if an option exists
     *
     * @param string $name            
     * @return boolean
     */
    public static function exists($name)
    {
        self::autoLoad();
        
        $exists = isset(self::$options[$name]) || self::repository()->findCount(array(
            'where' => 'option_name = ?',
            'data' => array(
                $name
            )
        ));
        
        return (bool) $exists;
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
        if (! self::exists($name)) {
            if (! is_string($value)) {
                $value = serialize($value);
            }
            
            $option = array();
            $option['option_name'] = $name;
            $option['option_value'] = $value;
            $option['option_autoload'] = $autoload ? 1 : 0;
            
            self::repository()->save($option);
            
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
    public static function read($name)
    {
        self::autoLoad();
        
        if (isset(self::$options[$name])) {
            $option = self::$options[$name];
        } else {
            $option = \Simplify::db()->query()
                ->from(\Simplify::config()->get('amp:tables:options'))
                ->where('option_name = ?')
                ->execute($name)
                ->fetchRow();
        }
        
        return $option;
    }

    /**
     * Read an option and return it's value
     *
     * @param string $name            
     * @param mixed $default
     *            default return value if option does not exist
     * @return mixed
     */
    public static function value($name, $default = null)
    {
        try {
            $option = self::read($name);
            
            if (empty($option)) {
                return $default;
            }
            
            $str = sy_get_param($option, 'option_value', $default);
            $uns = @unserialize($str);
            
            $value = ($str == serialize(false) || $uns !== false) ? $uns : $str;
            
            return $value;
        } catch (\Simplify\Db\TableNotFoundException $e) {
            return $default;
        }
    }

    /**
     * Create or update an option
     *
     * @param mixed $name
     *            option name
     * @param mixed $value
     *            option value
     * @return Option
     */
    public static function update($name, $value = null, $autoload = false)
    {
        if (! is_string($value)) {
            $value = serialize($value);
        }
        
        $option = self::read($name);
        
        if (! $option) {
            $option = array();
        }
        
        $option['option_name'] = $name;
        $option['option_value'] = $value;
        $option['option_autoload'] = $autoload ? 1 : 0;
        
        self::repository()->save($option);
        
        return $option;
    }

    /**
     *
     * @return \Simplify\Db\RepositoryInterface
     */
    public function repository()
    {
        if (empty(self::$repository)) {
            self::$repository = \Simplify\Db\TableDataGateway::getInstance(\Simplify::config()->get('amp:tables:options'), 'option_id');
        }
        return self::$repository;
    }
}
