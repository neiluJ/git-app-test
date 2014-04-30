<?php
require_once __DIR__ .'/../vendor/autoload.php';

use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Components\Descriptor\Descriptor;

$desc = new Descriptor(__DIR__ .'/../app/fwk.xml');
$response = $desc->execute('TestGit')
    ->addListener(new ErrorReporterListener())
    ->setDefaultAction('Home')
    ->run();

if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
    $response->send();
}