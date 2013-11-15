<?php
namespace TestGit\Form;

use Fwk\Core\Components\ViewHelper\ViewHelper;
use Fwk\Core\Components\ViewHelper\AbstractViewHelper;
use Fwk\Core\Components\ViewHelper\Exception;

class RendererViewHelper extends AbstractViewHelper implements ViewHelper
{
    protected $rendererService;
    
    public function __construct($rendererService)
    {
        $this->rendererService = $rendererService;
    }
    
    public function execute(array $arguments)
    {
        $form = (isset($arguments[0]) ? $arguments[0] : null);
        if (!$form instanceof \Fwk\Form\Form) {
            throw new Exception('Argument is not an instance of Fwk\Form\Form');
        }
        
        return $this->getViewHelperService()
                    ->getApplication()
                    ->getServices()
                    ->get($this->rendererService)
                    ->render($form);
    }
}
