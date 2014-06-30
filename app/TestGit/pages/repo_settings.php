<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "settings"; include __DIR__ .'/_repository_left.php'; ?>
        <div class="col-md-8">
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
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
