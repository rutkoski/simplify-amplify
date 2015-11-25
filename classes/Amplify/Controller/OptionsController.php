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
 */
class OptionsController extends \Amplify\Controller
{

    /**
     *
     * @var \Simplify\Form
     */
    protected $Form;

    /**
     *
     * @var string
     */
    protected $template = 'form_container';

    /**
     * (non-PHPdoc)
     * 
     * @see \Simplify\Controller::indexAction()
     */
    protected function indexAction()
    {
        try {
            $this->Form = new \Simplify\Form('options', __('Opções'));
            
            $this->Form->showMenu = false;
            
            $this->Form->addAction(new \Simplify\Form\Action\Config('config', __('Gerais'), \Simplify::config()->get('amp:tables:options'), 'option_name', 'option_value'));
            
            $this->Form->addElement(new \Simplify\Form\Element\Text('site_name', __('Nome do site')), \Simplify\Form::ACTION_CONFIG);
            
            $this->Form->execute();
        } catch (\Simplify\ValidationException $e) {
            \Simplify::session()->warnings(__('Ocorreram erros'));
        }
        
        $this->set('formBody', $this->Form->render());
    }
}
