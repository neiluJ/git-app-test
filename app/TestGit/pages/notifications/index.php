<?php $vh = $this->_helper; include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'notifications')); ?>

<div class="container">
    <div class="row" style="margin-top:20px; text-align: center">
        <h1>Notifications</h1>
    </div>
    <div class="row" style="margin-top:40px;">
        <?php $notifMenuActive = $this->channel; include __DIR__ . '/_left.php'; ?>
        <div class="col-md-9">
            <?php if(isset($this->errorMsg) && !empty($this->errorMsg)): ?>
<div class="alert alert-warning">
    <?php echo $vh->escape($this->errorMsg); ?>
</div>
            <?php endif; if (!count($this->notifications)): ?>
<div class="alert alert-info">
    They are no notifications to show in <strong><?php echo $vh->escape($this->channel); ?></strong>.
</div>
            <?php else: ?>
<?php $hasUnread = false; foreach ($this->notifications as $notifUser) { if ($notifUser->isUnread()) { $hasUnread = true; break; }} ?>
            <div class="btn-group pull-right btn-group-sm" style="margin-top: -40px;">
                <a href="<?php echo $vh->url('NotificationsReadAll', array('channel' => $this->channel)); ?>" class="btn btn-default btn-sm<?php if(!count($this->notifications) || !$hasUnread): ?> disabled<?php endif; ?>"><i class="octicon octicon-eye-unwatch"></i> Mark all as read</a>
                <a href="<?php echo $vh->url('NotificationsDeleteAll', array('channel' => $this->channel)); ?>" class="btn btn-default btn-sm<?php if(!count($this->notifications)): ?> disabled<?php endif; ?>"><i class="octicon octicon-x"></i> Delete all</a>
            </div>
            <ol class="list-unstyled notifs">
<?php foreach ($this->notifications as $notifUser): ?>
<li<?php if($notifUser->isUnread()): ?> class="unread"<?php endif; ?>>
    <span class="actions">
        <?php if($notifUser->isUnread()): ?>
            <a href="<?php echo $vh->url('NotificationRead', array('nId' => $notifUser->getNotificationId())); ?>"><i class="octicon octicon-eye-unwatch"></i></a>
        <?php else: ?>
            <a href="<?php echo $vh->url('NotificationRead', array('nId' => $notifUser->getNotificationId())); ?>"><i class="octicon octicon-eye-watch"></i></a>
        <?php endif; ?>
        <a href="<?php echo $vh->url('NotificationDelete', array('nId' => $notifUser->getNotificationId())); ?>"><i class="octicon octicon-x"></i></a>
    </span>
    <b class="<?php echo $notifUser->getNotification()->get()->getIcon(); ?>"></b>
    <span class="date"><?php echo $notifUser->getNotification()->getCreatedOn()->format('d M, Y H:i'); ?></span>
<?php if ($notifUser->getNotification()->getType() == "mention"): ?>
    <a href="#">Bidule</a> mentioned you in a <a href="#">commit</a> on <a href="#">ecommerce/ecommerce</a>:
    <p class="desc"><?php echo $vh->escape($notifUser->getNotification()->getText()); ?></p>
<?php elseif ($notifUser->getNotification()->getType() == "failedlogin"): ?>
    <?php echo $vh->escape($notifUser->getNotification()->getText()); ?>
<?php endif; ?>
</li>
<?php endforeach; ?>
            </ol>
            <?php endif; ?>
        </div>
</div>
<?php include __DIR__ . '/../_footer.php'; ?>
