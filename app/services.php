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
   new ClassDefinition('TestGit\\GitService', 
    array(
        '@repos.dir', 
        '@repos.working.dir', 
        '@git.executable',
        '@git.date.format', 
        '@forgery.user.name', 
        '@forgery.user.email', 
        '@forgery.user.fullname', 
        '@git.user.name', 
        '@git.clone.hostname.ssh.local', 
        '@logger'
    )),
    true
);

// users service
$container->set(
   'users',
   new ClassDefinition('TestGit\\UsersService', array('@apache.htpasswd.bin')),
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
$viewHelperClassDef->addMethodCall('add', array('isAllowed', new ClassDefinition('TestGit\SecurityViewHelper', array('security', 'guest'))));

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

$usersDaoDef = new ClassDefinition('TestGit\Model\User\UsersDao', 
array(
    '@db',
    array(
        'usersTable' => '@users.table',
        'sshKeysTable'   => '@users.ssh_keys.table'
    )
));
$usersDaoDef->addMethodCall('addListener', array('@gitolite'));

$container->set(
   'usersDao',
   $usersDaoDef,
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

$gitDaoDef = new ClassDefinition('TestGit\Model\Git\GitDao', 
array(
    '@db',
    array(
        'repositoriesTable' => '@repos.table',
        'accessesTable'      => '@repos.accesses.table',
        'repositoriesBasePath' => '@repos.basePath'
    )
));
$gitDaoDef->addMethodCall('addListener', array('@gitolite'));

$container->set(
   'gitDao',
   $gitDaoDef,
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

$securityDef = new ClassDefinition('Fwk\Security\Service', 
   array(
       '@authManager',
       '@usersDao',
       '@aclsManager'
   )
);
$securityDef->addMethodCall('addListener', array(
    new ClassDefinition('Fwk\Security\Acl\LoadUserAclListener') 
));

$container->set(
   'security',
   $securityDef,
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
    )),
    true
);

$container->set(
    'gitolite',
    new ClassDefinition('TestGit\GitoliteService', array()),
    true
);

$level = $container->get('log.level');
switch(strtolower($level))
{
    case 'debug':
        $finalLevel = \Monolog\Logger::DEBUG;
        break;
    
    case 'info':
        $finalLevel = \Monolog\Logger::INFO;
        break;
    
    case 'notice':
        $finalLevel = \Monolog\Logger::NOTICE;
        break;
    
    case 'warning':
        $finalLevel = \Monolog\Logger::WARNING;
        break;
    
    case 'error':
        $finalLevel = \Monolog\Logger::ERROR;
        break;
    
    case 'critical':
        $finalLevel = \Monolog\Logger::CRITICAL;
        break;
    
    case 'alert':
        $finalLevel = \Monolog\Logger::ALERT;
        break;
    
    case 'emergency':
        $finalLevel = \Monolog\Logger::EMERGENCY;
        break;
    
    default:
        $finalLevel = \Monolog\Logger::DEBUG;
        break;
}
$loggerHandlerDef = new ClassDefinition('Monolog\Handler\StreamHandler', array('@log.file', $finalLevel));
$loggerDef = new ClassDefinition('Monolog\Logger', array('forgery'));
$loggerDef->addMethodCall('pushHandler', array($loggerHandlerDef));
$container->set(
    'logger',
    $loggerDef,
    true
);

return $container;