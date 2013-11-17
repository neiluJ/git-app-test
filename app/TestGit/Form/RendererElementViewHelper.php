<?php
namespace TestGit\Form;

use Fwk\Core\Components\ViewHelper\ViewHelper;
use Fwk\Core\Components\ViewHelper\AbstractViewHelper;
use Fwk\Core\Components\ViewHelper\Exception;

class RendererElementViewHelper extends AbstractViewHelper implements ViewHelper
{
    protected $rendererService;
    
    public function __construct($rendererService)
    {
        $this->rendererService = $rendererService;
    }
    
    public function execute(array $arguments)
    {
        $element = (isset($arguments[0]) ? $arguments[0] : null);
        $form = (isset($arguments[1]) ? $arguments[1] : null);
        
        if (empty($element)) {
            throw new Exception('Empty form element');
        }
        
        if (!$form instanceof \Fwk\Form\Form) {
            throw new Exception('Argument 2 is not an instance of Fwk\Form\Form');
        }
        
        return $this->getViewHelperService()
                    ->getApplication()
                    ->getServices()
                    ->get($this->rendererService)
                    ->renderElement($form->element($element), $form);
    }
}
