git clone <span id="gitUrl"><?php echo $this->cloneSshUrl; ?></span>
<div class="btn-group btns-clone" data-toggle="buttons">
  <label class="btn btn-default btn-xs active">
    <input type="radio" name="clonetype" id="ssh_btn" value="ssh" data-toggle="button"> SSH
  </label>
  <?php if ($this->cloneHttpUrl != null): ?>  
  <label class="btn btn-default btn-xs">
    <input type="radio" name="clonetype" id="http_btn" value="http"> HTTP<?php if (strpos($this->cloneHttpUrl, 'https', 0) !== false): ?>S<?php endif; ?> 
  </label>
  <?php endif; ?>  
  <?php if ($this->cloneHttpUrl != null && !$this->entity->isPrivate()): ?>  
  <label class="btn btn-default btn-xs">
    <input type="radio" name="clonetype" id="http_btn" value="public"> Read-Only<?php if (strpos($this->cloneHttpUrl, 'https', 0) !== false): ?>S<?php endif; ?> 
  </label>
  <?php endif; ?> 
    <input type="hidden" name="githost.ssh" id="gitSshUrl" value="<?php echo $this->cloneSshUrl; ?>">
    <?php if ($this->cloneHttpUrl != null): ?>
    <input type="hidden" name="githost.http" id="gitHttpUrl" value="<?php echo $this->cloneHttpUrl; ?>">
    <?php endif; ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
    var updateUrl = function(type) {
        if (type == "ssh") {
            $('#gitUrl').html($('#gitSshUrl').val());
        } else {
            $('#gitUrl').html($('#gitHttpUrl').val());
        }
        
        $('#gitUrl').trigger('change');
    };
    
    $('body').on('click', '.btns-clone .btn', function() {
        updateUrl($(this)[0].textContent.trim().toLowerCase());
    });
    
});
</script>