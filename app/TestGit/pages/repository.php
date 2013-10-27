<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container"  ng-controller="RepositoryMainCtrl">
        <div class="starter-template">
            <div class="repo-title">
                <div class="collapse navbar-collapse repo-nav">
                  <ul class="nav navbar-nav navbar-left">
                      <li class="active"><a href="<?php echo $vh->url('Repository', array('name' => $this->name, 'branch' => $this->branch), true); ?>" data-placement="bottom" data-toggle="tooltip" title="Browse source"><i class="glyphicon glyphicon-list"></i></a></li>
                      <li><a href="#" data-placement="bottom" data-toggle="tooltip" title="Branches/Tags" class="txt"><i class="glyphicon glyphicon-random"></i> <span><?php echo (strlen($this->branch) == 40 ? substr($this->branch, 0, 6) : $this->branch); ?></span></a></li>
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
                <h1><a id="repoName" href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a></h1>
                <div class="clearfix"></div>
                 <p class="help-clone">git clone https://dsi-svn-prd/<?php echo $this->name; ?>.git</p>
                <p>small repository description</p>
            </div>
        </div><!-- /starter-template -->
        
          
    <input type="hidden" id="repoAction" name="repoAction" ng-bind="repoAction" value="<?php echo $this->repoAction; ?>" />  
    <input type="hidden" id="repoPath" name="repoPath" ng-bind="path" value="<?php echo $this->path; ?>" />
    <input type="hidden" id="repoBranch" name="repoBranch" ng-bind="branch" value="<?php echo $this->branch; ?>" />
    
    <div class="row">
        <div class="col-xs-5 col-sm-5 col-md-3 left-list" ng-controller="CommitsCtrl">
            <h4><a href="#" style="float:right;" title="RSS Feed"><i class="glyphicon glyphicon-signal"></i></a> Commits History</h4>
            <ul class="commits-list">
                <li class="commit-{{ commit.hash|shortHash }} ng-class: {active: currentCommit.hash == commit.hash}" ng-repeat="commit in commits | orderObjectBy:commit.ts">
                    <strong><a data-placement="top" data-toggle="tooltip" class="commit commit-{{ commit.hash|shortHash }}" ng-click="browseRevisions($event, commit);" href="./{{ repoAction }}.action?name={{ repoName }}&amp;branch={{ commit.hash }}&amp;path={{ path }}" title="{{ commit.message }}">{{ commit.hash|shortHash }}</a></strong> by <a href="#">{{ commit.author }}</a><br />
                    <span style="font-size: 12px; color: #666;">{{ commit.date }}</span>
                </li>
            </ul>
        </div>
         
        <div class="col-xs-12 col-sm-7 col-md-9" ng-controller="RepositoryDisplayCtrl">
            <h4><a href="#" style="float:right" class="btn btn-default btn-xs" ng-click="navigateToCommit($event, currentCommitHash);">View <strong>{{ currentCommitHash|shortHash }}</strong></a>Commit <a ng-bind="currentCommitHash" ng-click="navigateToCommit($event, currentCommitHash);" href="./Commit.action?name={{ repoName }}&amp;hash={{ currentCommitHash }}">{{ currentCommitHash }}</a></h4>
            <p class="commit-infos commit-txt"><a href="#" class="commit-collapse"><i class="glyphicon glyphicon-plus"></i></a>{{ currentCommitMessage }}</p>
            <hr style="margin:10px 0;" />
             <ul class="breadcrumb repo-path">
                <li ng-repeat="p in pathParts">
                    <a ng-if="!$last" ng-click="navigateToFile($event, p);" href="<?php echo $vh->url(); ?>/Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ p.realpath }}">{{ p.path }}</a>
                    <a ng-if="$last" ng-click="navigateToFile($event, p);" style="color: inherit" href="<?php echo $vh->url(); ?>/{{ repoAction }}.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ p.realpath }}">{{ p.path }}</a>
                </li>
            </ul>
            <div id="main">
                <div id="blobContents" class="main-view"></div>
                <div id="commitContents" class="main-view"></div>
                <div id="treeContents" class="main-view">
                    <table class="table table-striped">
                        <thead>
                          <tr>
                            <th style="width: 35px;">&nbsp;</th>
                            <th style="width: 200px;">File</th>
                            <th>Message</th>
                            <th style="width: 130px;">Last update</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr ng-repeat="(idx,file) in files">
                            <td ng-if="!file.special">
                                <i ng-if="file.directory" class="glyphicon glyphicon-folder-close"></i>
                                <i ng-if="!file.directory" class="glyphicon glyphicon-file"></i>
                            </td>
                            <td ng-if="file.special">
                                &nbsp;
                            </td>
                            <td ng-if="!file.special">
                                <a ng-if="file.directory" ng-click="navigateToFile($event,file);" href="./Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ path }}/{{ file.path }}">{{ file.path }}</a>
                                <a ng-if="!file.directory" ng-click="navigateToFile($event,file);" href="./Blob.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ path }}/{{ file.path }}">{{ file.path }}</a>
                            </td>
                            <td ng-if="file.special">
                                <a ng-if="file.realpath != ''" ng-click="navigateToFile($event,file);" href="./Repository.action?name={{ repoName }}&amp;branch={{ branch }}&amp;path={{ file.realpath }}">{{ file.path }}</a>
                                <a ng-if="file.realpath == ''" ng-click="navigateToFile($event,file);" href="./Repository.action?name={{ repoName }}&amp;branch={{ branch }}">{{ file.path }}</a>
                            </td>
                            <td ng-if="!file.special" class="commit-txt"><a href="./Commit.action?name={{ repoName }}&amp;hash={{ file.lastCommit.hash }}" ng-click="navigateToCommit($event, file.lastCommit);" style="color:inherit">{{ file.lastCommit.message }}</a> [<a href="#">{{ file.lastCommit.author }}</a>]</td>
                            <td ng-if="file.special">&nbsp;</td>
                            <td>{{ file.lastCommit.date }}</td>
                          </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- /row -->
    
    </div><!-- /.container -->
    
<script type="text/javascript">
$(document).ready(function() {
    $('.commit-collapse').bind('click', function(event) {
       event.preventDefault();
       $(this).parent().toggleClass('collapsed');
       if($(this).parent().hasClass('collapsed')) {
           $(this).find('i').removeClass('glyphicon-plus').addClass('glyphicon-minus');
       } else {
           $(this).find('i').removeClass('glyphicon-minus').addClass('glyphicon-plus');
       }
    });
});  
</script>
<?php include __DIR__ .'/_footer.php'; ?>