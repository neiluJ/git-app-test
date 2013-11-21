<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
<?php $repoMenuActive = 'branches'; include __DIR__ .'/_repository_header.php'; ?>
        
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <h3>Branches <i class="glyphicon glyphicon-random"></i></h3>
                <?php if (count($this->branches)): ?>
                <ul class="tags-list">
                <?php foreach($this->branches as $tag): ?>
                <li>
                    <a style="float:right" class="btn btn-xs btn-default" href="<?php echo $this->_helper->url('Compare', array('name' => $this->name, 'compare' => $tag->getName() . '..' . $this->branch), true); ?>">Compare</a>
                    <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $this->name, 'branch' => $tag->getName()), true); ?>"><?php echo $tag->getName(); ?></a></strong> <small>@ <a href="<?php echo $this->_helper->url('Commit', array('name' => $this->name, 'hash' => $tag->getCommit()->getHash()), true); ?>"><?php echo substr($tag->getCommit()->getHash(),0,6); ?></a></small>
                    <p>Last updated by <?php echo $tag->getCommit()->getCommitterName(); ?> on <?php echo $tag->getCommit()->getCommitterDate()->format('d/m/Y H:i:s'); ?></p>
                    <small class="commit-txt"><?php echo htmlentities($tag->getCommit()->getShortMessage(), ENT_QUOTES, 'utf-8'); ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p>No branches found</p>
            <?php endif; ?>
        </div>
         
        <div class="col-xs-6 col-sm-6 col-md-6">
            <h3>Tags <i class="glyphicon glyphicon-tags"></i></h3>
            <?php if (count($this->tags)): ?>
            <ul class="tags-list">
                <?php foreach($this->tags as $tag): ?>
                <li>
                    <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $this->name, 'branch' => $tag->getName()), true); ?>"><?php echo $tag->getName(); ?></a></strong> <small>@ <a href="<?php echo $this->_helper->url('Commit', array('name' => $this->name, 'hash' => $tag->getCommit()->getHash()), true); ?>"><?php echo substr($tag->getCommit()->getHash(),0,6); ?></a></small>
                    <p>Created by <?php echo $tag->getCommit()->getCommitterName(); ?> on <?php echo $tag->getCommit()->getCommitterDate()->format('d/m/Y H:i:s'); ?></p>
                    <a href="#" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-download"></i> Download <strong>.zip</strong></a> <a href="#" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-download"></i> Download <strong>.tar.gz</strong></a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <p>No tags found</p>
            <?php endif; ?>
        </div>
    </div><!-- /row -->
    
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>