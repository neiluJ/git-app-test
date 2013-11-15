<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu'); ?>

      <div class="container">
          <div class="starter-template">
              <h1>Login</h1>  
            
<?php if($this->loginForm->isSubmitted() && count($this->loginForm->getErrors())): ?>
<div class="alert alert-danger">
     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php foreach ($this->loginForm->getErrors() as $error): ?>
        <?php echo $vh->escape($error); ?><br />
    <?php endforeach; ?>
</div>
<?php endif; ?>
        
              
              <?php echo $vh->form($this->loginForm); ?>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
