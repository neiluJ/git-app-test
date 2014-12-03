<?php
namespace Nitronet\DevTools;


use Fwk\Core\Action\ProxyFactory;
use Fwk\Core\Application;
use Fwk\Core\Events\BootEvent;

class DevToolsManager
{
    protected $plugins = array();

    public function __construct(array $plugins = array())
    {
        array_walk($plugins, function($plugin) { $this->add($plugin); });
    }

    public function onBoot(BootEvent $bootEvent)
    {
        $app = $bootEvent->getApplication();
        $this->load($app);
    }

    public function load(Application $app)
    {
        foreach ($this->plugins as $plugin) {
            if (!$plugin instanceof Plugin) {
                throw new \InvalidArgumentException('A plugin must be an instance of Nitronet\DevTools\Plugin');
            }

            $listeners = $plugin->getListeners();
            array_walk($listeners, function($listener) use ($app) { $app->addListener($listener); });

            $actions = $plugin->getActions();
            array_walk($actions, function($proxy, $action) use ($app) {
                $app->register($action, $proxy);
            });
        }
    }

    public function add(Plugin $plugin)
    {
        $this->plugins[strtolower($plugin->getName())] = $plugin;

        return $this;
    }

    /**
     * @param string$pluginName
     *
     * @return boolean
     */
    public function has($pluginName)
    {
        return array_key_exists(strtolower($pluginName), $this->plugins);
    }

    /**
     * @param string $pluginName
     *
     * @return Plugin
     * @throws \RuntimeException
     */
    public function get($pluginName)
    {
        if (!$this->has($pluginName)) {
          throw new \RuntimeException(sprintf('Plugin "%s" does not exist', $pluginName));
        }

        return $this->plugins[strtolower($pluginName)];
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}