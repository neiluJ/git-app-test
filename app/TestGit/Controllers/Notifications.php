<?php
namespace TestGit\Controllers;

use Fwk\Core\Context;
use Fwk\Core\ContextAware;
use Fwk\Core\ServicesAware;
use Fwk\Di\Container;
use Fwk\Core\Action\Result;
use TestGit\Model\Notifications\Notification;
use TestGit\Model\Notifications\NotificationsDao;

class Notifications implements ServicesAware, ContextAware
{
    public $errorMsg;
    public $channel = Notification::CHANNEL_GENERAL;
    public $nId;

    protected $services;
    protected $context;

    protected $user;
    protected $inNotifications = false;
    protected $notifications;
    protected $channels;
    protected $counters;

    public function show()
    {
        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();
        } catch(\Exception $e) {
            return Result::ERROR;
        }

        $this->channels = $this->notifications()->getChannelsForUser($this->user);
        $this->counters = $this->notifications()->getNotificationsCount($this->user, array_keys($this->channels));

        try {
            $this->notifications = $this->notifications()->getForUser($this->user, array($this->channel));
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
        }

        return Result::SUCCESS;
    }
    
    public function menu()
    {
        // probably not an embeded request
        if (!$this->getContext()->hasParent()) {
            return Result::SUCCESS;
        }

        $this->inNotifications = ($this->getContext()->hasParent() && $this->context->getParent()->getParent()->getParent()->getParent()->getActionName() == "Notifications");

        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();

            $this->counters = $this->notifications()->getNotificationsCount($this->user);
        } catch(\Exception $e) {
            return Result::ERROR;
        }

        return Result::SUCCESS;
    }

    public function read()
    {
        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();
        } catch(\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return Result::ERROR;
        }

        try {
            $this->notifications()->read($this->user, (int)$this->nId);
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        return Result::SUCCESS;
    }

    public function readAll()
    {
        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();
        } catch(\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return Result::ERROR;
        }

        if (empty($this->channel)) {
            $this->errorMsg = 'Empty channel';
            return Result::ERROR;
        }

        try {
            $this->notifications()->readAll($this->user, $this->channel);
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        return Result::SUCCESS;
    }

    public function delete()
    {
        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();
        } catch(\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return Result::ERROR;
        }

        try {
            $this->notifications()->delete($this->user, (int)$this->nId);
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        return Result::SUCCESS;
    }

    public function deleteAll()
    {
        try {
            $this->user = $this->getServices()
                ->get('security')
                ->getUser();
        } catch(\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return Result::ERROR;
        }

        if (empty($this->channel)) {
            $this->errorMsg = 'Empty channel';
            return Result::ERROR;
        }

        try {
            $this->notifications()->deleteAll($this->user, $this->channel);
        } catch(\Exception $exp) {
            $this->errorMsg = $exp->getMessage();
            return Result::ERROR;
        }

        return Result::SUCCESS;
    }

    /**
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(Container $services)
    {
        $this->services = $services;
    }
    
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return boolean
     */
    public function getInNotifications()
    {
        return $this->inNotifications;
    }

    /**
     * Sets current context
     *
     * @param \Fwk\Core\Context $context Current context
     *
     * @return void
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return NotificationsDao
     */
    protected function notifications()
    {
        return $this->getServices()->get('notifications');
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @return mixed
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @return mixed
     */
    public function getCounters()
    {
        return $this->counters;
    }
}