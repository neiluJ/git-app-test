<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => '')); ?>

    <div class="container">
       <div class="starter-template">
          <h1>Error</h1>
      </div>

       <div class="well" style="font-weight: bold; text-align:center;">
        <?php echo $this->_helper->escape($this->errorMsg); ?>
       </div>
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>