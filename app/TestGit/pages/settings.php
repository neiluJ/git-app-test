<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

      <div class="container user-settings">
          <?php $repoMenuActive = 'settings'; include __DIR__ .'/_repository_header.php'; ?>
          
          <div class="row">
              <div class="col-md-10" style="float:none; margin:0 auto;">
                  <div class="row">
                    <div class="col-md-3">
                        <ul class="nav nav-pills nav-stacked">
                            <li class="active"><a href="<?php echo $this->_helper->url('Settings', array('name' => $this->entity->getFullname())); ?>">General Settings</a></li>
                            <li><a href="#">Administration</a></li>
                        </ul>
                    </div>
                    <div class="col-md-8" style="">
                        <?php if ($this->updated == 1): ?>
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Success!</strong> Informations have been updated.
                </div>
                <?php endif; ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h3 class="panel-title">Informations</h3>
                            </div>
                            <div class="panel-body">
                                <?php echo $this->_helper->form($this->generalInfosForm); ?>
                            </div>
                        </div>
                    </div>
                  </div>
              </div>
              <div class="clearfix"></div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
