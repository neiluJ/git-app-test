<li<?php if($this->inNotifications): $vh = $this->_helper ?> class="active"<?php endif; ?>>
    <?php if($this->counters['__total'] > 0): ?>
    <span class="notif-count"><?php echo $this->counters['__total']; ?></span>
    <?php endif; ?>
    <a href="<?php echo $this->_helper->url('Notifications'); ?>"><i class="octicon octicon-megaphone"></i></a>
</li>
