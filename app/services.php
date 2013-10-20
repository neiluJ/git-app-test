<?php

use Fwk\Di\Container;
use Fwk\Di\ClassDefinition;
use Fwk\Core\Components\RequestMatcher\RequestMatcher;

$container = new Container();
$container->set('requestMatcher', new RequestMatcher(), true);
$container->set('urlRewriter', new ClassDefinition('Fwk\Core\Components\UrlRewriter\UrlRewriterService'), true);

// git service
$container->set(
   'git',
   new ClassDefinition('TestGit\\GitService', array('/home/neiluj/tmp/repositories')),
    true
);

// viewHelper
$viewHelperClassDef = new ClassDefinition('Fwk\Core\Components\ViewHelper\ViewHelperService');
$viewHelperClassDef->addMethodCall('add', array('embed', new ClassDefinition('Fwk\Core\Components\ViewHelper\EmbedViewHelper')));
$viewHelperClassDef->addMethodCall('add', array('url', new ClassDefinition('Fwk\Core\Components\UrlRewriter\UrlViewHelper', array('requestMatcher', 'urlRewriter'))));

$container->set('viewHelper', $viewHelperClassDef, true);
return $container;