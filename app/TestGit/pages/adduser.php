<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu'); ?>

      <div class="container">
          <div class="starter-template">
              <h1>Add User</h1>  
        
<?php echo $vh->form($this->addUserForm); ?>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
