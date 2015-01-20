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
        <div class="col-md-2">
            <?php if($this->_helper->isAllowed($this->entity, 'read')): ?>
                <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div>

                <div class="btn-group btn-group-sm" style="margin-top:10px;">
                    <a href="<?php echo $vh->url('Archive', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'format' => 'zip'), true); ?>" class="btn btn-default"><b class="octicon octicon-cloud-download"></b> Download <strong>ZIP</strong></a>
                    <a href="<?php echo $vh->url('Archive', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'format' => 'tar.gz'), true); ?>" class="btn btn-default"><strong>TAR</strong></a>
                </div>
                <small style="display: block; text-align: center; color: #ccc; margin-top: 5px;">Download the contents of <strong><?php echo $vh->escape($this->entity->getFullname()); ?></strong> at <strong><?php echo $vh->escape($this->branch); ?></strong></small>
            <?php endif; ?>
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ . '/../_footer.php'; ?>