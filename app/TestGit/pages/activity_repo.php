<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <?php $repoMenuActive = 'activity'; include __DIR__ .'/_repository_header.php'; ?>
        
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <h3>Latest Activity <i class="mega-octicon octicon-history"></i></h3>
            
            <?php echo $this->_helper->embed('Activity', array('repositories' => array($this->entity))); ?>
        </div>
    </div><!-- /row -->
    
</div><!-- /.container -->
<?php include __DIR__ .'/_footer.php'; ?>