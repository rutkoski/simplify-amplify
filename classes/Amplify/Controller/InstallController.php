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

namespace Amplify\Controller;

/**
 *
 */
class InstallController extends \Amplify\Controller
{

  protected $permissions = false;

  protected function indexAction()
  {
    $username = '';
    $email = '';
    $password = '';

    if (\Simplify::request()->method(\Simplify\Request::POST)) {
      try {
        $username = \Simplify::request()->post('username');
        $email = \Simplify::request()->post('email');
        $password = \Simplify::request()->post('password');

        \Amplify\Install::performInstall($username, $email, $password);

        \Simplify::session()->notices(__('Instalação realizada com sucesso.'));

        $url = \Simplify::request()->get('redirect', \Simplify::router()->make('admin'));

        \Simplify::response()->redirect($url);
      }
      catch (\Simplify\ValidationException $e) {
        \Simplify::session()->warnings($e->getErrors());
      }
    }

    $this->set('username', $username);
    $this->set('email', $email);
    $this->set('password', $password);
  }

}
