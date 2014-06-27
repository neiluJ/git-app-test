<div class="col-md-2 avatar">
    <?php if ($this->profile->isUser()): ?>
        <i class="glyphicon glyphicon-user"></i>
    <?php else: ?>
        <i class="octicon octicon-organization"></i>
        <span class="label label-warning">Organization</span>
    <?php endif; ?>
    <h1><strong><?php echo $vh->escape($this->profile->getUsername()); ?></strong></h1>
    <p><?php echo $vh->escape($this->profile->getFullname()); ?></p>

    <hr />
    <ul class="nav nav-pills nav-stacked" style="margin-top: 20px; text-align: left;">
        <li<?php if ($userMenuActive == "profile"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('Profile', array('username' => $this->profile->getUsername())); ?>"><b class="octicon octicon-repo"></b> Repositories</a></li>
        <?php if ($this->profile->isUser()): ?>
            <li<?php if ($userMenuActive == "activity"): ?> class="active"<?php endif; ?>><a style="padding: 5px 15px;" href="<?php echo $this->_helper->url('UserActivity', array('username' => $this->profile->getUsername())); ?>"><b class="octicon octicon-history"></b> Activity</a></li>
        <?php else: ?>
            <li<?php if ($userMenuActive == "members"): ?> class="active"<?php endif; ?>><a style="padding: 5px 15px;" href="<?php echo $this->_helper->url('OrgMembers', array('username' => $this->profile->getUsername())); ?>"><b class="octicon octicon-organization"></b> Members</a></li>
        <?php endif; ?>
        <?php if ($this->_helper->isAllowed($this->profile, 'edit')): ?>
            <li class="divider"></li>
            <li<?php if ($userMenuActive == "settings"): ?> class="active"<?php endif; ?>><a style="padding: 5px 15px;" href="<?php echo $this->_helper->url('UserSettings', array('username' => $this->profile->getUsername())); ?>"><b class="octicon octicon-tools"></b> Settings</a></li>
        <?php endif; ?>
    </ul>
</div>