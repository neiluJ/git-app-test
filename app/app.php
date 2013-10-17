<?php

use Fwk\Core\Application;
use Fwk\Core\Components\RequestMatcher\RequestMatcherListener;
use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Components\ViewHelper\ViewHelperListener;

$app = Application::factory("test-git", include __DIR__ .'/services.php')
->addListener(new RequestMatcherListener('requestMatcher'))
->addListener(new ErrorReporterListener(array(
    'application_root' => dirname(__FILE__),
    'display_line_numbers' => true,
    'server_name' => 'DEV',
    'ignore_folders' => array(),
    'enable_saving' => false,
    'catch_ajax_errors' => true,
    'snippet_num_lines' => 10
)))
->addListener(new ViewHelperListener('viewHelper'));

$app['Home']            = function($services) {
    include __DIR__ .'/TestGit/pages/homepage.php';
};
$app['Menu']            = function($active, $services) {
    include __DIR__ .'/TestGit/pages/menu.php';
};

// repositories action
$app['Repositories']    = function($services) {
    $repositories = $services->get('git')->listRepositories();
    
    return new Symfony\Component\HttpFoundation\JsonResponse($repositories);
};

$app->setDefaultAction('Home');

return $app;