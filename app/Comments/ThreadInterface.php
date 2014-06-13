<?php
namespace Nitronet\Comments;

use \DateTime;

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
    public function setCreatedOn(DateTime $createdOn);

    /**
     * @return mixed
     */
    public function getCreatedOn();

    /**
     * @param mixed $comments
     */
    public function setComments();

    /**
     * @return mixed
     */
    public function getComments();
}