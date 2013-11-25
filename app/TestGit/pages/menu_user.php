<?php if (null !== $this->user): ?>
<ul class="nav navbar-nav navbar-right">
    <?php if ($this->_helper->isAllowed('repository', 'create')): ?>
    <li<?php if($this->active == 'create'): ?> class="active"<?php endif; ?>><a href="<?php echo $this->_helper->url('Create'); ?>"><i class="glyphicon glyphicon-plus"></i></a></li>
    <?php endif; ?>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php $fn = $this->user->getFullname(); echo $this->_helper->escape(empty($fn) ? $this->user->getUsername() : $fn); ?> <b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo $this->_helper->url('Profile', array('username' => $this->user->getUsername())); ?>">Profile</a></li>
        <li><a href="<?php echo $this->_helper->url('UserSettings', array('username' => $this->user->getUsername())); ?>">Settings</a></li>
        <li class="divider"></li>
        <li><a href="<?php echo $this->_helper->url('Logout'); ?>">Logout</a></li>
      </ul>
    </li>
</ul>
<?php else: ?>
<form class="navbar-form navbar-right" style="padding-right:0;" method="post" action="<?php echo $this->_helper->url('Login',  array('back' => $_SERVER['REQUEST_URI']), true); ?>">
<div class="form-group">
  <input type="text" name="username" style="width: 100px;" placeholder="Username" class="form-control">
</div>
<div class="form-group">
  <input type="password" name="password" style="width: 100px;" placeholder="Password" class="form-control">
</div>
<button type="submit" class="btn btn-success">Sign in</button>
</form>
<?php endif; ?>
