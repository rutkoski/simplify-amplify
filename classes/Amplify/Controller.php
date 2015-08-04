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
 * Base Controller
 *
 */
class Controller extends \Simplify\Controller
{

  /**
   *
   * @var string[]
   */
  protected $permissions;

  /**
   *
   */
  protected function beforeAction()
  {
    try {
      if ($this->permissions !== false) {
        if (Account::getUser() || !in_array($this->getAction(), array('login', 'logout'))) {
          Account::validate('admin');
          Account::validate($this->permissions);
        }
      }

      parent::beforeAction();
    }
    catch (LoginRequiredException $e) {
      \Simplify::response()->redirect(
        array('route://admin?action=login', array('redirect' => \Simplify::request()->base() . \Simplify::request()->uri())));
    }
    catch (SecurityException $e) {
      \Simplify::session()->warnings($e->getMessage());

      \Simplify::response()->redirect('route://admin');
    }
  }

}
