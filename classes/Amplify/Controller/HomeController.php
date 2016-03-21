<?php
namespace Amplify\Controller;

use Amplify\Dashboard;
class HomeController extends \Amplify\Controller
{

    /**
     *
     * @var string[]
     */
    protected $permissions = array('admin');
    
    protected function indexAction()
    {
        $this->set('widgets', Dashboard::loadModules());
    }
    
}