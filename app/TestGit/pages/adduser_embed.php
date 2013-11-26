<?php $vh = $this->_helper; ?>
<?php $err = false; $elm = $this->addUserForm->element('username'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
<div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
     <?php echo $vh->formElement('username', $this->addUserForm); ?>
</div>
<?php $err = false; $elm = $this->addUserForm->element('email'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
<div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
<?php echo $vh->formElement('email', $this->addUserForm); ?>
</div>
<?php $err = false; $elm = $this->addUserForm->element('password'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); }  $elm = $this->addUserForm->element('confirm'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
<div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
<label for="password">Password &amp; Confirmation</label>
<?php echo $vh->formElement('password', $this->addUserForm); ?>
<?php echo $vh->formElement('confirm', $this->addUserForm); ?>
</div>
<label for="">Roles &amp; Permissions</label>
<?php if ($this->addUserForm->has('role_repos')): ?>
<div class="checkbox">
    <?php echo $vh->formElement('role_repos', $this->addUserForm); ?>
</div>
<?php endif; ?>
<?php if ($this->addUserForm->has('role_staff')): ?>
<div class="checkbox">
    <?php echo $vh->formElement('role_staff', $this->addUserForm); ?>
</div>
<?php endif; ?>
<?php if ($this->addUserForm->has('role_admin')): ?>
<div class="checkbox">
    <?php echo $vh->formElement('role_admin', $this->addUserForm); ?>
</div>
<?php endif; ?>
