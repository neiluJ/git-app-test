<?php

use Fwk\Di\Container;
use Fwk\Di\ClassDefinition;
use Fwk\Core\Components\RequestMatcher\RequestMatcher;

$container = new Container();
$container->set('requestMatcher', new RequestMatcher(), true);

// git service
$container->set(
   'git',
   new ClassDefinition('TestGit\\GitService', array('/home/neiluj/tmp/repositories')),
    true
);

// viewHelper
$viewHelperClassDef = new ClassDefinition('Fwk\Core\Components\ViewHelper\ViewHelperService');
$container->set('embedViewHelper', new ClassDefinition('Fwk\Core\Components\ViewHelper\EmbedViewHelper'));
$viewHelperClassDef->addMethodCall('add', array('embed', '@embedViewHelper'));
$container->set('viewHelper', $viewHelperClassDef, true);

return $container;