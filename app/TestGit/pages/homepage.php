<!DOCTYPE html>
<html lang="en" ng-app="gitApp">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Test-Git</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/site.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.js"></script>
      <script src="./js/respond.min.js"></script>
    <![endif]-->
    
    <script src="./js/angular.min.js"></script>
    <script src="./app/gitapp.js"></script>
    <script src="./app/controllers.js"></script>
  </head>

  <body ng-controller="RepositoriesCtrl">
    <?php echo $services->get('viewHelper')->embed('Menu', array('active' => 'repositories')); ?>

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
            <td><a href="#">{{ repo.name }}</a></td>
            <td>{{ repo.lastCommit.date }}</td>
            <td>
                [<a href="#">{{ repo.lastCommit.author }}</a>] {{ repo.lastCommit.message }} (<a href="#">{{ repo.lastCommit.hash|shortHash }}</a>)
            </td>
            <td>{{ repo.size }}</td>
          </tr>
        </tbody>
      </table>
        
    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
  </body>
</html>