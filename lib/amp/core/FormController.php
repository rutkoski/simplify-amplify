<?php

class FormController extends AmpController
{

  /**
   *
   * @var Simplify_Form
   */
  protected $Form;

  /**
   *
   * @var string
   */
  protected $table;

  /**
   *
   * @var string
   */
  protected $pk;

  /**
   *
   * @var int
   */
  protected $limit;

  /**
   *
   * @var string
   */
  protected $title;

  /**
   *
   * @var string
   */
  protected $template = 'form_body';

  /**
   * (non-PHPdoc)
   * @see Simplify_Controller::initialize()
   */
  protected function initialize()
  {
    parent::initialize();

    $this->Form = new Simplify_Form($this->getName());

    $this->Form->table = $this->getTable();
    $this->Form->primaryKey = $this->getPrimaryKey();
    $this->Form->title = $this->getTitle();

    $this->Form->setLayout($this->getLayout());

    $this->Form->addAction(new Simplify_Form_Action_List());
    $this->Form->addAction(new Simplify_Form_Action_Edit());
    $this->Form->addAction(new Simplify_Form_Action_Create());
    $this->Form->addAction(new Simplify_Form_Action_Delete());
    $this->Form->addAction(new Simplify_Form_Action_Services());
  }

  protected function getTable()
  {
    if (empty($this->table)) {
      $this->table = $this->getName();
    }

    return $this->table;
  }

  protected function getTitle()
  {
    if (empty($this->title)) {
      $this->title = Simplify_Inflector::titleize($this->getName());
    }

    return $this->title;
  }

  protected function getPrimaryKey()
  {
    if (empty($this->pk)) {
      $this->pk = Simplify_Inflector::singularize($this->getTable()) . '_id';
    }

    return $this->pk;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Controller::indexAction()
   */
  protected function indexAction()
  {
    try {
      $result = $this->Form->execute();

      if ($result == Simplify_Form::RESULT_SUCCESS) {
        s::app()->notices('Success');

        return s::response()->redirect(s::request()->route());
      }
    }
    catch (Simplify_ValidationException $e) {
      s::app()->warnings($e->getErrors());
    }

    return $this->Form->render();
  }

}
