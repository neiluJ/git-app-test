<div class="starter-template">
    <div class="repo-title">
        <?php if($this->_helper->isAllowed($this->entity, 'read')): ?>
        <div class="collapse navbar-collapse repo-nav">
          <ul class="nav navbar-nav navbar-left">
              <li<?php if($repoMenuActive == 'code'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('Repository', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Browse source"><i class="octicon octicon-code"></i></a></li>
              <li<?php if($repoMenuActive == 'branches'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('Branches', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Branches/Tags" class="txt"><i class="octicon octicon-git-branch"></i> <span><?php echo (strlen($this->branch) == 40 ? substr($this->branch, 0, 6) : $this->branch); ?></span></a></li>
              <li<?php if($repoMenuActive == 'activity'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('RepoActivity', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Activity" class="txt"><i class="octicon octicon-history"></i></a></li>
              <?php if ($this->_helper->isAllowed($this->entity, 'admin')): ?>
              <li<?php if($repoMenuActive == 'accesses'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('Accesses', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Access Rights"><i class="octicon octicon-organization"></i></a></li>
              <?php endif; ?>
          </ul>

          <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="octicon octicon-gear"></i></a>
                  <ul class="dropdown-menu">
                    <?php if ($this->_helper->isAllowed('repository', 'create') && $this->_helper->isAllowed($this->entity, 'read')): ?>
                    <li><a href="<?php echo $this->_helper->url('Fork', array('name' => $this->entity->getFullname())); ?>"><i class="octicon octicon-repo-forked"></i> Fork</a></li>
                    <?php endif; ?>
                    <?php if ($this->_helper->isAllowed($this->entity, 'admin')): ?>
                    <li><a href="<?php echo $this->_helper->url('Settings', array('name' => $this->entity->getFullname())); ?>"><i class="octicon octicon-tools"></i> Settings</a></li>
                    <?php endif; ?>
                    <?php if ($this->_helper->isAllowed('repository', 'create') && $this->_helper->isAllowed($this->entity, 'owner')): ?>
                    <li class="divider"></li>
                    <li><a href="<?php echo $this->_helper->url('Delete', array('name' => $this->entity->getFullname())); ?>"><i class="glyphicon glyphicon-remove"></i> Delete</a></li>
                    <?php endif; ?>
                  </ul>
              </li>
          </ul>
      </div>
        <?php endif; ?>
        <h1><?php $own = $this->entity->getOwner_id(); if(!empty($own)): ?><a id="repoOwner" href="<?php echo $vh->url('Profile', array('username' => $this->entity->getOwner()->getUsername()), true); ?>"><?php echo $this->entity->getOwner()->getUsername(); ?></a><?php else: ?>special<?php endif; ?>/<a href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->_helper->escape($this->entity->getName()); ?></a> <?php if($this->entity->getParent_id() != null && $this->_helper->isAllowed($this->entity, 'read')): ?><span class="fork">forked from <a class="repo-name" href="<?php echo $this->_helper->url('Repository', array('name' => $this->entity->getParent()->getFullname())); ?>"><?php echo $this->_helper->escape($this->entity->getParent()->getFullname()); ?></a></span><?php endif; ?></h1>
        <div class="clearfix"></div>
        <?php if($this->_helper->isAllowed($this->entity, 'read')): ?>
        <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div> 
        <p><?php if($this->entity->isPrivate()): ?><i class="octicon octicon-lock"></i> <strong>private repository</strong> - <?php endif; ?> <?php echo $this->_helper->escape($this->entity->getDescription()); ?> <?php $ws = $this->entity->getWebsite(); if(!empty($ws)): ?>- <a href="<?php echo $this->_helper->escape($ws); ?>"><?php echo $this->_helper->escape($ws); ?></a><?php endif; ?></p>
        <?php endif; ?>
    </div>
</div><!-- /starter-template -->
