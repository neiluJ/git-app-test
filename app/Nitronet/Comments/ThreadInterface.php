<?php
namespace Nitronet\Comments;

interface ThreadInterface
{
    /**
     * @param mixed $name
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $open
     */
    public function setOpen($open);

    /**
     * @return mixed
     */
    public function isOpen();

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn);

    /**
     * @return mixed
     */
    public function getCreatedOn();

    /**
     * @param mixed $comments
     */
    public function setComments($commentsCount);

    /**
     * @return mixed
     */
    public function getComments();
}