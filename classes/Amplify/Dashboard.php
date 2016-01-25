<?php
namespace Amplify;

class Dashboard
{

    /**
     * 
     * @var string[]
     */
    protected static $modules = array();

    /**
     * 
     * @param DashboardModule $module
     * @return DashboardModule
     */
    public static function addModule($class)
    {
        self::$modules[] = $class;
    }

    /**
     * 
     * @return DashboardModule[]
     */
    public static function loadModules()
    {
        $modules = array();
        
        foreach (self::$modules as $class) {
            $modules[] = new $class;
        }
        
        return $modules;
    }
}