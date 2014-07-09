<div class="col-md-2 avatar">
        <i class="octicon <?php if($this->entity->hasParent()): ?>octicon-repo-forked<?php else: ?>octicon-repo<?php endif; ?>"></i>
    <?php if(!$this->entity->isPrivate()): ?>
        <span class="label label-success">public</span>
    <?php else: ?>
        <span class="label label-danger">private</span>
    <?php endif; ?>

    <h1 class="profile"><a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname())); ?>"><?php echo $vh->escape($this->entity->getName()); ?></a></h1>

    <?php if($this->entity->getOwner_id() != null): ?>
    <p class="profile-info">@<a href="<?php echo $this->_helper->url('Profile', array('username' => $this->entity->getOwner()->getUsername())); ?>"><?php echo $vh->escape($this->entity->getOwner()->getUsername()); ?></a></p>
    <?php else: ?>
    <br />
    <?php endif; ?>


    <?php $this->entity->hasParent(); if ($this->entity->hasParent()): ?>
        <p class="profile-info"><b class="octicon octicon-repo-forked"></b> <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getParent()->getFullname())); ?>"><?php echo $vh->escape($this->entity->getParent()->getFullname()); ?></a></p>
    <?php endif; ?>

    <?php if ($this->entity->getDescription() != null): ?>
    <p class="profile-info">
        <?php echo $vh->escape($this->entity->getDescription()); ?>
        <?php $ws = $this->entity->getWebsite(); if(!empty($ws)): ?><br /><a href="<?php echo $this->_helper->escape($ws); ?>"><?php echo $this->_helper->escape($ws); ?></a><?php endif; ?>
    </p>
    <?php endif; ?>

    <hr />
    <!-- Split button -->
    <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><b class="octicon octicon-git-branch"></b> <?php echo $this->_helper->escape((strlen($this->branch) > 12 ? substr($this->branch, 0, 9) .'...' : $this->branch)); ?> <b class="caret"></b></button>
        <a href="<?php echo $this->_helper->url('CompareNEW', array('name' => $this->entity->getFullname())); ?>" class="btn btn-success"><b class="octicon octicon-git-compare"></b></a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
        </ul>
    </div>

    <hr />
    <?php if($this->_helper->isAllowed($this->entity, 'read')): ?>
    <ul class="nav nav-pills nav-stacked" style="margin-top: 20px; text-align: left;">
        <li<?php if ($repoMenuActive == "code"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><b class="octicon octicon-code"></b> Browse</a></li>
        <li<?php if ($repoMenuActive == "commits"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('CommitsNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><b class="octicon octicon-git-commit"></b> Commits</a></li>
        <li<?php if ($repoMenuActive == "activity"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('ActivityNEW', array('name' => $this->entity->getFullname())); ?>"><b class="octicon octicon-history"></b> Activity</a></li>
        <li<?php if ($repoMenuActive == "branches"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('BranchesNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><b class="octicon octicon-git-branch"></b> Branches &amp; Tags</a></li>
        <?php if ($this->_helper->isAllowed($this->entity, 'admin')): ?>
        <li<?php if ($repoMenuActive == "accesses"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('AccessesNEW', array('name' => $this->entity->getFullname())); ?>"><b class="octicon octicon-organization"></b> Access Rights</a></li>
        <li class="divider"></li>
        <li<?php if ($repoMenuActive == "settings"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('SettingsNEW', array('name' => $this->entity->getFullname())); ?>"><b class="octicon octicon-tools"></b> Settings</a></li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
</div>