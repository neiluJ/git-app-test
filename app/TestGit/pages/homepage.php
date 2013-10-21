<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body ng-controller="RepositoriesCtrl">
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">

      <div class="starter-template">
          <h1>Repositories</h1>
      </div>

        <div class="">
            <button type="button" class="btn btn-primary pull-right">Create Repository</button>
        <form role="form" class="form-inline filter">
            <div class="form-group">
              <input type="search" tabindex="1" ng-model="query" class="form-control" id="searchRepos" placeholder="Filter repositories">
            </div>
          </form>
            
        </div>

    <table class="table table-striped" style="margin-top:20px;">
        <thead>
          <tr>
            <th style="width: 35px;">&nbsp;</th>
            <th style="width: 350px;">Repository</th>
            <th style="width: 150px;">Last Update</th>
            <th>Message</th>
            <th style="width: 50px;">Size</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="repo in repositories | filter:query">
            <td><i class="glyphicon glyphicon-list"></i></td>
            <td><a href="<?php echo $vh->url(); ?>/Repository.action?name={{ repo.name }}">{{ repo.name }}</a></td>
            <td>{{ repo.lastCommit.date }}</td>
            <td>
                [<a href="#">{{ repo.lastCommit.author }}</a>] {{ repo.lastCommit.message }} (<a href="#">{{ repo.lastCommit.hash|shortHash }}</a>)
            </td>
            <td>{{ repo.size }}</td>
          </tr>
        </tbody>
      </table>
        
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>