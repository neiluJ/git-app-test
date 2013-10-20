<?php
$vh = $services->get('viewHelper');
?>
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
        <a class="navbar-brand" href="<?php echo $vh->url(); ?>">
            <i id="loader" class="glyphicon glyphicon-time" title="loading..."></i> TestGit
        </a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li<?php if($active == "repositories"): ?> class="active"<?php endif ?>><a href="<?php echo $vh->url(); ?>"><i class="glyphicon glyphicon-list"></i> Repositories</a></li>
        <li<?php if($active == "users"): ?> class="active"<?php endif ?>><a href="<?php echo $vh->url('Users'); ?>"><i class="glyphicon glyphicon-user"></i> Users</a></li>
        <li<?php if($active == "admin"): ?> class="active"<?php endif ?>><a href="<?php echo $vh->url('Admin'); ?>">Admin</a></li>
      </ul>
    </div>
  </div>
</div>