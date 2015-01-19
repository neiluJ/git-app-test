<?php $vh = $this->_helper; ?>
<?php $page_title = $this->entity->getFullname() ." - Delete Branch"; include __DIR__ . '/../../_header.php'; ?>
<body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="row" style="margin-top:40px;">
            <?php $repoMenuActive = "branches"; include __DIR__ . '/../_left.php'; ?>
            <div class="col-md-8">

                <h3 style="margin-top:0">Delete <?php echo ($this->refType == \TestGit\Model\Git\Reference::TYPE_BRANCH ? 'Branch' : 'Tag'); ?></h3>

<?php if($this->canDeleteBranch === true): ?>
                <div class="alert alert-warning">
                    <b class="octicon octicon-alert"></b>You're about to <strong>delete</strong> the <strong><?php echo $vh->escape($this->branch); ?></strong> <?php echo ($this->refType == \TestGit\Model\Git\Reference::TYPE_BRANCH ? 'branch' : 'tag'); ?> from
                    this repository. Are you sure ?
                </div>

                <form action="<?php echo $this->_helper->url('DeleteRef', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>" method="post">
                    <div class="form-group">
                        <input type="submit" value="Delete <?php echo ($this->refType == \TestGit\Model\Git\Reference::TYPE_BRANCH ? 'Branch' : 'Tag'); ?>" class="btn btn-danger">
                        <a href="<?php echo $this->_helper->url('BranchesNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>" class="btn btn-default">Cancel</a>
                    </div>
                </form>
<?php elseif (!$this->canDeleteBranch): ?>
                <div class="alert alert-danger">
                    <b class="octicon octicon-alert"></b>You can't <strong>delete</strong> the <strong><?php echo $vh->escape($this->branch); ?></strong> <?php echo ($this->refType == \TestGit\Model\Git\Reference::TYPE_BRANCH ? 'branch' : 'tag'); ?> from
                    this repository: <?php echo $vh->escape($this->errorMsg); ?>
                </div>
<?php endif; ?>
            </div>
        </div><!-- /row -->
    </div><!-- /.container -->
</body>
<?php include __DIR__ .'/../../_footer.php'; ?>
