<?php

use Fwk\Di\Container;
use Fwk\Di\ClassDefinition;
use Fwk\Core\Components\RequestMatcher\RequestMatcher;

$container = new Container();
$container->iniProperties(__DIR__ .'/config.ini', 'services');

$container->set('requestMatcher', new RequestMatcher(), true);
$container->set('urlRewriter', new ClassDefinition('Fwk\Core\Components\UrlRewriter\UrlRewriterService'), true);
$container->set('resultTypeService', new \Fwk\Core\Components\ResultType\ResultTypeService(), true);

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

// database & dao
$container->set(
   'db',
   new ClassDefinition('Fwk\\Db\\Connection', 
   array(
       array(
           'dbname'    => '@db.database',
           'user'      => '@db.user',
           'password'  => '@db.password',
           'driver'    => '@db.driver',
           'host'      => '@db.hostname'
       )
   )),
   true
);

$container->set(
   'usersDao',
   new ClassDefinition('TestGit\Model\User\UsersDao', 
   array(
       '@db',
       array(
           'usersTable' => '@users.table'
       )
   )),
   true
);

$container->set(
   'gitDao',
   new ClassDefinition('TestGit\Model\Git\GitDao', 
   array(
       '@db',
       array(
           'repositoriesTable' => '@repos.table',
           'accessesTable'      => '@repos.accesses.table',
           'repositoriesBasePath' => '@repos.basePath'
       )
   )),
   true
);

return $container;