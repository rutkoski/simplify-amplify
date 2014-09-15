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
class GroupsController extends Amplify_FormController
{

  protected function initialize()
  {
    parent::initialize();

    $permissions = new Simplify_Form_Element_Checkboxes('permissions', 'Permissions');
    $permissions->labelField = 'permission_name';

    $this->Form->addElement(new Simplify_Form_Element_Text('group_name', 'Name'));
    $this->Form->addElement($permissions, Simplify_Form::ACTION_LIST | Simplify_Form::ACTION_EDIT);
  }

}
