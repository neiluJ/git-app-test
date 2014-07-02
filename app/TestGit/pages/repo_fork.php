<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "none"; include __DIR__ .'/_repository_left.php'; ?>
    <div class="col-md-8">

            <h3 style="margin-top:0">Fork this repository <i class="mega-octicon octicon-repo-forked"></i></h3>

            <?php echo $vh->form($this->forkForm); ?>
        </div>
        <div class="col-md-2">
            <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div>
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ .'/_footer.php'; ?>