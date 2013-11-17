<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container">
          <div class="starter-template">
              <h1><?php echo $vh->escape($this->profile->getUsername()); ?></h1>  
              <p><?php echo $vh->escape($this->profile->getFullname()); ?></p>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
