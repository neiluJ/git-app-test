<div class="col-md-2 avatar">
    <?php if ($this->profile->isUser()): ?>
        <i class="glyphicon glyphicon-user"></i>
    <?php else: ?>
        <i class="octicon octicon-organization"></i>
        <span class="label label-warning">Organization</span>
    <?php endif; ?>
    <h1 class="profile"><strong><?php echo $vh->escape($this->profile->getUsername()); ?></strong></h1>
    <p class="profile-info"><?php echo $vh->escape($this->profile->getFullname()); ?></p>

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

    <?php if(count($this->organizations)): ?>
    <hr />
        <h6 style="margin-bottom: 5px;font-weight: bold;">Organizations</h6>
    <ul class="nav nav-pills nav-stacked" style=" text-align: left;">
        <?php foreach($this->organizations as $org): ?>
        <li><a href="<?php echo $this->_helper->url('Profile', array('username' => $org->getUsername())); ?>"  style="padding: 5px 15px;"><b class="octicon octicon-organization"></b> <?php echo $this->_helper->escape($org->getUsername()); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>