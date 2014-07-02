<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "none"; include __DIR__ .'/_repository_left.php'; ?>
    <div class="col-md-8">

            <h3 style="margin-top:0">Delete this repository <i class="mega-octicon octicon-repo-delete"></i></h3>

        <div class="alert alert-warning">
            <b class="octicon octicon-alert"></b>You're about to <strong>delete this repository</strong>.
            This will remove all files, branches, tags, history <strong>permanently</strong> !
        </div>

            <form action="<?php echo $this->_helper->url('Delete', array('name' => $this->entity->getFullname())); ?>" method="post">
                <div class="form-group">
                    <input type="submit" value="Delete repository" class="btn btn-danger">
                    <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname())); ?>" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
        <div class="col-md-2">
            <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div>
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ .'/_footer.php'; ?>