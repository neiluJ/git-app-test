<?php $vh = $this->_helper; ?>
<?php $err = false; $elm = $this->addOrganizationForm->element('username'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
<div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
     <?php echo $vh->formElement('username', $this->addOrganizationForm); ?>
</div>

