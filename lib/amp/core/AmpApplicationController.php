<?php

class AmpApplicationController extends Simplify_Controller_ApplicationController
{

  const MESSAGES_NOTICES = 'notices';

  const MESSAGES_WARNINGS = 'warnings';

  protected $messages;

  protected $menu;

  public function menu()
  {
    if (empty($this->menu)) {
      $this->menu = new Simplify_Menu('amp');

      $this->menu->addItem(
        new Simplify_Menu('accounts', array(
          new Simplify_MenuItem('users', 'Users', 'index', new Simplify_URL('/users')),
          new Simplify_MenuItem('add_user', 'Add user', 'create', new Simplify_URL('/users', array('formAction' => 'create'))),
          new Simplify_Menu('groups', array(
            new Simplify_MenuItem('groups', 'Groups', 'index', new Simplify_URL('/users/groups')),
            new Simplify_MenuItem('add_group', 'Add group', 'create', new Simplify_URL('/users/groups', array('formAction' => 'create')))
          ), null, 'Groups', 'group'),
          new Simplify_Menu('permissions', array(
            new Simplify_MenuItem('permissions', 'Permissions', 'index', new Simplify_URL('/users/permissions')),
            new Simplify_MenuItem('add_permission', 'Add permission', 'create', new Simplify_URL('/users/permissions', array('formAction' => 'create')))
          ), null, 'Permissions', 'permission'),
        ), null, 'Accounts')
      );

      $this->menu->addItem(new Simplify_Menu('options', array(
        new Simplify_MenuItem('general', 'General', 'options', new Simplify_URL('/options')),
        new Simplify_MenuItem('email', 'Email', 'options', new Simplify_URL('/options', array('action' => 'email'))),
        new Simplify_MenuItem('modules', 'Modules', 'options', new Simplify_URL('/options/modules'))
      ), null, 'Options'));
    }

    return $this->menu;
  }

  public function dispatch()
  {
    try {
      $this->messages = s::session()->flash('messages');

      //Modules::loadModules();

      //$this->installed();

      return parent::dispatch();
    }
    catch (LoginRequiredException $e) {
      s::response()->redirect(array('/login', array('action' => 'login', 'redirect' => s::request()->base() . s::request()->uri())));
    }
    catch (SecurityException $e) {
      s::app()->warnings($e->getMessage());

      s::response()->redirect(array('/login', array('action' => 'login', 'redirect' => s::request()->base() . s::request()->uri())));
    }
    catch (NotInstalledException $e) {
      s::response()->redirect('/login', array('action' => 'login'));
    }
  }

  public function installed()
  {
    if (s::request()->route() == '/install') return;

    $found = s::db()->query()->select('COUNT(user_id)')->from(ACL_USERS_TABLE)->where('user_id = ?')->execute(1)->fetchOne();

    if (! $found) {
      throw new NotInstalledException('Amp is not installed');
    }
  }

  public function messages($type, $msgs = null, $name = '*')
  {
    if (is_array($msgs)) {
      foreach ($msgs as $msg) {
        $this->messages($type, $msg, $name);
      }
    }
    elseif (! empty($msgs)) {
      $this->messages[$type][$name][md5($msgs)] = $msgs;

      s::session()->flash('messages', $this->messages);
    }

    return sy_get_param($this->messages, $type);
  }

  public function warnings($msgs = null, $name = '*')
  {
    return $this->messages(self::MESSAGES_WARNINGS, $msgs, $name);
  }

  public function notices($msgs = null, $name = '*')
  {
    return $this->messages(self::MESSAGES_NOTICES, $msgs, $name);
  }

}
