<?php if (null !== $this->user): ?>
<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php $fn = $this->user->getFullname(); echo $this->_helper->escape(empty($fn) ? $this->user->getUsername() : $fn); ?> <b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="#">Profile</a></li>
        <li class="divider"></li>
        <li><a href="<?php echo $this->_helper->url('Logout'); ?>">Logout</a></li>
      </ul>
    </li>
</ul>
<?php else: ?>
<ul class="nav navbar-nav navbar-right">
    <li class="">
      <a href="<?php echo $this->_helper->url('Login'); ?>">Login</a>
    </li>
</ul>
<?php endif; ?>
