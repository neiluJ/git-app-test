<?php $vh = $this->_helper; ?>
<?php $page_title = "Create Repository"; include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'create')); ?>

      <div class="container">
          <div class="row" style="margin-top:40px;">
              <div class="col-md-6" style="margin: 0 auto; float:none;">
                  <h1>Create Repository <i class="octicon mega-octicon octicon-repo"></i></h1>
                  
                  <?php echo $vh->form($this->createForm); ?>
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
