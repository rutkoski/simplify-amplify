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
class ModulesController extends Amplify_Controller
{

  protected function indexAction()
  {
    if (s::request()->get('activate')) {
      Amplify_Modules::activateModule(s::request()->get('module'));
      s::response()->redirect(s::request()->route());
    } elseif (s::request()->get('deactivate')) {
      Amplify_Modules::deactivateModule(s::request()->get('module'));
      s::response()->redirect(s::request()->route());
    }

    $modules = Amplify_Modules::getAllModules();
    $this->set('modules', $modules);
  }

}
