<?php
namespace TestGit;

require_once __DIR__ .'/../vendor/autoload.php';

use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Components\Descriptor\Descriptor;
use Fwk\Core\Plugins\RequestMatcherPlugin;
use Fwk\Core\Plugins\ResultTypePlugin;
use Fwk\Core\Plugins\UrlRewriterPlugin;
use Symfony\Component\HttpFoundation\Response;

$desc = new Descriptor(__DIR__ .'/../app/fwk.xml');
$app = $desc->execute(__NAMESPACE__)
->setDefaultAction('Home')
->plugin(new RequestMatcherPlugin())
->plugin(new UrlRewriterPlugin())
->plugin(new ResultTypePlugin())
->addListener(new ErrorReporterListener());

ob_start("ob_gzhandler");

$response = $app->run();
if ($response instanceof Response) {
    $response->send();
}

//ob_end_flush();