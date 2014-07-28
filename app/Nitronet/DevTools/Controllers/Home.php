<?php
namespace Nitronet\DevTools\Controllers;

use Fwk\Core\Action\Result;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Nitronet\DevTools\DevToolsManager;

class Home implements ServicesAware
{
    protected $services;

    protected $plugins = array();

    public function show()
    {
        $this->plugins = $this->getDevToolsManager()->getPlugins();

        return Result::SUCCESS;
    }

    /**
     * @return DevToolsManager
     */
    public function getDevToolsManager()
    {
        return $this->getServices()->get('devtools');
    }

    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Container $container)
    {
        $this->services = $container;
    }

    public function getPlugins()
    {
        return $this->plugins;
    }
}