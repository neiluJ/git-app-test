<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container">
          <div class="row" style="margin-top:40px;">
            <div class="col-md-2 avatar">
                <i class="glyphicon glyphicon-user"></i>
                <h1><strong><?php echo $vh->escape($this->profile->getUsername()); ?></strong></h1>  
                <p><?php echo $vh->escape($this->profile->getFullname()); ?></p>
                
                <div class="btn-group">
                    <?php if ($this->_helper->isAllowed($this->profile, 'edit')): ?>
                    <a href="<?php echo $this->_helper->url('UserSettings', array('username' => $this->profile->getUsername())); ?>" class="btn btn-sm btn-default">Settings</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-8">
                <?php if(!count($this->repositories)): ?>
                    <div class="alert alert-warning">This user has no public repositories yet.</div>
                <?php else: ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                      <form role="form" class="pull-right filter">
                          <input type="search" class="form-control input-sm" placeholder="Filter repositories">
                      </form>
                    <h3 class="panel-title">Repositories</h3>
                  </div>
                  <div class="panel-body">
                      <ul class="repositories">
                          <?php foreach ($this->repositories as $repo): ?>
                          <li>
                              <?php if ($this->_helper->isAllowed('repository', 'create')): ?>
                              <div class="btn-group pull-right">
                                <a href="<?php echo $this->_helper->url('Fork', array('name' => $repo->getFullname())); ?>" class="btn btn-sm btn-default">Fork</a>
                                 <?php if ($this->_helper->isAllowed($repo, 'owner')): ?>
                                 <a href="<?php echo $this->_helper->url('Delete', array('name' => $repo->getFullname())); ?>" class="btn btn-sm btn-danger">Delete</a>
                                <?php endif; ?>
                              </div>
                              <?php endif; ?>
                              <?php if($repo->getParent_id() == null): ?><i class="glyphicon glyphicon-list"></i><?php else: ?><i class="glyphicon glyphicon-random"></i><?php endif; ?> <a class="repo-name" href="<?php echo $this->_helper->url('Repository', array('name' => $repo->getFullname())); ?>"><?php echo $this->_helper->escape($repo->getName()); ?></a> <?php if($repo->getParent_id() != null): ?><span class="fork">forked from <a class="repo-name" href="<?php echo $this->_helper->url('Repository', array('name' => $repo->getParent()->getFullname())); ?>"><?php echo $this->_helper->escape($repo->getParent()->getFullname()); ?></a></span><?php endif; ?>
                              <p class="infos">Created on <span><?php $date = new \DateTime($repo->getCreated_at()); echo $date->format($this->dateFormat); ?></span>. <?php if ($repo->getLast_commit_date() != null): ?>Last updated on <span><?php $date = new \DateTime($repo->getLast_commit_date()); echo $date->format($this->dateFormat); ?></span>.<?php endif; ?></p>
                          </li>
                          <?php endforeach; ?>
                      </ul>
                   </div>
                </div>
                <?php endif; ?>
                    
                <h3>Latest activity</h3> 
                
                <?php echo $this->_helper->embed('Activity', array('user' => $this->profile, 'repositories' => $this->repositories)); ?>
            </div>
              <div class="col-md-2">
                  <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
                  <p class="user-stat"><span class="big-counter"><?php echo $this->totalCommits; ?></span> commits</p>
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
