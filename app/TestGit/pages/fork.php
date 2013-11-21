<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

      <div class="container">
          <?php $repoMenuActive = 'fork'; include __DIR__ .'/_repository_header.php'; ?>
          
          <div class="row">
              <div class="col-md-6" style="margin: 0 auto; float:none;">
                  <h1>Fork this Repository</h1>
                  
                  <?php echo $vh->form($this->forkForm); ?>
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
