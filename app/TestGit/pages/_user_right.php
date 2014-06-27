<div class="col-md-2">
    <?php if ($this->profile->isUser()): ?>
        <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
        <p class="user-stat"><span class="big-counter"><?php echo $this->totalCommits; ?></span> commits</p>
    <?php else: ?>
        <p class="user-stat"><span class="big-counter"><?php echo count($this->profile->getMembers()); ?></span> members</p>
        <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
    <?php endif; ?>
</div>