<?php
namespace Nitronet\DevTools\Controllers;

use Fwk\Core\Action\Result;
use Nitronet\DevTools\Plugin as PluginInterface;

class Plugin extends Home
{
    public $plugin;

    /**
     * @var PluginInterface
     */
    protected $pluginObj;

    /**
     * @return string
     */
    public function show()
    {
        if (!$this->getDevToolsManager()->has($this->plugin)) {
            return Result::ERROR;
        }

        $this->pluginObj = $this->getDevToolsManager()->get($this->plugin);

        return Result::SUCCESS;
    }

    /**
     * @return PluginInterface
     */
    public function getPluginObj()
    {
        return $this->pluginObj;
    }
}