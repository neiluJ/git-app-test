<?php

use Fwk\Core\Application;
use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Components\Descriptor\Descriptor;

$desc = new Descriptor(__DIR__ .'/fwk.xml');
$desc->iniProperties(__DIR__ .'/config.ini');
$app = $desc->execute('TestGit', include __DIR__ .'/services.php');
$app->addListener(new ErrorReporterListener(array(
    'application_root' => dirname(__FILE__),
    'display_line_numbers' => true,
    'server_name' => 'DEV',
    'ignore_folders' => array(),
    'enable_saving' => false,
    'catch_ajax_errors' => true,
    'snippet_num_lines' => 10
)));

$app->setDefaultAction('Home');

return $app;