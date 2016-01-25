<?php
namespace Amplify\Controller;

use Amplify\Dashboard;
class HomeController extends \Amplify\Controller
{
    
    protected function indexAction()
    {
        $this->set('widgets', Dashboard::loadModules());
    }
    
}