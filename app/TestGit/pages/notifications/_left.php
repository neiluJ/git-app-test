<div class="col-md-3">
    <ul class="nav nav-pills nav-stacked" style="text-align: left;">
        <li<?php if ($notifMenuActive == "general"): ?> class="active"<?php endif; ?>><a href="<?php echo $this->_helper->url('Notifications'); ?>"><span class="count pull-right"><?php echo (isset($this->counters['general']) ? $this->counters['general'] : null); ?></span> General</a></li>
        <?php if (array_key_exists('admin', $this->channels)): ?>
        <li<?php if ($notifMenuActive == "admin"): ?> class="active"<?php endif; ?>><a href="<?php echo $this->_helper->url('Notifications', array('channel' => 'admin')); ?>"><?php echo (isset($this->counters['admin']) ? $this->counters['admin'] : null); ?> Administration</a></li>
        <?php endif; ?>
        <li class="divider" style="border-bottom:solid 1px #ccc"></li>
        <?php $hasOrgs = false; foreach ($this->channels as $chan => $display) {
            if (strpos($chan, \TestGit\Model\Notifications\Notification::CHANNEL_ORGANIZATION, 0) !== false): $hasOrgs = true; ?>
            <li<?php if ($notifMenuActive == $chan): ?> class="active"<?php endif; ?>><a style="padding: 5px 15px !important;" href="<?php echo $this->_helper->url('Notifications', array('channel' => $chan)); ?>"><span class="count pull-right"><?php echo (isset($this->counters[$chan]) ? $this->counters[$chan] : null); ?></span> <i class="octicon octicon-organization"></i> <?php echo $this->_helper->escape($display); ?></a></li>
        <?php endif; } ?>
        <?php if ($hasOrgs): ?>
        <li class="divider" style="border-bottom:solid 1px #ccc"></li>
        <?php endif; ?>
        <?php foreach ($this->channels as $chan => $display) {
            if (strpos($chan, \TestGit\Model\Notifications\Notification::CHANNEL_REPOSITORY, 0) !== false): ?>
                <li<?php if ($notifMenuActive == $chan): ?> class="active"<?php endif; ?>><a style="padding: 5px 15px !important;" href="<?php echo $this->_helper->url('Notifications', array('channel' => $chan)); ?>"><span class="count pull-right"><?php echo (isset($this->counters[$chan]) ? $this->counters[$chan] : null); ?></span> <i class="octicon octicon-repo"></i> <?php echo $this->_helper->escape($display); ?></a></li>
            <?php endif; } ?>
    </ul>
</div>