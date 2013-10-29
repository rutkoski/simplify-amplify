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
 * Base Controller
 *
 */
class Amplify_Controller extends Simplify_Controller
{

  /**
   *
   * @var string[]
   */
  protected $permissions;

  /**
   *
   * @var Simplify_Menu
   */
  protected $menu;

  /**
   *
   * @return Simplify_Menu
   */
  public function menu()
  {
    if (empty($this->menu)) {
      $this->createMenu();
    }
    return $this->menu;
  }

  /**
   * (non-PHPdoc)
   *
   * @see Simplify_Controller::initialize()
   */
  protected function initialize()
  {
    s::config()->set('theme', 'admin');
  }

  /**
   * (non-PHPdoc)
   *
   * @see Simplify_Controller::beforeAction()
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
      s::response()->redirect(
        array('route://admin?action=login', array('redirect' => s::request()->base() . s::request()->uri())));
    }
    catch (SecurityException $e) {
      s::session()->warnings($e->getMessage());

      s::response()->redirect('route://admin');
    }
  }

  /**
   * This callback runs once after every action
   *
   * @param mixed $output the action output
   * @return mixed
   */
  protected function afterAction($output)
  {
    $this->set('menu', $this->menu());

    $this->set('warnings', s::session()->warnings());
    $this->set('notices', s::session()->notices());

    s::session()->clearMessages();
  }

  /**
   *
   * @return Simplify_Menu
   */
  protected function createMenu()
  {
    $this->menu = new Simplify_Menu('amp');

    if (Account::validate('manage_accounts', true)) {
      $accountsMenu = new Simplify_Menu('accounts', null, null, __('Accounts'));

      $_users = new Simplify_MenuItem('users', __('Users'), 'index', new Simplify_URL('route://admin_users'));
      $_add_user = new Simplify_MenuItem('add_user', __('Add user'), 'create',
        new Simplify_URL('route://admin_users', array('formAction' => 'create')));

      $accountsMenu->addItem($_users);
      $accountsMenu->addItem($_add_user);

      $this->menu->addItem($accountsMenu);

      if (Account::validate('manage_groups', true)) {
        $_groups = new Simplify_MenuItem('groups', 'Groups', 'index', new Simplify_URL('route://admin_groups'));
        $_add_group = new Simplify_MenuItem('add_group', __('Add group'), 'create',
          new Simplify_URL('route://admin_groups', array('formAction' => 'create')));

        $groupsMenu = new Simplify_Menu('groups', null, null, __('Groups'), 'group');
        $groupsMenu->addItem($_groups);
        $groupsMenu->addItem($_add_group);

        $accountsMenu->addItem($groupsMenu);
      }

      if (Account::validate('manage_permissions', true)) {
        $_perms = new Simplify_MenuItem('permissions', __('Permissions'), 'index',
          new Simplify_URL('route://admin_permissions'));
        $_add_perm = new Simplify_MenuItem('add_permission', __('Add permission'), 'create',
          new Simplify_URL('route://admin_permissions', array('formAction' => 'create')));

        $permsMenu = new Simplify_Menu('permissions', null, null, __('Permissions'), 'permission');
        $permsMenu->addItem($_perms);
        $permsMenu->addItem($_add_perm);

        $accountsMenu->addItem($permsMenu);
      }
    }

    if (Account::validate('manage_options', true)) {
      $optionsMenu = new Simplify_Menu('options', null, null, __('Options'));

      $_gen = new Simplify_MenuItem('general', 'General', 'options', new Simplify_URL('route://admin_options'));
      $_mod = new Simplify_MenuItem('modules', 'Modules', 'options', new Simplify_URL('route://admin_modules'));

      $optionsMenu->addItem($_gen);
      $optionsMenu->addItem($_mod);

      $this->menu->addItem($optionsMenu);
    }

    Amplify_Modules::executeCallback('onCreateMenu', $this->menu);
  }

}
