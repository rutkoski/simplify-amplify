<?php
namespace Amplify\Controller;

class ConfigController extends \Amplify\Controller
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
    protected $table = 'options';

    /**
     *
     * @var string
     */
    protected $tablePrefix;

    /**
     *
     * @var string
     */
    protected $nameField = 'option_name';

    /**
     *
     * @var string
     */
    protected $valueField = 'option_value';

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
        $this->Form = new \Simplify\Form($this->getName(), __('Opções'));
        $this->Form->showMenu = false;
    }

    protected function createActions()
    {
        $this->Form->addAction(new \Simplify\Form\Action\Config('config', $this->getTitle(), $this->getTable(), $this->nameField, $this->valueField));
    }

    protected function createElements()
    {}

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

    /**
     * (non-PHPdoc)
     *
     * @see \Simplify\Controller::indexAction()
     */
    protected function indexAction()
    {
        try {
            $this->Form->execute();
        } catch (\Simplify\ValidationException $e) {
            \Simplify::session()->warnings(__('Ocorreram erros'));
        }
        
        $this->set('formBody', $this->Form->render());
    }
}
