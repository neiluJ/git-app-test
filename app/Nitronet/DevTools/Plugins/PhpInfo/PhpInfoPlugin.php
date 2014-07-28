<?php
namespace Nitronet\DevTools\Plugins\PhpInfo;

use Fwk\Core\Action\ProxyFactory;
use Fwk\Core\Application;
use Nitronet\DevTools\Plugin;

class PhpInfoPlugin implements Plugin
{
    protected $application;

    public function getName()
    {
        return "PhpInfo";
    }

    public function getDescription()
    {
        return "Simply display phpinfo() on this environment";
    }

    public function getListeners()
    {
        return array();
    }

    public function getActions()
    {
        return array(
            'DTPhpInfo' => ProxyFactory::factory(function() {
               phpinfo();
               exit;
            })
        );
    }

    public function getDefaultAction()
    {
        return 'DTPhpInfo';
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