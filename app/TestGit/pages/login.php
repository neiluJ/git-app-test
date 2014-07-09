<?php $vh = $this->_helper; ?>
<?php $page_title = "Login"; include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu'); ?>

      <div class="container">
          <div class="starter-template">
              <h1>Login</h1>  
        
<?php echo $vh->form($this->loginForm); ?>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
