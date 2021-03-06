<?php $vh = $this->_helper; ?>
<?php $page_title = $this->entity->getFullname() ." - Access Rights"; include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="row" style="margin-top:40px;">
            <?php $repoMenuActive = "accesses"; include __DIR__ . '/_left.php'; ?>
            <div class="col-md-8">
                <?php if($this->_helper->isAllowed($this->entity, 'admin')): ?>
                <button type="button" data-toggle="modal" data-target="#addModal" class="btn btn-primary pull-right">Add</button>
                <?php endif; ?>
                <h3 style="margin-top:0">Access List <i class="mega-octicon octicon-organization"></i></h3>

                <form role="form" action="<?php echo $this->_helper->url('AccessesNEW', array('name' => $this->name), true); ?>" method="post">
                <table class="table table-striped" style="margin-top:20px;">
                    <thead>
                      <tr>
                        <th style="width: 35px;">&nbsp;</th>
                        <th>User</th>
                        <th style="width: 40px;text-align:center">Read</th>
                        <th style="width: 40px;text-align:center">Write</th>
                        <th style="width: 40px;text-align:center">Write+</th>
                        <th style="width: 40px;text-align:center">Admin</th>
                        <th style="width: 100px;">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach($this->accesses as $access): ?>
                      <tr>
                        <td style="text-align:center"><input type="hidden" name="access[<?php echo $access->getUser_id(); ?>][exists]" value="1" /></td>
                        <td><a href="<?php echo $this->_helper->url('Profile', array('username' => $access->getUser()->getUsername()), true); ?>"><?php $fn = $access->getUser()->getFullname(); echo $this->_helper->escape(empty($fn) ? $access->getUser()->getUsername() : ($access->getUser()->isOrganization() ? '@'. $access->getUser()->getUsername() .' ('. $access->getUser()->getFullname() .')' : $access->getUser()->getFullname())); ?></a></td>
                        <td style="text-align:center"><input type="checkbox" name="access[<?php echo $access->getUser_id(); ?>][read]" id="access_<?php echo $access->getUser_id(); ?>_read"<?php if((bool)$access->getReadAccess() == true): ?> checked="checked"<?php endif; ?> /></td>
                        <td style="text-align:center"><input type="checkbox" name="access[<?php echo $access->getUser_id(); ?>][write]" id="access_<?php echo $access->getUser_id(); ?>_write"<?php if((bool)$access->getWriteAccess() == true): ?> checked="checked"<?php endif; ?> /></td>
                        <td style="text-align:center"><input type="checkbox" name="access[<?php echo $access->getUser_id(); ?>][special]" id="access_<?php echo $access->getUser_id(); ?>_special"<?php if((bool)$access->getSpecialAccess() == true): ?> checked="checked"<?php endif; ?> /></td>
                        <td style="text-align:center"><input type="checkbox" name="access[<?php echo $access->getUser_id(); ?>][admin]" id="access_<?php echo $access->getUser_id(); ?>_admin"<?php if((bool)$access->getAdminAccess() == true): ?> checked="checked"<?php endif; ?> /></td>
                        <td style="text-align:center"><a href="<?php echo $this->_helper->url('RemoveAccess', array('name' => $this->name, 'userId' => $access->getUser_id()), true); ?>">remove</a></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="submit" class="btn btn-default" value="Apply changes" />
                </form>
            </div>
            <div class="col-md-2">
                <?php if($this->_helper->isAllowed($this->entity, 'read')): ?>
                    <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div>

                    <div class="btn-group btn-group-sm" style="margin-top:10px;">
                        <a href="<?php echo $vh->url('Archive', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'format' => 'zip'), true); ?>" class="btn btn-default"><b class="octicon octicon-cloud-download"></b> Download <strong>ZIP</strong></a>
                        <a href="<?php echo $vh->url('Archive', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'format' => 'tar.gz'), true); ?>" class="btn btn-default"><strong>TAR</strong></a>
                    </div>
                    <small style="display: block; text-align: center; color: #ccc; margin-top: 5px;">Download the contents of <strong><?php echo $vh->escape($this->entity->getFullname()); ?></strong> at <strong><?php echo $vh->escape($this->branch); ?></strong></small>
                <?php endif; ?>
            </div>
        </div><!-- /row -->
    </div><!-- /.container -->

<?php if($this->_helper->isAllowed($this->entity, 'admin')): ?>  
<div class="modal fade" id="addModal">
  <div class="modal-dialog">
      <form role="form" method="post" action="<?php echo $this->_helper->url('AddAccess', array('name' => $this->name)); ?>">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Add Repository Access</h4>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <label for="userid">User</label>
                <select name="userid" class="form-control" id="userid">
                    <?php foreach ($this->users as $user): ?>
                    <option value="<?php echo $user->getId(); ?>"><?php $fn = $user->getFullname(); echo $this->_helper->escape(empty($fn) ? $user->getUsername() : $user->getFullname()); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">Can't find the people you're looking for ? <a href="<?php echo $this->_helper->url('Users'); ?>">Add them here</a></span>
          </div>
        <div class="form-group">
            <label>Access Rights</label>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="read" id="read"> Read Access
                </label>
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="write" id="write"> Write Access
                </label>
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="special" id="special"> Write+ Access
                </label>
          </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="admin" id="admin"> Admin Access
                </label>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
<?php endif; ?>

<?php include __DIR__ . '/../_footer.php'; ?>