<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="starter-template">
            <div class="repo-title">
                <div class="collapse navbar-collapse repo-nav">
                  <ul class="nav navbar-nav navbar-left">
                      <li><a href="<?php echo $vh->url('Repository', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Browse source"><i class="glyphicon glyphicon-list"></i></a></li>
                      <li class="active"><a href="<?php echo $vh->url('Branches', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Branches/Tags" class="txt"><i class="glyphicon glyphicon-random"></i> <span><?php echo (strlen($this->branch) == 40 ? substr($this->branch, 0, 6) : $this->branch); ?></span></a></li>
                      <li><a href="<?php echo $vh->url('Accesses', array('name' => $this->name), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Access Rights"><i class="glyphicon glyphicon-user"></i></a></li>
                  </ul>

                  <ul class="nav navbar-nav navbar-right">
                      <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i></a>
                          <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li><a href="#">Separated link</a></li>
                            <li><a href="#">One more separated link</a></li>
                          </ul>
                      </li>
                  </ul>
              </div>
                <h1><a id="repoName" href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a></h1>
                <div class="clearfix"></div>
                 <p class="help-clone">git clone https://dsi-svn-prd/<?php echo $this->name; ?>.git</p>
                <p>small repository description</p>
            </div>
        </div><!-- /starter-template -->
        
          
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