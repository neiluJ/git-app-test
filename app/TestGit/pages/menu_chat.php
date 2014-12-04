<?php if($this->user !== null):  $vh = $this->_helper; ?>
<li<?php if($this->inChat): ?> class="active"<?php endif ?>><a href="<?php echo $vh->url('Chat', array(), true); ?>"><i class="octicon octicon-comment-discussion"></i> Chat</a></li>
<?php endif; ?>