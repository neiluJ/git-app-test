<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body ng-controller="RepositoryCtrl">
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="starter-template">
            <h1><a id="repoName" href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a> @ <a id="repoBranch" ng-bind="branch" href="#">master</a></h1>
            <p>Last Updated on <a href="#"><?php echo $this->repository->getHeadCommit()->getAuthorDate()->format('d/m/y H:i:s'); ?></a> by <strong><?php echo $this->repository->getHeadCommit()->getAuthorName(); ?></strong></p>
        </div>
        
    <ul class="breadcrumb">
        <li><a href="<?php echo $vh->url('Repository', array('name' => $this->name), true); ?>"><?php echo $this->name; ?></a></li>
        <li></li>
    </ul>
        
    <table class="table table-striped">
        <thead>
          <tr>
            <th style="width: 35px;">&nbsp;</th>
            <th style="width: 350px;">File</th>
            <th>Message</th>
            <th style="width: 100px;">Last update</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="file in files">
            <td ng-if="file.directory"><i class="glyphicon glyphicon-folder-close"></i></td>
            <td ng-if="!file.directory"><i class="glyphicon glyphicon-file"></i></td>
            <td><a href="<?php echo $vh->url(); ?>/Tree.action?path={{ file.path }}">{{ file.path }}</a></td>
            <td>{{ file.lastCommit.message }}</td>
            <td>{{ file.lastCommit.date }}</td>
          </tr>
        </tbody>
      </table>
        
        <h2>Commits History</h2>
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>