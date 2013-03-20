<?php

class OptionsController extends ControllerImpl
{

  protected $Simplify_Form;

  protected $template = '/form_body';

  protected function initialize()
  {
    parent::initialize();

    $this->Form = new Simplify_Form('options');
    $this->Form->addAction(new Simplify_Form_ActionOptions());
    //$this->Form->title = 'Options';
    $this->Form->setLayout($this->getLayout());
  }

  protected function indexAction()
  {
    $this->Form->addElement(new Simplify_Form_Element_Text('site_name', 'Site name'), Simplify_Form::ACTION_OPTIONS);
    $this->Form->addElement(new Simplify_Form_Element_Text('site_tagline', 'Site tag line'), Simplify_Form::ACTION_OPTIONS);

    return $this->showSimplify_Form('General');
  }

  protected function emailAction()
  {
    $this->Form->addElement(new Simplify_Form_Element_Text('email_from', 'From'), Simplify_Form::ACTION_OPTIONS);

    return $this->showSimplify_Form('Email');
  }

  protected function showSimplify_Form($title)
  {
    $this->Form->getAction()->title = $title;

    try {
      $this->Form->execute();
    }
    catch (ValidationException $e) {
      s::app()->warnings($e->getErrors());
    }

    return $this->Form->render();
  }

}
