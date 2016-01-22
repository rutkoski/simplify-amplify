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
     *
     * @var int
     */
    protected $actionMask = \Simplify\Form::ACTION_ALL;

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
            $this->Form = new \Simplify\Form\Sortable($this->getName(), $this->getTitle());
            $this->Form->sortField = $this->getSortField();
        } else {
            $this->Form = new \Simplify\Form($this->getName(), $this->getTitle());
        }
        
        $this->Form->table = $this->getTable();
        $this->Form->primaryKey = $this->getPrimaryKey();
        $this->Form->label = $this->getLabel();
    }

    protected function createActions()
    {
        if ($this->show(\Simplify\Form::ACTION_LIST)) {
            $this->Form->addAction(new \Simplify\Form\Action\Index(null, __('Listar')));
        }
        
        if ($this->show(\Simplify\Form::ACTION_VIEW)) {
            $this->Form->addAction(new \Simplify\Form\Action\View(null, __('Visualizar')));
        }
        
        if ($this->show(\Simplify\Form::ACTION_EDIT)) {
            $this->Form->addAction(new \Simplify\Form\Action\Edit(null, __('Editar')));
        }
        
        if ($this->show(\Simplify\Form::ACTION_CREATE)) {
            $this->Form->addAction(new \Simplify\Form\Action\Create(null, __('Criar')));
        }
        
        if ($this->show(\Simplify\Form::ACTION_DELETE)) {
            $this->Form->addAction(new \Simplify\Form\Action\Delete(null, __('Remover')));
        }
    }

    protected function createElements()
    {
        //
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

    public function getTable()
    {
        if (empty($this->table)) {
            $this->table = $this->getName();
        }
        
        return $this->getTablePrefix() . $this->table;
    }

    public function getTablePrefix()
    {
        if (empty($this->tablePrefix) && $this->tablePrefix !== false) {
            $this->tablePrefix = \Simplify::config()->get('amp:tables_prefix');
        }
        
        return $this->tablePrefix;
    }

    public function getTitle()
    {
        if (empty($this->title)) {
            $this->title = \Simplify\Inflector::titleize($this->getName());
        }
        
        return $this->title;
    }

    public function getPrimaryKey()
    {
        if (empty($this->pk)) {
            $this->pk = \Simplify\Inflector::singularize($this->getName()) . '_id';
        }
        
        return $this->pk;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getSortField()
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
                
                //return \Simplify::response()->redirect(\Simplify::request()->route());
                return \Simplify::response()->redirect($this->Form->url()->extend()->set('formAction', null));
            }
//         } catch (\Simplify\Db\TableNotFoundException $e) {
//             $create = $this->Form->url()->set('createRepository', 1);
//             \Simplify::session()->warnings(__('Repository not found. Click <a href="' . $create . '">here</a> to create it.') . '<br/>' . $e->getMessage());
//             return;
//         } catch (\Simplify\Db\ColumnNotFoundException $e) {
//             $create = $this->Form->url()->set('createColumns', 1);
//             \Simplify::session()->warnings(__('Column not found. Click <a href="' . $create . '">here</a> to create it.') . '<br/>' . $e->getMessage());
//             return;
        } catch (\Simplify\ValidationException $e) {
            \Simplify::session()->warnings(__('Verifique os erros abaixo'));
            //\Simplify::session()->warnings($e->getErrors());
        } catch (\Exception $e) {
            \Simplify::session()->warnings($e->getMessage());
        }
        
        $this->set('formBody', $this->Form->render());
    }

    /**
     *
     * @return boolean
     */
    public function show($actionMask)
    {
        return ($this->actionMask & $actionMask) == $actionMask;
    }
}
