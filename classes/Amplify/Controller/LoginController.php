<?php
namespace Amplify\Controller;

class LoginController extends \Amplify\Controller
{

    /**
     *
     * @var string[]
     */
    protected $permissions = false;

    /**
     *
     */
    protected function indexAction()
    {
        $username = '';
        $password = '';
        
        if (\Simplify::request()->method(\Simplify\Request::POST)) {
            try {
                $this->set('__post', true);
                $username = \Simplify::request()->post('username');
                $password = \Simplify::request()->post('password');
                
                \Amplify\Account::login($username, $password);
                
                if (\Simplify::request()->json()) {
                    return true;
                }
                
                $url = \Simplify::request()->get('redirect', 'route://admin');
                
                return \Simplify::response()->redirect($url);
            } catch (\Amplify\LoginException $e) {
                \Simplify::session()->warnings($e->getMessage());
            }
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