<?php

use Fwk\Di\Container;
use Fwk\Di\ClassDefinition;
use Fwk\Core\Components\RequestMatcher\RequestMatcher;
use Fwk\Security\Service as SecurityService;

$container = new Container();
$container->iniProperties(__DIR__ .'/config.ini', 'services');

$container->set('requestMatcher', new RequestMatcher(), true);
$container->set('urlRewriter', new ClassDefinition('Fwk\Core\Components\UrlRewriter\UrlRewriterService'), true);
$container->set('resultTypeService', new \Fwk\Core\Components\ResultType\ResultTypeService(), true);

// git service
$container->set(
   'git',
   new ClassDefinition('TestGit\\GitService', array('@repos.dir', '@repos.working.dir')),
    true
);

// console App
$container->set(
    'consoleApp',
    new ClassDefinition('\Symfony\Component\Console\Application', 
    array('TestGit', '1.0')),
    true
);

// viewHelper
$viewHelperClassDef = new ClassDefinition('Fwk\Core\Components\ViewHelper\ViewHelperService');
$viewHelperClassDef->addMethodCall('add', array('embed', new ClassDefinition('Fwk\Core\Components\ViewHelper\EmbedViewHelper')));
$viewHelperClassDef->addMethodCall('add', array('url', new ClassDefinition('Fwk\Core\Components\UrlRewriter\UrlViewHelper', array('requestMatcher', 'urlRewriter'))));
$viewHelperClassDef->addMethodCall('add', array('escape', new ClassDefinition('Fwk\Core\Components\ViewHelper\EscapeViewHelper', array(ENT_QUOTES, "utf-8"))));
$viewHelperClassDef->addMethodCall('add', array('form', new ClassDefinition('TestGit\Form\RendererViewHelper', array('formRenderer'))));
$viewHelperClassDef->addMethodCall('add', array('formElement', new ClassDefinition('TestGit\Form\RendererElementViewHelper', array('formRenderer'))));

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
   'aclsDao',
   new ClassDefinition('TestGit\Model\User\AclDao', 
   array(
       '@db',
       array(
            'rolesTable'        => '@acls.table.roles',
            'resourcesTable'    => '@acls.table.resources',
            'permissionsTable'  => '@acls.table.permissions'
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

$container->set(
  'securitySessionStorage',
  new ClassDefinition('Fwk\Security\Http\SessionStorage',
    array(
       '@session'
    )        
));

$container->set(
    'authManager',
    new ClassDefinition('Fwk\Security\Authentication\Manager',
    array(
       '@securitySessionStorage'
    ) 
));

$container->set(
    'aclsManager',
    new ClassDefinition('Fwk\Security\Acl\Manager',
    array(
       '@aclsDao'
    ) 
));

$container->set(
   'security',
   new ClassDefinition('Fwk\Security\Service', 
   array(
       '@authManager',
       '@usersDao',
       '@aclsManager'
   )),
   true
);

$container->set(
    'authFilter',
    new ClassDefinition('TestGit\Form\AuthenticationFilter',
    array(
        '@usersDao',
        '@security'
    ))
);

$container->set(
    'formRenderer',
    new ClassDefinition('Fwk\Form\Renderer',
    array(
        array('resourcesDir' => __DIR__ .'/TestGit/pages/form')
    ))
);

return $container;