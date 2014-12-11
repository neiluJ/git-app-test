<?php
namespace TestGit;

use Fwk\Core\Application;
use Fwk\Core\Components\Descriptor\Descriptor;
use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Events\BootEvent;
use Fwk\Core\Plugins\RequestMatcherPlugin;
use Fwk\Core\Plugins\ResultTypePlugin;
use Fwk\Core\Plugins\UrlRewriterPlugin;
use Fwk\Db\Connection;
use Fwk\Security\Authentication\Result;
use Nitronet\Fwk\Comments\CommentsPlugin;
use Symfony\Component\HttpFoundation\Request;
use TestGit\Listeners\CommentsListener;
use TestGit\Model\User\User;
use Zend\Authentication\Adapter\AdapterInterface;

class TestAuthAdapter implements AdapterInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        return new Result(
            Result::SUCCESS,
            array(
                'identifier' => $this->user->getIdentifier(),
                'username'   => $this->user->getUsername()
            ),
            array()
        );
    }
}

class TestUtils
{
    public static function getApplication()
    {
        $desc = new Descriptor(__DIR__ .'/fwk-test.xml');
        $app = $desc->execute(__NAMESPACE__)
            ->setDefaultAction('Home')
            ->plugin(new RequestMatcherPlugin())
            ->plugin(new UrlRewriterPlugin())
            ->plugin(new ResultTypePlugin());

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

        return $app;
    }

    public static function requestFactory($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array())
    {
        $server['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server);

        return $request;
    }

    public static function authenticate(Application $app, User $user)
    {
        $authManager = $app->getServices()->get('authManager');
        $authManager->setAdapter(new TestAuthAdapter($user));
        $authManager->authenticate();
    }

    public static function installTestDb(Connection $db)
    {

    }
}