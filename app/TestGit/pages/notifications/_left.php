<div class="col-md-3">
    <ul class="nav nav-pills nav-stacked" style="text-align: left;">
        <li<?php if ($notifMenuActive == "all"): ?> class="active"<?php endif; ?>><a href="<?php echo $this->_helper->url('Notifications'); ?>"><span class="count pull-right">2</span> General Notifications</a></li>
        <li<?php if ($notifMenuActive == "admin"): ?> class="active"<?php endif; ?>><a href="<?php echo $this->_helper->url('Notifications', array('channel' => 'admin')); ?>"><span class="count pull-right">0</span> Administration</a></li>
        <li class="divider" style="border-bottom:solid 1px #ccc"></li>
    </ul>
</div>