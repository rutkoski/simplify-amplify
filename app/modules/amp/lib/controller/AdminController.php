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
class AdminController extends Amplify_Controller
{

  protected $layout = 'simple';

  protected function indexAction()
  {
    $this->setLayout('default');
  }

  protected function loginAction()
  {
    if (Account::getUser()) {
      return s::response()->redirect('route://admin');
    }

    $email = '';
    $password = '';

    if (s::request()->method(Simplify_Request::POST)) {
      try {
        $email = s::request()->post('email');
        $password = s::request()->post('password');

        Account::login($email, $password);

        $url = s::request()->get('redirect', 'route://admin');

        return s::response()->redirect($url);
      }
      catch (LoginException $e) {
        s::session()->warnings($e->getMessage());
      }
    }

    $this->set('email', $email);
    $this->set('password', $password);
  }

  protected function logoutAction()
  {
    Account::logout();

    return s::response()->redirect('route://admin?action=login');
  }

}
