<?php
namespace Nitronet\Comments;

interface CommentFormInterface
{
    public function getParentId();

    public function getAuthorName();

    public function getAuthorEmail();

    public function getAuthorUrl();

    public function getComment();
}