<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body ng-controller="RepositoryCtrl">
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="starter-template">
            <h1><a id="repoName" href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a> @ <a id="repoBranch" ng-bind="branch" href="#"><?php echo $this->branch; ?></a></h1>
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
            
            <div class="clearfix"></div>
        </div><!-- /starter-template -->
    
        <?php if (!empty($this->path)): ?>
            <input type="hidden" id="repoPath" name="repoPath" ng-bind="path" value="<?php echo $this->path; ?>" />
        <?php endif; ?>
        
    <ul class="breadcrumb">
        <li><a href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a></li>
        <li ng-repeat="p in pathParts">
            <a ng-if="!$last" ng-click="repositoryBrowse($event);" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ p.link }}">{{ p.path }}</a>
            <a ng-if="$last" style="color: inherit" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ p.link }}">{{ p.path }}</a>
        </li>
    </ul>
        
    <table class="table table-striped">
        <thead>
          <tr>
            <th style="width: 35px;">&nbsp;</th>
            <th style="width: 280px;">File</th>
            <th>Message</th>
            <th style="width: 100px;">Last update</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="file in files">
            <td ng-if="!file.special">
                <i ng-if="file.directory" class="glyphicon glyphicon-folder-close"></i>
                <i ng-if="!file.directory" class="glyphicon glyphicon-file"></i>
            </td>
            <td ng-if="file.special">
                &nbsp;
            </td>
            <td ng-if="!file.special">
                <a ng-if="file.directory" ng-click="repositoryBrowse($event);" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ path }}/{{ file.path }}">{{ file.path }}</a>
                <a ng-if="!file.directory" href="<?php echo $vh->url(); ?>/Blob.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ path }}/{{ file.path }}">{{ file.path }}</a>
            </td>
            <td ng-if="file.special">
                <a ng-if="file.realpath != ''" ng-click="repositoryBrowse($event);" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ file.realpath }}">{{ file.path }}</a>
                <a ng-if="file.realpath == ''" ng-click="repositoryBrowse($event);" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}">{{ file.path }}</a>
            </td>
            <td ng-if="!file.special" class="commit-txt"><a href="./Commit.action?name={{ repoName }}&amp;hash={{ file.lastCommit.hash }}" style="color:inherit">{{ file.lastCommit.message }}</a> [<a href="#">{{ file.lastCommit.author }}</a>]</td>
            <td ng-if="file.special">&nbsp;</td>
            <td>{{ file.lastCommit.date }}</td>
          </tr>
        </tbody>
      </table>
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>