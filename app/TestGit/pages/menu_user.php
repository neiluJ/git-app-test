<?php if (null !== $this->user): ?>
<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="#">Action</a></li>
        <li><a href="#">Another action</a></li>
        <li><a href="#">Something else here</a></li>
        <li class="divider"></li>
        <li><a href="#">Separated link</a></li>
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
