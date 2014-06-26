<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container user-settings">
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
            </div>
            <div class="col-md-8">
                <?php if ($this->updated == 1): ?>
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Success!</strong> Your profile informations have been updated.
                </div>
                <?php endif; ?>
                <p><a href="<?php echo $this->_helper->url('Profile', array('username' => $this->profile->getUsername())); ?>">&LongLeftArrow; Back to profile</a></p>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">General Settings</h3>
                  </div>
                  <div class="panel-body">
                      <?php echo $this->_helper->form($this->generalInfosForm); ?>
                   </div>
                </div>
                <?php if ($this->profile->isUser()): ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">SSH Keys</h3>
                  </div>
                  <div class="panel-body">
                      <?php
                    $keys = $this->profile->getSshKeys();
                    if (!count($keys)):
                    ?>
                    <div class="alert alert-warning">
                        <strong>Argh!</strong> No SSH key(s) is set (= no git ssh access).
                    </div>
                    <?php else: ?>
                      <ol class="ssh-keys">
                          <?php foreach ($keys as $key): ?>
                          <li>
                              <a href="<?php echo $this->_helper->url('RevokeSshKey', array('username' => $this->profile->getUsername(), 'id' => $key->id), true); ?>" class="btn btn-danger btn-xs pull-right">revoke</a>
                              <strong><?php echo $key->title; ?></strong> <span>added on <?php $date = new \DateTime($key->created_on); echo $date->format($this->dateFormat); ?></span>
                          </li>
                          <?php endforeach; ?>
                      </ol>
                    <?php endif; ?>
                      <hr />
                      <h4>Add SSH Key</h4>
                      <?php echo $this->_helper->form($this->sshKeyForm); ?>
                   </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="panel-title">Change password</h3>
                  </div>
                  <div class="panel-body">
                      <?php echo $this->_helper->form($this->changePasswordForm); ?>
                   </div>
                </div>
                <?php endif; ?>
                <div class="panel panel-default panel-warning">
                  <div class="panel-heading">
                    <h3 class="panel-title">Administration</h3>
                  </div>
                  <div class="panel-body">
                      <p>Admin</p>
                   </div>
                </div>
            </div>
              <div class="col-md-2">
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
