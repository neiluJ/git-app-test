<?php $vh = $this->_helper; ?>
<?php $page_title = $this->entity->getFullname() ." - Branches & Tags"; include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="row" style="margin-top:40px;">
            <?php $repoMenuActive = "branches"; include __DIR__ . '/_left.php'; ?>
            <div class="col-md-7">
                <h3 style="margin-top:0;">Branches <i class="mega-octicon octicon-git-branch"></i></h3>
                <?php if (count($this->branches)): ?>
                <ul class="tags-list">
                <?php foreach($this->branches as $tag): ?>
                <li>
                    <div class="btn-group btn-group-xs" style="float:right">
                        <?php if($this->branch != $tag->getName()): ?>
                            <a class="btn btn-success" href="<?php echo $this->_helper->url('CompareNEW', array('name' => $this->name, 'compare' => $this->branch . '..' . $tag->getName()), true); ?>"><i class="octicon octicon-git-compare"></i> Compare</a>
                        <?php endif; ?>
                        <?php if($this->entity->getDefault_branch() != $tag->getName() && $this->_helper->isAllowed($this->entity, 'special')): ?>
                            <a class="btn btn-danger" href="<?php echo $this->_helper->url('DeleteRef', array('name' => $this->name, 'branch' => $tag->getName()), true); ?>" title="delete branch"><i class="octicon octicon-git-branch-delete"></i></a>
                        <?php endif; ?>
                    </div>
                    <strong><a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->name, 'branch' => $tag->getName()), true); ?>"><?php echo $tag->getName(); ?></a></strong> <small>@ <a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->name, 'hash' => $tag->getCommit()->getHash()), true); ?>"><?php echo substr($tag->getCommit()->getHash(),0,6); ?></a></small> <?php if($this->entity->getDefault_branch() == $tag->getName()): ?><span class="label label-default">Default</span><?php endif; ?>
                    <p>Last updated by <?php echo $tag->getCommit()->getCommitterName(); ?> on <?php echo $tag->getCommit()->getCommitterDate()->format('d/m/Y H:i:s'); ?></p>
                    <small class="commit-txt"><?php echo htmlentities($tag->getCommit()->getShortMessage(), ENT_QUOTES, 'utf-8'); ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p>No branches found</p>
            <?php endif; ?>
        </div>
         
        <div class="col-md-3">
            <h3 style="margin-top:0">Tags <i class="mega-octicon octicon-tag"></i></h3>
            <?php if (count($this->tags)): ?>
            <ul class="tags-list">
                <?php foreach($this->tags as $tag): ?>
                <li>
                    <?php if($this->_helper->isAllowed($this->entity, 'special')): ?>
                        <a class="btn btn-danger btn-xs" style="float:right;" href="<?php echo $this->_helper->url('DeleteRef', array('name' => $this->name, 'branch' => $tag->getName()), true); ?>" title="delete tag"><i class="octicon octicon-tag-remove"></i></a>
                    <?php endif; ?>
                    <strong><a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->name, 'branch' => $tag->getName()), true); ?>"><?php echo $tag->getName(); ?></a></strong> <small>@ <a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->name, 'hash' => $tag->getCommit()->getHash()), true); ?>"><?php echo substr($tag->getCommit()->getHash(),0,6); ?></a></small>
                    <p>Created by <?php echo $tag->getCommit()->getCommitterName(); ?> on <?php echo $tag->getCommit()->getAuthorDate()->format('d/m/Y H:i:s'); ?></p>

                    <div class="btn-group btn-group-xs">
                        <a href="<?php echo $vh->url('Archive', array('name' => $this->entity->getFullname(), 'branch' => $tag->getName(), 'format' => 'zip'), true); ?>" class="btn btn-default"><i class="glyphicon glyphicon-download"></i> Download <strong>.zip</strong></a>
                        <a href="<?php echo $vh->url('Archive', array('name' => $this->entity->getFullname(), 'branch' => $tag->getName(), 'format' => 'tar.gz'), true); ?>" class="btn btn-default"><i class="glyphicon glyphicon-download"></i> Download <strong>.tar.gz</strong></a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p>No tags found</p>
            <?php endif; ?>
        </div>
    </div><!-- /row -->
    
</div><!-- /.container -->
    
<?php include __DIR__ . '/../_footer.php'; ?>