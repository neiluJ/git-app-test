<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
  <body ng-controller="UsersCtrl">
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

    <div class="container">

      <div class="starter-template" style="text-align:center">
          <h1>Users</h1>
      </div>

        <div class="">
            <button type="button" data-toggle="modal" data-target="#addModal" class="btn btn-primary pull-right">Add User</button>
            <form role="form" class="form-inline filter">
            <div class="form-group">
              <input type="search" tabindex="1" ng-model="query" class="form-control" id="searchRepos" placeholder="Filter users">
            </div>
          </form>
            
        </div>

    <table class="table table-striped" style="margin-top:20px;">
        <thead>
          <tr>
            <th style="width: 35px;">&nbsp;</th>
            <th style="width: 150px;">Username</th>
            <th style="width: 280px;">Emails</th>
            <th>Fullname</th>
            <th style="width: 180px;">Added on</th>
            <th style="width: 50px;">Active</th>
            <th style="width: 50px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="user in users | filter:query">
            <td><i class="glyphicon glyphicon-user"></i></td>
            <td><a href="<?php echo rtrim($vh->url(), '/'); ?>/Profile.action?username={{ user.username }}">{{ user.username }}</a></td>
            <td><span ng-repeat="email in user.emails">{{ email }}</span></td>
            <td>{{ user.fullname }}</td>
            <td>{{ user.added_date }}</td>
            <td>{{ user.active }}</td>
            <td><a href="#">suspend</a> - <a href="#">delete</a></td>
          </tr>
        </tbody>
      </table>
        
    </div><!-- /.container -->
    
    

<div class="modal fade" id="addModal">
  <div class="modal-dialog">
      <form role="form" id="addUser" method="post" action="<?php echo $this->_helper->url('AddUser'); ?>">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Add user</h4>
      </div>
      <div class="modal-body" id="addUserContents">
<?php echo $this->_helper->embed('AddUser'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Add User</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->
<script type="text/javascript">
    $(function() {
       $('#addUser').on('submit', function(e) {
          e.preventDefault();
          var data = $(this).serializeArray(), url = $(this).attr('action');
          $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(data) {
                $('#addUserContents').html(data);
            }
         });
       });
    });
</script>
<?php include __DIR__ .'/_footer.php'; ?>