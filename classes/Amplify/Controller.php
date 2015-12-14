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
 * Base Controller
 */
class Controller extends \Simplify\Controller
{

    /**
     *
     * @var string[]
     */
    protected $permissions;

    /**
     * (non-PHPdoc)
     * @see \Simplify\Controller::beforeAction()
     */
    protected function beforeAction($action, $params)
    {
        $this->checkPermissions($action, $params);
    }

    protected function checkPermissions($action, $params)
    {
        try {
            $permissions = array();
            
            if (! empty($this->permissions)) {
                foreach ($this->permissions as $key => $value) {
                    if ($action === $key) {
                        $permissions = array_merge($permissions, (array) $value);
                    } elseif (substr($key, 0, 1) === '^' && $action !== substr($key, 1)) {
                        $permissions = array_merge($permissions, (array) $value);
                    } elseif (is_numeric($key)) {
                        $permissions = array_merge($permissions, (array) $value);
                    }
                }
            }
            
            if (! empty($permissions)) {
                Account::validate($permissions);
            }
            
            parent::beforeAction($action, $params);
        } catch (LoginRequiredException $e) {
            $loginUrl = array(
                'route://admin?action=login',
                array(
                    'redirect' => \Simplify::request()->base() . \Simplify::request()->uri()
                )
            );
            
            \Simplify::response()->redirect($loginUrl);
        } catch (SecurityException $e) {
            \Simplify::session()->warnings($e->getMessage());
            
            \Simplify::response()->redirect('route://admin');
        }
    }
}
