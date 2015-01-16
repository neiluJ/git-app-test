<?php $vh = $this->_helper; ?>

<div class="row">
    <div class="col col-md-5">
        <?php $err = false; $elm = $this->addTagForm->element('tagname'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
        <div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
            <?php echo $vh->formElement('tagname', $this->addTagForm); ?>
        </div>
    </div>
    <div class="col col-md-1" style="font-weight: bold; padding-top: 30px;">
        @
    </div>
    <div class="col col-md-6">
        <?php $err = false; $elm = $this->addTagForm->element('reference'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
        <div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
            <?php echo $vh->formElement('reference', $this->addTagForm); ?>
        </div>
    </div>
</div>
<?php $err = false; $elm = $this->addTagForm->element('annotation'); if ($elm->hasError()) { $err = true; $elm->hint($elm->getError()); } ?>
<div class="form-group<?php if ($err): ?> has-error<?php endif; ?>">
    <?php echo $vh->formElement('annotation', $this->addTagForm); ?>
</div>