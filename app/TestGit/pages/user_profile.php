<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container">
          <div class="row" style="margin-top:40px;">
            <div class="col-md-2 avatar">
                <?php if ($this->profile->isUser()): ?>
                    <i class="glyphicon glyphicon-user"></i>
                <?php else: ?>
                    <i class="octicon octicon-organization"></i>
                    <span class="label label-default">Organization</span>
                <?php endif; ?>
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
                    <div class="alert alert-warning">This <?php echo $this->_helper->escape($this->profile->getType()); ?> has no public repositories yet.</div>
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
                              <?php if($repo->getParent_id() == null): ?><i class="octicon octicon-repo"></i><?php else: ?><i class="octicon octicon-repo-forked"></i><?php endif; ?> <?php if($repo->isPrivate()): ?><i class="octicon octicon-lock"></i><?php endif; ?> <a class="repo-name" href="<?php echo $this->_helper->url('Repository', array('name' => $repo->getFullname())); ?>"><?php echo $this->_helper->escape($repo->getName()); ?></a> <?php if($repo->getParent_id() != null): ?><span class="fork">forked from <a class="repo-name" href="<?php echo $this->_helper->url('Repository', array('name' => $repo->getParent()->getFullname())); ?>"><?php echo $this->_helper->escape($repo->getParent()->getFullname()); ?></a></span><?php endif; ?>
                              <p class="infos">Created on <span><?php $date = new \DateTime($repo->getCreated_at()); echo $date->format($this->dateFormat); ?></span>. <?php if ($repo->getLast_commit_date() != null): ?>Last updated on <span><?php $date = new \DateTime($repo->getLast_commit_date()); echo $date->format($this->dateFormat); ?></span>.<?php endif; ?></p>
                          </li>
                          <?php endforeach; ?>
                      </ul>
                   </div>
                </div>
                <?php endif; ?>

                <?php if ($this->profile->isUser()): ?>
                <h3>Latest activity</h3> 
                
                <?php echo $this->_helper->embed('Activity', array('user' => $this->profile, 'repositories' => $this->activityRepositories)); ?>
                <?php else: ?>
                <h3>Members</h3>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="width: 15px;">&nbsp;</th>
                        <th style="">User</th>
                        <th style="width: 100px;font-size:12px;text-align: center;">Write Access</th>
                        <th style="width: 100px;font-size:12px;text-align: center;">Repos Admin</th>
                        <th style="width: 110px;font-size:12px;text-align: center;">Members Admin</th>
                        <th style="width: 100px;font-size:12px;text-align: center;">Admin</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $members = $this->profile->getMembers();
                        foreach($members as $member): ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <a href="<?php echo $this->_helper->url('Profile', array('username' => $member->getUser()->getUsername())) ?>"><?php echo $this->_helper->escape($member->getUser()->displayName()); ?></a>
                                <?php if($member->getAdded_by() == $member->getUser_id()): ?>
                                <span class="label label-default">owner</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center">
                                <i class="octicon <?php if($member->getReposWriteAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                            </td>
                            <td style="text-align: center">
                                <i class="octicon <?php if($member->getReposAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                            </td>
                            <td style="text-align: center">
                                <i class="octicon <?php if($member->getMembersAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                            </td>
                            <td style="text-align: center">
                                <i class="octicon <?php if($member->getAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php endif; ?>
            </div>
              <div class="col-md-2">
                  <?php if ($this->profile->isUser()): ?>
                  <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
                  <p class="user-stat"><span class="big-counter"><?php echo $this->totalCommits; ?></span> commits</p>
                    <?php else: ?>
                      <p class="user-stat"><span class="big-counter"><?php echo count($members); ?></span> members</p>
                      <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
                  <?php endif; ?>
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
