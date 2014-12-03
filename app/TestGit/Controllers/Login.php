<?php
namespace TestGit\Controllers;

use Fwk\Core\Action\Result;
use Symfony\Component\HttpFoundation\Session\Session;
use TestGit\Form\LoginForm;
use TestGit\Form\AuthenticationFilter;
use Fwk\Core\Preparable;
use Fwk\Core\Action\Controller;

class Login extends Controller implements Preparable
{
    protected $back;
    
    /**
     * The Login Forms
     * 
     * @var LoginForm
     */
    protected $loginForm;
    
    public function prepare()
    {
        // prevent redirection error (empty uri)
        if (empty($this->back)) {
            $this->back = $this->getContext()->getRequest()->getBaseUrl();
            if (empty($this->back)) {
                $this->back = '/';
            }
        }
    }
    
    public function show()
    { 
        if ($this->getIdentity() !== null) {
            return Result::SUCCESS;
        }
        
        $form = $this->getLoginForm();
        if ($this->isPOST()) {
            $form->submit($_POST);
            
            if(!$form->validate()) {
                return Result::FORM;
            }
            
            /**
             * @todo Update last_login
             */
            return Result::SUCCESS;
        }
        
        return Result::FORM;
    }
    
    public function logout()
    {
        $security   = $this->getServices()->get('security');
        $request    = $this->getContext()->getRequest();

        try {
            if (false === $security->deauthenticate($request)) {
                /**
                 * @todo Log Logout errors
                 */
            }
        } catch(\Exception $e) {

        }

        $session = $this->getServices()->get('session');
        if ($session instanceof Session) {
            $session->clear();
        }

        $ref = $request->headers->get('Referer');
        if (!empty($ref)) {
            $this->back = $ref;
        } else {
            $this->back = $this->getServices()->get('viewHelper')->url('Home');
        }
        
        /**
         * @todo Update last_seen
         */
        return Result::SUCCESS;
    }
    
    public function getBack() // Jojo 
    {
        return $this->back;
    }

    public function setBack($back)
    {
        $this->back = $back;
    }
    
    /**
     *
     * @return LoginForm
     */
    public function getLoginForm()
    {
        if (!isset($this->loginForm)) {
            $this->loginForm    = new LoginForm();
            $this->loginForm->filter(
                $this->getServices()->get('authFilter'),
                "Invalid credentials."
            );
            $this->loginForm->setAction($this->getServices()->get('viewHelper')->url('Login', array('back' => $this->back)));
        }
        
        return $this->loginForm;
    }
    
    public function getIdentity()
    {
        return $this->getServices()
                ->get('security')
                ->getAuthenticationManager()
                ->getIdentity();
    }
    
    public function isPOST()
    {
        return "POST" === $_SERVER['REQUEST_METHOD'];
    }
}