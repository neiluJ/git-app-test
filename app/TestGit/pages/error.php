<?php $vh = $this->_helper; ?>
<?php $page_title = "/!\\ Error"; include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => '')); ?>

    <div class="container">
       <div class="starter-template">
          <h1>Error</h1>
      </div>

       <div class="well" style="font-weight: bold; text-align:left;">
        <pre><?php echo $this->_helper->escape($this->errorMsg); ?></pre>
       </div>
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>