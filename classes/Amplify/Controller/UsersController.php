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

use Simplify\Form\Element\Checkboxes;

/**
 */
class UsersController extends \Amplify\Controller\FormController
{

    /**
     * (non-PHPdoc)
     * @see \Amplify\Controller\FormController::initialize()
     */
    protected function initialize()
    {
        parent::initialize();
        
        $groups = new Checkboxes('groups', __('Grupos'));
        $groups->table = \Simplify::config()->get('amp:tables:groups');
        $groups->foreignKey = 'group_id';
        $groups->associationPrimaryKey = 'user_id';
        $groups->associationForeignKey = 'group_id';
        $groups->associationTable = \Simplify::config()->get('amp:tables:groups_users');
        $groups->labelField = 'group_name';
        
        $permissions = new Checkboxes('permissions', __('Permissões'));
        $permissions->table = \Simplify::config()->get('amp:tables:permissions');
        $permissions->foreignKey = 'permission_id';
        $permissions->associationPrimaryKey = 'user_id';
        $permissions->associationForeignKey = 'permission_id';
        $permissions->associationTable = \Simplify::config()->get('amp:tables:permissions_users');
        $permissions->labelField = 'permission_description';
        
        $password = new \Simplify\Form\Element\Password('user_password', __('Senha'));
        
        $email = new \Simplify\Form\Element\Email('user_email', __('Email'));
        $email->unique = __('Email já cadastrado');
        
        $this->Form->addElement($email);
        $this->Form->addElement($password, \Simplify\Form::ACTION_ALL ^ \Simplify\Form::ACTION_LIST);
        $this->Form->addElement($groups, \Simplify\Form::ACTION_LIST | \Simplify\Form::ACTION_EDIT);
        $this->Form->addElement($permissions, \Simplify\Form::ACTION_LIST | \Simplify\Form::ACTION_EDIT);
        
        $this->Form->label = 'user_email';

        $this->Form->title = __('Usuários');
        
        $this->Form->addListener(\Simplify\Form::ON_RENDER, $this);
        $this->Form->addListener(\Simplify\Form::ON_BEFORE_DELETE, $this);
    }

    protected function accountAction()
    {
        try {
            $user = \Amplify\Account::getUser();
            
            $this->Form->id = $user['user_id'];
            $this->Form->showMenu = false;
            $this->Form->showItemMenu = false;
            
            $result = $this->Form->execute('edit');
            
            if ($result == \Simplify\Form::RESULT_SUCCESS) {
                \Simplify::session()->notices(__('Sucesso'));
                
                return \Simplify::response()->redirect(\Simplify::request()->route());
            }
        } catch (\Simplify\ValidationException $e) {
            \Simplify::session()->warnings(__('Ocorreram erros'));
        }
        
        $this->set('formBody', $this->Form->render('edit'));
    }

    public function onBeforeDelete(\Simplify\Form\Action $action, $row)
    {
        if ($row['id'] == 1) {
            throw new \Simplify\ValidationException(__('Não é possível remover este usuário'));
        }
    }

    public function onRender(\Simplify\Form\Action $action)
    {
        $data = $action->get('data');
        
        foreach ($data as &$row) {
            if ($row[\Simplify\Form::ID] == 1) {
                $row['elements']['groups']['controls'] = '<p class="form-control-static">' . __('Este usuário pertence a todos os grupos') . '</p>';
                $row['elements']['permissions']['controls'] = '<p class="form-control-static">' . __('Este usuário tem todas as permissões') . '</p>';
            }
        }
        
        $action->set('data', $data);
    }
}
