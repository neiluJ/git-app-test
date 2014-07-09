<?php $vh = $this->_helper; ?>
<?php $page_title = $this->profile->getUsername() . " Members"; include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container">
          <div class="row" style="margin-top:40px;">
            <?php $userMenuActive = "members"; include __DIR__ .'/_user_left.php'; ?>
            <div class="col-md-8">
                <?php if ($this->profile->isOrganization()): ?>
                <h3 style="margin-top:0;">Members of <b>@<?php echo $this->_helper->escape($this->profile->getUsername()); ?></b></h3>
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
                            <?php if ($this->_helper->isAllowed($this->profile, 'edit-members') || $this->_helper->isAllowed($this->profile, 'admin')): ?>
                            <td style="text-align: center">
                                <a href="<?php echo $this->_helper->url('ToggleUserRight', array('username' => $this->profile->getUsername(), 'target' => $member->getUser_id(), 'right' => 'write')); ?>"><i class="octicon <?php if($member->getReposWriteAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i></a>
                            </td>
                            <?php else: ?>
                            <td style="text-align: center">
                                <i class="octicon <?php if($member->getReposWriteAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                            </td>
                            <?php endif; ?>
                            <?php if ($this->_helper->isAllowed($this->profile, 'edit-members') || $this->_helper->isAllowed($this->profile, 'admin')): ?>
                                <td style="text-align: center">
                                    <a href="<?php echo $this->_helper->url('ToggleUserRight', array('username' => $this->profile->getUsername(), 'target' => $member->getUser_id(), 'right' => 'repos')); ?>"><i class="octicon <?php if($member->getReposAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i></a>
                                </td>
                            <?php else: ?>
                                <td style="text-align: center">
                                    <i class="octicon <?php if($member->getReposAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                                </td>
                            <?php endif; ?>
                            <?php if ($this->_helper->isAllowed($this->profile, 'edit-members') || $this->_helper->isAllowed($this->profile, 'admin')): ?>
                                <td style="text-align: center">
                                    <a href="<?php echo $this->_helper->url('ToggleUserRight', array('username' => $this->profile->getUsername(), 'target' => $member->getUser_id(), 'right' => 'members')); ?>"><i class="octicon <?php if($member->getMembersAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i></a>
                                </td>
                            <?php else: ?>
                                <td style="text-align: center">
                                    <i class="octicon <?php if($member->getMembersAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                                </td>
                            <?php endif; ?>
                            <?php if ($this->_helper->isAllowed($this->profile, 'edit-members') || $this->_helper->isAllowed($this->profile, 'admin')): ?>
                                <td style="text-align: center">
                                    <a href="<?php echo $this->_helper->url('ToggleUserRight', array('username' => $this->profile->getUsername(), 'target' => $member->getUser_id(), 'right' => 'admin')); ?>"><i class="octicon <?php if($member->getAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i></a>
                                </td>
                            <?php else: ?>
                                <td style="text-align: center">
                                    <i class="octicon <?php if($member->getAdminAccess()): ?>octicon-check<?php else: ?>octicon-x<?php endif; ?>"></i>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning">This user is not an organization.</div>
                <?php endif; ?>
            </div>
              <?php include __DIR__ .'/_user_right.php'; ?>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
