<?php use TestGit\Model\User\Activity; if (!count($this->activities)): ?>
<div class="alert alert-info">No recent activity found.</div>
<?php return; endif; ?>
<ul class="activity">
<?php foreach ($this->activities as $activity): ?>
    <?php if ($activity->type == Activity::DYN_REF_CREATE): ?>
    <li class="branch">
        <span class="date"><?php echo $activity->date->format('d/m/Y H:i:s'); ?></span>
        <i class="glyphicon glyphicon-random"></i> 
        <?php if ($activity->user != null): ?><strong><a href="<?php echo $this->_helper->url('Profile', array('username' => $activity->user->getUsername())); ?>"><?php echo $activity->user->getUsername(); ?></a></strong><?php else: ?><i><?php echo $activity->username; ?></i><?php endif; ?>
        created a new <?php echo ($activity->reference->isBranch() ? "branch" : "tag"); ?> <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $activity->repository->getFullname(), 'branch' => $activity->reference->getName())); ?>"><?php echo $activity->reference->getName(); ?></a></strong> at <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $activity->repository->getFullname())); ?>"><?php echo $activity->repository->getFullname(); ?></a></strong>
    </li>
    <?php elseif ($activity->type == Activity::REPO_CREATE): ?>
    <li class="create">
        <span class="date"><?php echo $activity->date->format('d/m/Y H:i:s'); ?></span>
        <i class="glyphicon glyphicon-plus"></i> 
        <?php if ($activity->user != null): ?><strong><a href="<?php echo $this->_helper->url('Profile', array('username' => $activity->user->getUsername())); ?>"><?php echo $activity->user->getUsername(); ?></a></strong><?php else: ?><i><?php echo $activity->username; ?></i><?php endif; ?>
        created repository <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $activity->repository->getFullname())); ?>"><?php echo $activity->repository->getFullname(); ?></a></strong>
    </li>
    <?php elseif ($activity->type == Activity::REPO_FORK): ?>
    <li class="fork">
        <span class="date"><?php echo $activity->date->format('d/m/Y H:i:s'); ?></span>
        <i class="glyphicon glyphicon-retweet"></i> 
        <?php if ($activity->user != null): ?><strong><a href="<?php echo $this->_helper->url('Profile', array('username' => $activity->user->getUsername())); ?>"><?php echo $activity->user->getUsername(); ?></a></strong><?php else: ?><i><?php echo $activity->username; ?></i><?php endif; ?>
        forked repository <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $activity->obj->getTargetName())); ?>"><?php echo $activity->obj->getTargetName(); ?></a></strong> to <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $activity->repository->getFullname())); ?>"><?php echo $activity->repository->getFullname(); ?></a></strong>
    </li>
    <?php elseif ($activity->type == Activity::DYN_PUSH): ?>
    <li class="push">
        <span class="date"><?php echo $activity->date->format('d/m/Y H:i:s'); ?></span>
        <i class="glyphicon glyphicon-circle-arrow-right"></i> 
        <?php if ($activity->user != null): ?><strong><a href="<?php echo $this->_helper->url('Profile', array('username' => $activity->user->getUsername())); ?>"><?php echo $activity->user->getUsername(); ?></a></strong><?php else: ?><i><?php echo $activity->username; ?></i><?php endif; ?> pushed <strong><?php echo count($activity->commits); ?> commits</strong> at <strong><a href="<?php echo $this->_helper->url('Repository', array('name' => $activity->repository->getFullname())); ?>"><?php echo $activity->repository->getFullname(); ?></a></strong>
        <ul>
            <?php $idx = 0; foreach ($activity->commits as $commit): ?>
            <?php if ($idx >= 10): ?>
            <li class="more">plus <?php echo count($activity->commits)-10; ?> hidden commits</li>
            <?php break; ?>
            <?php else: $idx++; ?>
            <li class="commit"><a href="<?php echo $this->_helper->url('Commit', array('name' => $activity->repository->getFullname(), 'hash' => $commit->getHash())); ?>"><?php echo substr($commit->getHash(), 0, 6); ?></a>: <span class="commit-txt"><?php $msg = str_replace("\n", " ", $commit->getMessage()); echo $this->_helper->escape(substr($msg,0,60)); ?></span></li>
            <?php endif; ?>
            <?php endforeach; ?>
                <?php 
                $last = array_shift($activity->commits); 
                $first = array_pop($activity->commits); 
                
                if (count($activity->commits) > 1) {
                    $comparision = substr($first->getHash(),0, 10) . '..' . substr($last->getHash(),0,10);
                    $compareUrl = $this->_helper->url('Compare', array('name' => $activity->repository->getFullname(), 'compare' => $comparision));
                } elseif ($last != null) {
                    $comparision = false;
                    $compareUrl = $this->_helper->url('Commit', array('name' => $activity->repository->getFullname(), 'hash' => $last->getHash()));
                } else {
                    $comparision = null;
                    $compareUrl = null;
                }
                
                if (null !== $compareUrl):
                ?>
            <li>
                <a href="<?php echo $compareUrl; ?>">show <?php echo ($comparision === false ? 'commit' : 'comparision'); ?> &rarr;</a>
            </li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
<?php endforeach; ?>
</ul>