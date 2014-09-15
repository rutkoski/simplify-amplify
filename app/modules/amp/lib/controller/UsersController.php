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
class UsersController extends Amplify_FormController
{

  protected function createForm()
  {
    parent::createForm();

    $groups = new Simplify_Form_Element_Checkboxes('groups', __('Groups'));
    $groups->labelField = 'group_name';

    $permissions = new Simplify_Form_Element_Checkboxes('permissions', __('Permissions'));
    $permissions->labelField = 'permission_description';

    $password = new Simplify_Form_Element_Password('user_password', __('Password'));

    $email = new Simplify_Form_Element_Email('user_email', __('Email'));
    $email->unique = __('This email address is already taken');

    $this->Form->addElement($email);
    $this->Form->addElement($password, Simplify_Form::ACTION_ALL ^ Simplify_Form::ACTION_LIST);
    $this->Form->addElement($groups, Simplify_Form::ACTION_LIST | Simplify_Form::ACTION_EDIT);
    $this->Form->addElement($permissions, Simplify_Form::ACTION_LIST | Simplify_Form::ACTION_EDIT);

    $this->Form->label = 'user_email';

    $this->Form->addListener(Simplify_Form::ON_RENDER, $this);
    $this->Form->addListener(Simplify_Form::ON_BEFORE_DELETE, $this);
  }

  protected function accountAction()
  {
    try {
      $user = Account::getUser();

      $this->Form->id = $user['user_id'];
      $this->Form->showMenu = false;
      $this->Form->showItemMenu = false;

      $result = $this->Form->execute('edit');

      if ($result == Simplify_Form::RESULT_SUCCESS) {
        s::session()->notices(__('Success'));

        return s::response()->redirect(s::request()->route());
      }
    }
    catch (Simplify_ValidationException $e) {
      s::session()->warnings(__('There are errors'));
    }

    $this->set('formBody', $this->Form->render('edit'));
  }

  public function onBeforeDelete(Simplify_Form_Action $action, $row)
  {
    if ($row['id'] == 1) {
      throw new Simplify_ValidationException('Cannot delete this user');
    }
  }

  public function onRender(Simplify_Form_Action $action)
  {
    $data = $action->get('data');

    foreach($data as &$row) {
      if ($row[Simplify_Form::ID] == 1) {
        $row['elements']['groups']['controls'] = __('This user belongs to every group');
        $row['elements']['permissions']['controls'] = __('This user has all permissions');
      }
    }

    $action->set('data', $data);
  }

}
