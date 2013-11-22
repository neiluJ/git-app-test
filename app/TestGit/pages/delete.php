<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

      <div class="container">
          <?php $repoMenuActive = 'delete'; include __DIR__ .'/_repository_header.php'; ?>
          
          <div class="row">
              <div class="col-md-6" style="margin: 0 auto; float:none;">
                  <h1>Delete this Repository</h1>
                  <p>
                      You're about to <strong>delete this repository</strong>. 
                    This will remove all files, branches, tags, history <strong>permanently</strong> ! 
                  </p>
                  
                  <form action="<?php echo $this->_helper->url('Delete', array('name' => $this->entity->getFullname())); ?>" method="post">
                      <div class="form-group">
                          <input type="submit" value="Delete repository" class="btn btn-danger">
                          - <a href="<?php echo $this->_helper->url('Repository', array('name' => $this->entity->getFullname())); ?>" class="btn btn-default">Cancel</a>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
