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
 *
 */
class Module
{

  public $active;
  
  protected $name;

  protected $description;
  
  public function getName()
  {
    if (empty($this->name)) {
      $this->name = substr(get_class($this), 0, strrpos(get_class($this), '\Module'));
    }
    return $this->name;
  }

  public function onInitialize()
  {
  }

  public function onActivate()
  {
  }

  public function onDeactivate()
  {
  }

  public function onInstall()
  {
  }

  public function onUninstall()
  {
  }

  public function onCreateMenu(\Simplify\Menu $menu)
  {
  }

}
