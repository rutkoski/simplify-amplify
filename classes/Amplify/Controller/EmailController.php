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

use Amplify\Controller\ConfigController;
use Simplify\Form;
use Simplify\Form\Element\Text;
use Simplify\Form\Element\Boolean;
use Simplify\Form\Element\Password;
use Simplify\Form\Element\Email;

class EmailController extends ConfigController
{

    protected function createElements()
    {
        // mail from
        $this->Form->addElement(new Email('mail_from_email', 'Remetente - Email'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Text('mail_from_name', 'Remetente - Nome'), Form::ACTION_CONFIG);
        
        // smtp
        $this->Form->addElement(new Boolean('mail_smtp', 'Usar SMTP'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Boolean('mail_smtp_auth', 'Requer autenticação'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Text('mail_smtp_auth_type', 'Tipo de autenticação'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Text('mail_smtp_host', 'Host'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Text('mail_smtp_port', 'Porta'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Text('mail_smtp_user', 'Usuário'), Form::ACTION_CONFIG);
        $this->Form->addElement(new Password('mail_smtp_password', 'Senha'), Form::ACTION_CONFIG)->setOption('hashCallback', false)->setOption('required', false);
    }
}