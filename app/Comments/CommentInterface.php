<?php
namespace Nitronet\Comments;

interface CommentInterface
{
    public function getThread();

    public function getAuthorName();

    public function getAuthorEmail();

    public function isActive();

    public function getContents();

    public function getDatePosted();

    public function setContents($contents);

    public function setDatePosted(\DateTime $date);

    public function setActive($active);

    public function setAuthorName($authorName);

    public function setAuthorEmail($authorEmail);

    public function setThread(ThreadInterface $thread);
}