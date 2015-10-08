<?php

namespace Amplify\Controller;

class FormController extends \Amplify\Controller
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
  protected $table;

  /**
   *
   * @var string
   */
  protected $tablePrefix;
  
  /**
   *
   * @var string
   */
  protected $pk;

  /**
   *
   * @var string
   */
  protected $label;
  
  /**
   *
   * @var string
   */
  protected $sortField;

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
  protected $template = 'form_container';

  /**
   * (non-PHPdoc)
   * 
   * @see \Amplify\Controller::initialize()
   */
  protected function initialize()
  {
    parent::initialize();
    
    $this->createForm();
    $this->createActions();
    $this->createElements();
  }

  protected function createForm()
  {
    if ($this->sortField) {
      $this->Form = new \Simplify\Form\Sortable($this->getName());
      $this->Form->sortField = $this->getSortField();
    }
    else {
      $this->Form = new \Simplify\Form($this->getName());
    }

    $this->Form->table = $this->getTable();
    $this->Form->primaryKey = $this->getPrimaryKey();
    $this->Form->label = $this->getLabel();
    $this->Form->title = $this->getTitle();
  }

  protected function createActions()
  {
    $this->Form->addAction(new \Simplify\Form\Action\Index());
    $this->Form->addAction(new \Simplify\Form\Action\View());
    $this->Form->addAction(new \Simplify\Form\Action\Edit());
    $this->Form->addAction(new \Simplify\Form\Action\Create());
    $this->Form->addAction(new \Simplify\Form\Action\Delete());
  }

  protected function createElements()
  {
  }

  /**
   *
   * @return \Simplify\Form\Action\Index
   */
  protected function actionIndex()
  {
    return $this->Form->getAction('index');
  }

  /**
   *
   * @return \Simplify\Form\Action\Create
   */
  protected function actionCreate()
  {
    return $this->Form->getAction('create');
  }

  /**
   *
   * @return \Simplify\Form\Action\Edit
   */
  protected function actionEdit()
  {
    return $this->Form->getAction('edit');
  }

  /**
   *
   * @return \Simplify\Form\Action\Delete
   */
  protected function actionDelete()
  {
    return $this->Form->getAction('delete');
  }

  /**
   *
   * @return \Simplify\Form\Action\Config
   */
  protected function actionConfig()
  {
    return $this->Form->getAction('config');
  }

  protected function getTable()
  {
    if (empty($this->table)) {
      $this->table = $this->getName();
    }
    
    return $this->getTablePrefix() . $this->table;
  }
  
  protected function getTablePrefix()
  {
      if (empty($this->tablePrefix) && $this->tablePrefix !== false) {
          $this->tablePrefix = \Simplify::config()->get('amp:tables_prefix');
      }
      
      return $this->tablePrefix;
  }

  protected function getTitle()
  {
    if (empty($this->title)) {
      $this->title = \Simplify\Inflector::titleize($this->getName());
    }
    
    return $this->title;
  }

  protected function getPrimaryKey()
  {
    if (empty($this->pk)) {
      $this->pk = \Simplify\Inflector::singularize($this->getName()) . '_id';
    }
    
    return $this->pk;
  }

  protected function getLabel()
  {
    return $this->label;
  }
  
  protected function getSortField()
  {
    if (empty($this->sortField)) {
      $this->sortField = \Simplify\Inflector::singularize($this->getName()) . '_order';
    }
  
    return $this->sortField;
  }
  
  /**
   * (non-PHPdoc)
   * 
   * @see \Simplify\Controller::indexAction()
   */
  protected function indexAction()
  {
    try {
      $result = $this->Form->execute();
      
      if ($result == \Simplify\Form::RESULT_SUCCESS) {
        \Simplify::session()->notices('Success');
        
        return \Simplify::response()->redirect(\Simplify::request()->route());
      }
    }
    catch (\Simplify\Db\TableNotFoundException $e) {
      $create = $this->Form->url()->set('createRepository', 1);
      \Simplify::session()->warnings(__('Repository not found. Click <a href="' . $create . '">here</a> to create it.') . '<br/>' . $e->getMessage());
      return;
    }
    catch (\Simplify\Db\ColumnNotFoundException $e) {
      $create = $this->Form->url()->set('createColumns', 1);
      \Simplify::session()->warnings(__('Column not found. Click <a href="' . $create . '">here</a> to create it.') . '<br/>' . $e->getMessage());
      return;
    }
    catch (\Simplify\ValidationException $e) {
      \Simplify::session()->warnings(__('There are errors') . '<br/>' . $e->getMessage());
    }
    
    $this->set('formBody', $this->Form->render());
  }

}
