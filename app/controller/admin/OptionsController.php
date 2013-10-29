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
class OptionsController extends Amplify_Controller
{

  /**
   *
   * @var string
   */
  protected $template = 'form_body';

  protected function indexAction()
  {
    try {
      $form = new Simplify_Form(__('Options'));

      $form->addAction(new Simplify_Form_Action_Config('config', OPTIONS_TABLE, 'option_name', 'option_value'));

      $form->addElement(new Simplify_Form_Element_Text('site_name'), Simplify_Form::ACTION_CONFIG);
      $form->addElement(new Simplify_Form_Element_Text('site_tagline'), Simplify_Form::ACTION_CONFIG);

      $form->execute();
    }
    catch (Simplify_ValidationException $e) {
      s::session()->warnings(__('There are errors'));
    }

    $this->set('formBody', $form->render());
  }

}
