<?php if ($this->_helper->isAllowed($this->entity, 'read') || $this->_helper->isAllowed($this->entity, 'write')): ?>
<div class="btn-group btns-clone" data-toggle="buttons">
    <?php if ($this->_helper->isAllowed($this->entity, 'read') || $this->_helper->isAllowed($this->entity, 'write')): ?>
  <label class="btn btn-default btn-xs active">
    <input type="radio" name="clonetype" id="ssh_btn" value="ssh" data-toggle="button"> SSH
  </label>
  <?php if ($this->cloneHttpUrl != null): ?>  
  <label class="btn btn-default btn-xs">
    <input type="radio" name="clonetype" id="http_btn" value="http"> HTTP<?php if (strpos($this->cloneHttpUrl, 'https', 0) !== false): ?>S<?php endif; ?> 
  </label>
  <?php endif; ?> 
  <?php endif; ?>
    <?php if ($this->_helper->isAllowed($this->entity, 'read') && $this->clonePublicUrl != null): ?>
  <label class="btn btn-default btn-xs">
    <input type="radio" name="clonetype" id="public_btn" value="public"> Read-Only 
  </label>
    <?php endif; ?>
    <?php if ($this->_helper->isAllowed($this->entity, 'read') || $this->_helper->isAllowed($this->entity, 'write')): ?>
    <input type="hidden" name="githost.ssh" id="gitSshUrl" value="<?php echo $this->cloneSshUrl; ?>">
    <?php if ($this->cloneHttpUrl != null): ?>
    <input type="hidden" name="githost.http" id="gitHttpUrl" value="<?php echo $this->cloneHttpUrl; ?>">
    <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->_helper->isAllowed($this->entity, 'read') && $this->clonePublicUrl != null): ?>
    <input type="hidden" name="githost.public" id="gitPublicUrl" value="<?php echo $this->clonePublicUrl; ?>">
    <?php endif; ?>
    <a href="#" style="margin-left: 5px;" title="Copy URL to clipboard"><i class="octicon octicon-pencil"></i></a>
</div>
    <p>git clone <span id="gitUrl"><?php echo $this->cloneSshUrl; ?></span></p>
<script type="text/javascript">
$(document).ready(function() {
    var updateUrl = function(type) {
        if (type == "ssh") {
            $('#gitUrl').html($('#gitSshUrl').val());
        } else if(type == "http") {
            $('#gitUrl').html($('#gitHttpUrl').val());
        } else {
            $('#gitUrl').html($('#gitPublicUrl').val());
        }
        
        $('#gitUrl').trigger('change');
    };
    
    $('body').on('click', '.btns-clone .btn', function() {
        updateUrl($(this)[0].textContent.trim().toLowerCase());
    });
});
</script>
<?php else: ?>
you're not allowed to clone this repository
<?php endif; ?>
