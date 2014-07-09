<?php $vh = $this->_helper; ?>
<?php $page_title = $this->profile->getUsername() . " Activity"; include __DIR__ .'/_header.php'; ?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container">
          <div class="row" style="margin-top:40px;">
          <?php $userMenuActive = "activity"; include __DIR__ .'/_user_left.php'; ?>
            <div class="col-md-8">
                <?php if ($this->profile->isUser()): ?>
                <?php echo $this->_helper->embed('Activity', array('user' => $this->profile, 'repositories' => $this->activityRepositories)); ?>
                <?php else: ?>
                <div class="alert alert-info">No activity found.</div>
                <?php endif; ?>
            </div>
              <div class="col-md-2">
                  <?php if ($this->profile->isUser()): ?>
                  <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
                  <p class="user-stat"><span class="big-counter"><?php echo $this->totalCommits; ?></span> commits</p>
                    <?php else: ?>
                      <p class="user-stat"><span class="big-counter"><?php echo count($this->profile->getMembers()); ?></span> members</p>
                      <p class="user-stat"><span class="big-counter"><?php echo count($this->repositories); ?></span> repositories</p>
                  <?php endif; ?>
              </div>
          </div>
      </div>
      
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
