<?php
namespace TestGit;

use Fwk\Core\Components\ViewHelper\ViewHelper;
use Fwk\Core\Components\ViewHelper\AbstractViewHelper;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Zend\Permissions\Acl\Role\GenericRole;

class AppTitleViewHelper extends AbstractViewHelper implements ViewHelper
{
    public function execute(array $arguments)
    {
        return $this->getViewHelperService()
            ->getApplication()
            ->getServices()
            ->getProperty('app.title', 'Factory');
    }
}