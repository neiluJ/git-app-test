<?php
namespace Nitronet\Comments;

interface CommentInterface
{
    public function getId();

    public function setId($id);

    public function getParentId();

    public function setParentId($parentId);

    public function getThread();

    public function getAuthorName();

    public function getAuthorEmail();

    public function getAuthorUrl();

    public function isActive();

    public function getContents();

    public function getCreatedOn();

    public function setContents($contents);

    public function setCreatedOn($date);

    public function setActive($active);

    public function setAuthorName($authorName);

    public function setAuthorEmail($authorEmail);

    public function setAuthorUrl($authorUrl);

    public function setThread($thread);
}