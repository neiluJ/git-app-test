<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body ng-controller="RepositoriesCtrl">
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">

      <div class="starter-template" style="text-align:center">
          <h1>Repositories</h1>
      </div>

        <div class="">
<?php if ($this->_helper->isAllowed('repository', 'create')): ?>
            <a href="<?php echo $vh->url('Create'); ?>" class="btn btn-primary pull-right">Create Repository</a>
<?php endif; ?>
          <form role="form" class="form-inline filter">
            <div class="form-group">
              <input type="search" tabindex="1" ng-model="query" class="form-control" id="searchRepos" placeholder="Filter repositories">
            </div>
          </form>
        </div>

    <table class="table table-striped" style="margin-top:20px;">
        <thead>
          <tr>
            <th style="width: 48px;">&nbsp;</th>
            <th style="width: 280px;">Repository</th>
            <th style="width: 120px;">Owner</th>
            <th style="width: 130px;">Last Update</th>
            <th>Message</th>
            <th style="width: 50px;">Size</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="repo in repositories | filter:query">
            <td>
                <i ng-if="!repo.fork" class="glyphicon glyphicon-list"></i>
                <i ng-if="repo.fork" class="glyphicon glyphicon-retweet"></i>
                 <i ng-if="repo.private" class="glyphicon glyphicon-lock"></i>
            </td>
            <td><a href="<?php echo rtrim($vh->url(), '/'); ?>/Repository.action?name={{ repo.fullname }}">{{ repo.name }}</a></td>
            <td><a href="<?php echo rtrim($vh->url(), '/'); ?>/Profile.action?username={{ repo.ownerName }}">{{ repo.ownerName }}</a></td>
            <td>{{ repo.lastCommit.date }}</td>
            <td class="commit-txt">[<a href="#">{{ repo.lastCommit.author }}</a>] {{ repo.lastCommit.message }} (<a href="#">{{ repo.lastCommit.hash|shortHash }}</a>)</td>
            <td>{{ repo.size }}</td>
          </tr>
        </tbody>
      </table>

       <h3>Comments (<?php echo $this->_helper->embed('CommentsCount', array('id' => 'homepage')); ?>)</h3>
        <?php echo $this->_helper->embed('CommentsThread', array('id' => 'homepage', 'type' => 'threaded')); ?>
    </div><!-- /.container -->
    
<?php include __DIR__ .'/_footer.php'; ?>