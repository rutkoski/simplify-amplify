<?php
namespace Amplify\Controller;

class HomeController extends \Simplify\Controller
{

    /**
     *
     * @var string[]
     */
    protected $permissions = false;

    /**
     * 
     */
    protected function loginAction()
    {
        //
    }

    /**
     *
     */
    protected function postLoginAction()
    {
        $username = '';
        $password = '';
        
        try {
            $username = \Simplify::request()->post('username');
            $password = \Simplify::request()->post('password');
            
            \Amplify\Account::login($username, $password);
            
            $url = \Simplify::request()->get('redirect', 'route://admin');
            
            return \Simplify::response()->redirect($url);
        } catch (\Amplify\LoginException $e) {
            \Simplify::session()->warnings($e->getMessage());
        }
        
        $this->set('username', $username);
        $this->set('password', $password);
    }

    /**
     * 
     */
    protected function logoutAction()
    {
        \Amplify\Account::logout();
        
        $url = \Simplify::request()->get('redirect', 'route://admin');
        
        return \Simplify::response()->redirect($url);
    }
}