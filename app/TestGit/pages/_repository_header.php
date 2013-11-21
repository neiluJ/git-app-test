<div class="starter-template">
    <div class="repo-title">
        <div class="collapse navbar-collapse repo-nav">
          <ul class="nav navbar-nav navbar-left">
              <li><a href="<?php echo $vh->url('Repository', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Browse source"><i class="glyphicon glyphicon-list"></i></a></li>
              <li><a href="<?php echo $vh->url('Branches', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Branches/Tags" class="txt"><i class="glyphicon glyphicon-random"></i> <span><?php echo (strlen($this->branch) == 40 ? substr($this->branch, 0, 6) : $this->branch); ?></span></a></li>
              <li><a href="<?php echo $vh->url('Accesses', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Access Rights"><i class="glyphicon glyphicon-user"></i></a></li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i></a>
                  <ul class="dropdown-menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li><a href="#">Separated link</a></li>
                    <li><a href="#">One more separated link</a></li>
                  </ul>
              </li>
          </ul>
      </div>
        <h1><?php $own = $this->entity->getOwner_id(); if(!empty($own)): ?><a id="repoOwner" href="<?php echo $vh->url('Profile', array('username' => $this->entity->getOwner()->getUsername()), true); ?>"><?php echo $this->entity->getOwner()->getUsername(); ?></a><?php else: ?>special<?php endif; ?>/<a href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->_helper->escape($this->entity->getName()); ?></a></h1>
        <div class="clearfix"></div>
         <p class="help-clone">git clone https://<?php echo $this->_helper->escape($this->cloneHost); ?>/<?php echo $this->_helper->escape($this->entity->getFullname()); ?>.git</p>
        <p><?php echo $this->_helper->escape($this->entity->getDescription()); ?></p>
    </div>
</div><!-- /starter-template -->
