<?php $vh = $this->_helper; ?>
<?php $page_title = "Delete ". $this->entity->getFullname(); include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "none"; include __DIR__ . '/_left.php'; ?>
    <div class="col-md-8">

            <h3 style="margin-top:0">Delete this repository <i class="mega-octicon octicon-repo-delete"></i></h3>

        <div class="alert alert-warning">
            <b class="octicon octicon-alert"></b>You're about to <strong>delete this repository</strong>.
            This will remove all files, branches, tags, history <strong>permanently</strong> !
        </div>

            <form action="<?php echo $this->_helper->url('DeleteNEW', array('name' => $this->entity->getFullname())); ?>" method="post">
                <div class="form-group">
                    <input type="submit" value="Delete repository" class="btn btn-danger">
                    <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname())); ?>" class="btn btn-default">Cancel</a>
                </div>
            </form>
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