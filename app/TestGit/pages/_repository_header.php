<div class="starter-template">
    <div class="repo-title">
        <div class="collapse navbar-collapse repo-nav">
          <ul class="nav navbar-nav navbar-left">
              <li<?php if($repoMenuActive == 'code'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('Repository', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Browse source"><i class="glyphicon glyphicon-list"></i></a></li>
              <li<?php if($repoMenuActive == 'branches'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('Branches', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Branches/Tags" class="txt"><i class="glyphicon glyphicon-random"></i> <span><?php echo (strlen($this->branch) == 40 ? substr($this->branch, 0, 6) : $this->branch); ?></span></a></li>
              <li<?php if($repoMenuActive == 'accesses'): ?> class="active"<?php endif; ?>><a href="<?php echo $vh->url('Accesses', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Access Rights"><i class="glyphicon glyphicon-user"></i></a></li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo $this->_helper->url('Fork', array('name' => $this->entity->getFullname())); ?>"><i class="glyphicon glyphicon-random"></i> Fork</a></li>
                    <li><a href="<?php echo $this->_helper->url('Settings', array('name' => $this->entity->getFullname())); ?>"><i class="glyphicon glyphicon-cog"></i> Settings</a></li>
                  </ul>
              </li>
          </ul>
      </div>
        <h1><?php $own = $this->entity->getOwner_id(); if(!empty($own)): ?><a id="repoOwner" href="<?php echo $vh->url('Profile', array('username' => $this->entity->getOwner()->getUsername()), true); ?>"><?php echo $this->entity->getOwner()->getUsername(); ?></a><?php else: ?>special<?php endif; ?>/<a href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->_helper->escape($this->entity->getName()); ?></a> <?php if($this->entity->getParent_id() != null): ?><span class="fork">forked from <a class="repo-name" href="<?php echo $this->_helper->url('Repository', array('name' => $this->entity->getParent()->getFullname())); ?>"><?php echo $this->_helper->escape($this->entity->getParent()->getFullname()); ?></a></span><?php endif; ?></h1>
        <div class="clearfix"></div>
        <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div> 
        <p><?php echo $this->_helper->escape($this->entity->getDescription()); ?></p>
    </div>
</div><!-- /starter-template -->
