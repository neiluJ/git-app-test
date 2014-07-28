<?php
namespace Nitronet\DevTools;

use Fwk\Core\Application;

interface Plugin
{
    public function getName();

    public function getDescription();

    public function getListeners();

    public function getActions();

    public function setApplication(Application $app);

    public function getApplication();
}