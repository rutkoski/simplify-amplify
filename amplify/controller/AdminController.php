<?php

class AdminController extends AmpController
{

  protected $layout = 'simple';

  protected function indexAction()
  {
    $this->setLayout('default');
  }

  protected function loginAction()
  {
    if (Account::getUser()) {
      return s::response()->redirect('/');
    }

    $email = '';
    $password = '';

    if (s::request()->method(Simplify_Request::POST)) {
      try {
        $email = s::request()->post('email');
        $password = s::request()->post('password');

        Account::login($email, $password);

        $url = s::request()->get('redirect', '/');

        return s::response()->redirect($url);
      }
      catch (LoginException $e) {
        s::app()->warnings($e->getMessage());
      }
    }

    $this->set('email', $email);
    $this->set('password', $password);
  }

  protected function logoutAction()
  {
    Account::logout();

    return s::response()->redirect('/');
  }

  protected function installAction()
  {
    if (s::request()->method(Simplify_Request::POST)) {
      Account::addUserPermission(Account::createUser(s::request()->post('email'), s::request()->post('password')), Account::createPermission('admin'));

      s::response()->redirect('/');
    }
  }

}
