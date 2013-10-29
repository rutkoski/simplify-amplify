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
class InstallController extends Amplify_Controller
{

  protected $layout = 'simple';

  protected $permissions = false;

  protected function indexAction()
  {
    $email = '';
    $password = '';

    if (s::request()->method(Simplify_Request::POST)) {
      try {
        $email = s::request()->post('email');
        $password = s::request()->post('password');

        Install::performInstall($email, $password);

        s::session()->notices(__('Amplify is ready! Login with your email and password.'));

        $url = s::request()->get('redirect', '/');

        s::response()->redirect($url);
      }
      catch (Simplify_ValidationException $e) {
        s::session()->warnings($e->getErrors());
      }
    }

    $this->set('email', $email);
    $this->set('password', $password);
  }

}
