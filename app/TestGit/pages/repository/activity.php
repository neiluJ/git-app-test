<?php $vh = $this->_helper; ?>
<?php $page_title = $this->entity->getFullname() . " - Activity"; include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "activity"; include __DIR__ . '/_left.php'; ?>
    <div class="col-md-8">

            <h3 style="margin-top:0">Latest Activity <i class="mega-octicon octicon-history"></i></h3>
            
            <?php echo $this->_helper->embed('Activity', array('repositories' => array($this->entity))); ?>
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ . '/../_footer.php'; ?>