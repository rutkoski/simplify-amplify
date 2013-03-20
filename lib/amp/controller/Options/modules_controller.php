<?php

class ModulesController extends ControllerImpl
{

  protected function indexAction()
  {
    $modules = Modules::findModules();
    $this->response()->set('modules', $modules);
  }

  protected function activateAction()
  {
    Modules::activate($this->request()->get('name'));
    return $this->response()->redirect($this->request()->route());
  }

  protected function deactivateAction()
  {
    Modules::deactivate($this->request()->get('name'));
    return $this->response()->redirect($this->request()->route());
  }

}
