<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body ng-controller="RepositoryBlob">
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="starter-template">
            <h1><a id="repoName" href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a> @ <a id="repoBranch" ng-bind="branch" href="#"><?php echo $this->branch ?></a></h1>
            <p>This is a smally-tiny-shiny nice repository description</p>
            <div class="collapse navbar-collapse repo-nav">
                <ul class="nav navbar-nav navbar-left">
                    <li class="active"><a href="<?php echo $vh->url('Repository', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Browse source"><i class="glyphicon glyphicon-list"></i></a></li>
                    <li><a href="<?php echo $vh->url('Commits', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Commits History"><i class="glyphicon glyphicon-time"></i></a></li>
                    <li><a href="#" data-placement="bottom" data-toggle="tooltip" title="Branches/Tags"><i class="glyphicon glyphicon-random"></i></a></li>
                    <li><a href="#" data-placement="bottom" data-toggle="tooltip" title="Access Rights"><i class="glyphicon glyphicon-user"></i></a></li>
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
        </div>
    
        <?php if (!empty($this->path)): ?>
            <input type="hidden" id="repoPath" name="repoPath" ng-bind="path" value="<?php echo $this->path; ?>" />
        <?php endif; ?>
        
    <ul class="breadcrumb">
        <li><a href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a></li>
        <li ng-repeat="p in pathParts">
            <a ng-if="!$last" ng-click="repositoryBrowse($event);" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ p.link }}">{{ p.path }}</a>
            <a ng-if="$last" style="color: inherit" href="<?php echo $vh->url(); ?>/Blob.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ p.link }}">{{ p.path }}</a>
        </li>
    </ul>
        
    <div class="row">
        <div class="col-xs-5 col-sm-5 col-md-3 left-list">
            <h4><a href="#" style="float:right;" title="RSS Feed"><i class="glyphicon glyphicon-signal"></i></a> File History</h4>
            <ul class="commits-list">
                <li ng-class="{active: currentCommit.hash == commit.hash}" ng-repeat="commit in commits">
                    <strong><a ng-click="blobBrowseRevisions($event);" href="./Blob.action?name={{ repoName }}&branch={{ commit.hash }}&path={{ path }}">{{ commit.hash|shortHash }}</a></strong> by <a href="#">{{ commit.author }}</a><br />
                    <span style="font-size: 12px; color: #666;">{{ commit.date }}</span>
                </li>
            </ul>
        </div>
        <div class="col-xs-12 col-sm-7 col-md-9">
            <h4><a href="#" style="float:right" class="btn btn-default btn-xs">View <strong>{{ currentCommit.hash|shortHash }}</strong></a>Commit <a href="#">{{ currentCommit.hash }}</a></h4>
            <p class="commit-infos">{{ currentCommit.message }}</p>
            <hr style="margin:10px 0;" />
            <div id="blobContents"></div>
</div>
        
    </div><!-- /row -->
            
    
    
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>