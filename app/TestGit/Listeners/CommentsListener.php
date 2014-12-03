<?php
namespace TestGit\Listeners;


use Fwk\Di\Container;
use Nitronet\Fwk\Comments\Events\CommentAddedEvent;
use Nitronet\Fwk\Comments\Events\CommentPostedEvent;
use TestGit\Form\CommentForm;
use TestGit\Model\Comment\Comment;
use TestGit\Model\Git\Repository;
use TestGit\Model\User\Activity;

class CommentsListener
{
    protected $services;
    protected $lastRepo;

    public function __construct(Container $services)
    {
        $this->services = $services;
    }

    public function onCommentPosted(CommentPostedEvent $event)
    {
        $form = $event->getCommentForm();
        $comment = $event->getComment();

        if (!$form instanceof CommentForm) {
            return;
        }

        $user = $this->services->get('security')->getUser();

        if (!$user instanceof \TestGit\Model\User\User) {
            // the previous call to getUser() should have thrown an exception
            $event->stop();
            $event->setError('Invalid user. Please authenticate...');
            return;
        }

        $form->setAuthorEmail($user->getEmail());
        $form->setAuthorName($user->getUsername());
        $form->setAuthorUrl(null);

        $repository = $this->extractRepository($event->getThread()->getName());
        $this->lastRepo = $repository;

        if (!$repository instanceof Repository) {
            $event->stop();
            $event->setError('Invalid repository.');
            return;
        }

        $repository->loadAcls($user, $this->services->get('aclsManager'));
        if (!$this->services->get('aclsManager')->isAllowed($user, $repository, 'read')) {
            $event->stop();
            $event->setError('You\'re not authorized to comment on this repository.');
            return;
        }

        if ($comment instanceof Comment) {
            $comment->setAuthorId($user->getId());
            $comment->setRepositoryId($repository->getId());
        }
    }

    public function onCommentAdded(CommentAddedEvent $event)
    {
        // add Activity here
        $comment = $event->getComment();
        if (!$comment instanceof Comment) {
            // we don't support other types
            return;
        }

        $usersDao   = $this->services->get('usersDao');
        $activity   = new Activity();

        if ($comment->isCommitComment()) {
            $activity->setType(Activity::REPO_COMMENT_COMMIT);
        } else {
            $activity->setType(Activity::REPO_COMMENT_PR);
        }

        if ($this->lastRepo instanceof Repository) {
            $repo = $this->lastRepo;
        } else {
            $repo = $this->services->get('gitDao')->getById($comment->getRepositoryId());
            if (!$repo instanceof Repository) {
                // should never happend
                return;
            }
        }

        $activity->setUserId($comment->getAuthorId());
        $activity->setCreatedOn($comment->getCreatedOn());
        $activity->setRepositoryId($comment->getRepositoryId());
        $activity->setRepositoryName($repo->getFullname());

        $activity->setMessage($comment->getCommitHash() . ' ' . substr($comment->getContents(), 0, 200));

        $usersDao->saveUserActivity($activity);
    }

    protected function extractRepository($threadName)
    {
        // commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d
        if (preg_match_all('/(commit|compare)\-([0-9]+)\-(.*)/', $threadName, $matches)) {
            $repoId = $matches[2][0];
        } else {
            return null;
        }

        return $this->services->get('gitDao')->getById($repoId);
    }
}