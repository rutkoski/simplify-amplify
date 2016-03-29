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

/**
 */
class Module
{

    public $active;

    protected $name;

    protected $description;

    /**
     *
     * @var \Simplify\Config
     */
    protected $config;

    /**
     *
     * @var \Simplify\Router
     */
    protected $router;

    public function __construct()
    {
        $this->config = \Simplify::config();
        $this->router = \Simplify::router();
    }

    /**
     * Get module name.
     * 
     * @return string
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->name = substr(get_class($this), 0, strrpos(get_class($this), '\Module'));
        }
        return $this->name;
    }

    /**
     * Runs on module initialization.
     * This is a good place to define routes, set config options.
     */
    public function onInitialize()
    {
        //
    }

    /**
     * Runs on module activation.
     */
    public function onActivate()
    {
        //
    }

    /**
     * Runs on module deactivation.
     */
    public function onDeactivate()
    {
        //
    }

    /**
     * Runs on module install.
     * It's the module's responsability to check if module is actually already installed.
     */
    public function onInstall()
    {
        //
    }

    /**
     * Runs on module removal.
     * It's the module's responsability to check if module is actually already installed.
     */
    public function onUninstall()
    {
        //
    }

    /**
     * Runs on amplify's menu creation.
     */
    public function onCreateMenu(\Simplify\Menu $menu)
    {
        //
    }
}
