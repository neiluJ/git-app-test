<?php
namespace Nitronet\DevTools\Plugins\Debug;

use Fwk\Core\Action\ProxyFactory;
use Fwk\Core\Application;
use Nitronet\DevTools\Plugin;

class DebugPlugin implements Plugin
{
    protected $application;

    public function getName()
    {
        return "Debugger";
    }

    public function getDescription()
    {
        return "Debug and Profile every request made to your Application";
    }

    public function getListeners()
    {
        return array(
            new DebugListener()
        );
    }

    public function getDefaultAction()
    {
        return 'DTDebuggerHome';
    }

    public function getActions()
    {
        return array(
            'DTDebuggerHome' => ProxyFactory::factory("Nitronet\\DevTools\\Plugins\\Debug\\Controllers\\Home:show")
        );
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }
}