<?php
namespace Amplify;

use Simplify\AssetManager;

class Application extends \Simplify\Application
{

    /**
     *
     * @var \Simplify\Menu
     */
    protected $menu;

    /**
     * (non-PHPdoc)
     * @see \Simplify\Application::initialize()
     */
    protected function initialize()
    {
        parent::initialize();

        \Amplify\Modules::executeCallback('onInitialize');
    }
    
    /**
     *
     * @return \Simplify\Menu
     */
    public function menu()
    {
        if (empty($this->menu)) {
            $this->createMenu();
        }
        
        return $this->menu;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Simplify\Application::pageNotFound()
     */
    protected function pageNotFound()
    {
        \Simplify::response()->set404();
        
        $view = \Simplify\View::factory();
        $view->setTemplate('page_not_found');
        $view->setLayout('layout/default');
        
        return $this->outputResponse($view);
    }

    /**
     * (non-PHPdoc)
     * @see \Simplify\Application::outputResponse()
     */
    protected function outputResponse($output)
    {
        if (defined('SY_IN_ADMIN') && $output instanceof \Simplify\View) {
            if (\Amplify\Install::installed() && !($output instanceof \Simplify\View\Json)) {
                $output->set('user', \Amplify\Account::getUser());
                
                $output->set('menu', $this->menu());

                AssetManager::load('fancybox/jquery.fancybox.css', 'vendor');
                AssetManager::load('fancybox/jquery.fancybox.pack.js', 'vendor');
                
                AssetManager::load('amplify.js', 'app');
            }
            
            if (\Simplify::session()->warnings()) {
                $output->set('warnings', \Simplify::session()->warnings());
            }
            
            if (\Simplify::session()->notices()) {
                $output->set('notices', \Simplify::session()->notices());
            }
            
            \Simplify::session()->clearMessages();
        }
        
        return parent::outputResponse($output);
    }

    /**
     *
     * @return \Simplify\Menu
     */
    protected function createMenu()
    {
        $this->menu = new \Simplify\Menu('amp');
        
        if (Account::validate('manage_accounts', true)) {
            $accountsMenu = new \Simplify\Menu('accounts', null, __('Usuários'));
            
            $_users = new \Simplify\MenuItem('users', __('Usuários'), null, new \Simplify\URL('route://admin_users'));
            $_add_user = new \Simplify\MenuItem('add_user', __('Adicionar usuário'), null, new \Simplify\URL('route://admin_users', array(
                'formAction' => 'create'
            )));
            
            $accountsMenu->addItem($_users);
            $accountsMenu->addItem($_add_user);
            
            $this->menu->addItem($accountsMenu);
            
            if (Account::validate('manage_groups', true)) {
                $_groups = new \Simplify\MenuItem('groups', 'Grupos', null, new \Simplify\URL('route://admin_groups'));
                $_add_group = new \Simplify\MenuItem('add_group', __('Criar grupo'), null, new \Simplify\URL('route://admin_groups', array(
                    'formAction' => 'create'
                )));
                
                $groupsMenu = new \Simplify\Menu('groups', null, __('Grupos'));
                $groupsMenu->addItem($_groups);
                $groupsMenu->addItem($_add_group);
                
                $accountsMenu->addItem($groupsMenu);
            }
            
            if (Account::validate('manage_permissions', true)) {
                $_perms = new \Simplify\MenuItem('permissions', __('Permissões'), null, new \Simplify\URL('route://admin_permissions'));
                $_add_perm = new \Simplify\MenuItem('add_permission', __('Criar permissão'), null, new \Simplify\URL('route://admin_permissions', array(
                    'formAction' => 'create'
                )));
                
                $permsMenu = new \Simplify\Menu('permissions', null, __('Permissões'));
                $permsMenu->addItem($_perms);
                $permsMenu->addItem($_add_perm);
                
                $accountsMenu->addItem($permsMenu);
            }
        }
        
        if (Account::validate('manage_options', true)) {
            $optionsMenu = new \Simplify\Menu('options', null, __('Opções'));
            
            $general = new \Simplify\MenuItem('general', 'Geral', null, new \Simplify\URL('route://admin_options'));
            $modules = new \Simplify\MenuItem('modules', 'Módulos', 'options', new \Simplify\URL('route://admin_modules'));
            $email = new \Simplify\MenuItem('email', 'Email', null, \Simplify\URL::make('route://admin_options_email'));
            
            $optionsMenu->addItem($general);
            $optionsMenu->addItem($modules);
            $optionsMenu->addItem($email);
            
            $this->menu->addItem($optionsMenu);
        }
        
        \Amplify\Modules::executeCallback('onCreateMenu', $this->menu);
    }
}
