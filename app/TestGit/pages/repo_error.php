<?php $vh = $this->_helper;
        $hasEntity = ($this->entity instanceof \TestGit\Model\Git\Repository);
        $this->branch = ($hasEntity ? $this->entity->getDefault_branch() : 'master');
?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "none"; if ($hasEntity) { include __DIR__ .'/_repository_left.php'; }  ?>
        <div class="col-md-<?php echo ($hasEntity ? "10" : "12"); ?>">
            <h3 style="margin-top:0"><i class="mega-octicon octicon-alert"></i> Oops! There was an error :(</h3>
            <div class="alert alert-danger"><?php echo nl2br($vh->escape($this->errorMsg)); ?></div>
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ .'/_footer.php'; ?>