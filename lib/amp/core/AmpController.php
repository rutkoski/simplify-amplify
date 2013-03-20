<?php

class AmpController extends Simplify_Controller
{

  protected $permissions;

  protected function beforeAction()
  {
    if (Account::getUser() || ($this->getAction() != 'login' && $this->getAction() != 'logout')) {
      Account::validate('admin');
      Account::validate($this->permissions);
    }

    parent::beforeAction();
  }

  public function getLayoutsPath()
  {
    $path = parent::getLayoutsPath();
    array_unshift($path, AMP_DIR . '/templates/layouts');
    return $path;
  }

  public function getTemplatesPath()
  {
    $path = parent::getTemplatesPath();
    $path[] = AMP_DIR . '/templates';
    return $path;
  }

}
