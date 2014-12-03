<?php
namespace TestGit;

require_once __DIR__ .'/../vendor/autoload.php';

use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Components\Descriptor\Descriptor;
use Fwk\Core\Events\BootEvent;
use Fwk\Core\Plugins\RequestMatcherPlugin;
use Fwk\Core\Plugins\ResultTypePlugin;
use Fwk\Core\Plugins\UrlRewriterPlugin;
use Symfony\Component\HttpFoundation\Response;
use Nitronet\Fwk\Comments\CommentsPlugin;
use TestGit\Listeners\CommentsListener;

$desc = new Descriptor(__DIR__ .'/../app/fwk.xml');
$app = $desc->execute(__NAMESPACE__)
->setDefaultAction('Home')
->plugin(new RequestMatcherPlugin())
->plugin(new UrlRewriterPlugin())
->plugin(new ResultTypePlugin())
->addListener(new ErrorReporterListener());

$services = $app->getServices();

$app->plugin(new CommentsPlugin(array(
    'db'            => $services->getProperty('comments.services.database', 'db'),
    'sessionService'    => $services->getProperty('comments.services.session', 'session'),
    'rendererService'   => $services->getProperty('comments.services.renderer', 'formRenderer'),
    'threadsTable'  => $services->getProperty('comments.tables.threads', 'comments_threads'),
    'threadEntity'  => $services->getProperty('comments.entities.thread', 'Nitronet\Fwk\Comments\Model\Thread'),
    'commentsTable' => $services->getProperty('comments.tables.comments', 'comments'),
    'commentEntity' => $services->getProperty('comments.entities.comment', 'Nitronet\Fwk\Comments\Model\Comment'),
    'commentForm'   => $services->getProperty('comments.form', 'Nitronet\Fwk\Comments\Forms\AnonymousCommentForm'),
    'autoThread'    => $services->getProperty('comments.auto.thread', false),
    'autoApprove'   => $services->getProperty('comments.auto.approve', true),
    'dateFormat'    => $services->getProperty('comments.date.format', 'Y-m-d H:i:s'),
    'serviceName'   => $services->getProperty('comments.service', 'comments')
)))
->on('boot', function(BootEvent $event) {
    $event->getApplication()->getServices()->get('comments')->addListener(
        new CommentsListener($event->getApplication()->getServices())
    );
});

ob_start("ob_gzhandler");

$response = $app->run();
if ($response instanceof Response) {
    $response->send();
}

//ob_end_flush();