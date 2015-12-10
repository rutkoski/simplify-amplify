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

use Amplify\AmplifyException;

class ModulesController extends \Amplify\Controller
{

    protected function indexAction()
    {
        if (\Simplify::request()->get('activate')) {
            \Amplify\Modules::activateModule(\Simplify::request()->get('module'));
            \Simplify::response()->redirect(\Simplify::request()->route());
        } elseif (\Simplify::request()->get('deactivate')) {
            \Amplify\Modules::deactivateModule(\Simplify::request()->get('module'));
            \Simplify::response()->redirect(\Simplify::request()->route());
        }
        
        try {
            $modules = \Amplify\Modules::getAllModules();
            
            $this->set('modules', $modules);
        } catch (AmplifyException $e) {
            //
        }
    }
}
