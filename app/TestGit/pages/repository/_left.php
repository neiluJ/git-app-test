<div class="col-md-2 avatar">
        <i class="octicon <?php if($this->entity->hasParent()): ?>octicon-repo-forked<?php else: ?>octicon-repo<?php endif; ?>"></i>
    <?php if(!$this->entity->isPrivate()): ?>
        <span class="label label-success">public</span>
    <?php else: ?>
        <span class="label label-danger">private</span>
    <?php endif; ?>

    <h1 class="profile"><a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname())); ?>"><?php echo $vh->escape($this->entity->getName()); ?></a></h1>

    <?php if($this->entity->getOwner_id() != null): ?>
    <p class="profile-info">@<a href="<?php echo $this->_helper->url('Profile', array('username' => $this->entity->getOwner()->getUsername())); ?>"><?php echo $vh->escape($this->entity->getOwner()->getUsername()); ?></a></p>
    <?php else: ?>
    <br />
    <?php endif; ?>


    <?php $this->entity->hasParent(); if ($this->entity->hasParent()): ?>
        <p class="profile-info"><b class="octicon octicon-repo-forked"></b> <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getParent()->getFullname())); ?>"><?php echo $vh->escape($this->entity->getParent()->getFullname()); ?></a></p>
    <?php endif; ?>

    <?php if ($this->entity->getDescription() != null): ?>
    <p class="profile-info">
        <?php echo $vh->escape($this->entity->getDescription()); ?>
        <?php $ws = $this->entity->getWebsite(); if(!empty($ws)): ?><br /><a href="<?php echo $this->_helper->escape($ws); ?>"><?php echo $this->_helper->escape($ws); ?></a><?php endif; ?>
    </p>
    <?php endif; ?>

    <?php if($this->_helper->isAllowed($this->entity, 'read')): ?>
    <hr />
    <?php if(!$this->emptyRepo): ?>
    <!-- Split button -->
    <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><b class="octicon octicon-git-branch"></b> <?php echo $this->_helper->escape((strlen($this->branch) > 12 ? substr($this->branch, 0, 9) .'...' : $this->branch)); ?> <b class="caret"></b></button>
        <a href="<?php echo $this->_helper->url('CompareNEW', array('name' => $this->entity->getFullname(), 'compare' => $this->entity->getDefault_branch() .'..'. $this->branch)); ?>" class="btn btn-success"><b class="octicon octicon-git-compare"></b></a>
        <ul class="dropdown-menu" role="menu" style="text-align: left;">
            <li role="presentation" class="dropdown-header" style="border-bottom: solid 1px #eee;"><u class="octicon octicon-git-branch pull-right"></u> Branches</li>

            <?php $tags = 0; foreach ($this->entity->getReferences() as $ref): if (!$ref->isBranch()) { $tags++; continue; } ?>
            <li><a href="<?php echo $this->_helper->url($this->repoAction, array('name' => $this->entity->getFullname(), 'branch' => $ref->getName())); ?>"><?php if($ref->getName() == $this->branch): ?><u class="octicon octicon-check"></u> <?php endif; ?><?php echo $this->_helper->escape($ref->getName()); ?></a></li>
            <?php endforeach; ?>
            <?php if($tags > 0): ?>
            <li role="presentation" class="dropdown-header" style="border-bottom: solid 1px #eee;"><u class="octicon octicon-tag pull-right"></u> Tags</li>
            <?php foreach ($this->entity->getReferences() as $ref): if ($ref->isBranch()) continue; ?>
                <li><a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $ref->getName())); ?>"><?php echo $this->_helper->escape($ref->getName()); ?></a></li>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($this->_helper->isAllowed($this->entity, 'special')): ?>
            <li class="divider"></li>
            <li><a href="#" data-toggle="modal" data-target="#addBranchModal"><u class="octicon octicon-git-branch-create"></u> Create Branch</a></li>
            <li><a href="#"><u class="octicon octicon-tag-add"></u> Create Tag</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <hr />
    <?php endif; ?>
    <ul class="nav nav-pills nav-stacked" style="margin-top: 20px; text-align: left;">
        <li<?php if ($repoMenuActive == "code"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><b class="octicon octicon-code"></b> Browse</a></li>
        <?php if(!$this->emptyRepo): ?>
        <li<?php if ($repoMenuActive == "commits"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('CommitsNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><b class="octicon octicon-git-commit"></b> Commits</a></li>
        <?php endif; ?>
        <li<?php if ($repoMenuActive == "activity"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('ActivityNEW', array('name' => $this->entity->getFullname())); ?>"><b class="octicon octicon-history"></b> Activity</a></li>
        <?php if(!$this->emptyRepo): ?>
        <li<?php if ($repoMenuActive == "branches"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('BranchesNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><b class="octicon octicon-git-branch"></b> Branches &amp; Tags</a></li>
        <?php endif; ?>
        <?php if ($this->_helper->isAllowed($this->entity, 'admin')): ?>
        <li<?php if ($repoMenuActive == "accesses"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('AccessesNEW', array('name' => $this->entity->getFullname())); ?>"><b class="octicon octicon-organization"></b> Access Rights</a></li>
        <li class="divider"></li>
        <li<?php if ($repoMenuActive == "settings"): ?> class="active"<?php endif; ?>><a  style="padding: 5px 15px;" href="<?php echo $this->_helper->url('SettingsNEW', array('name' => $this->entity->getFullname())); ?>"><b class="octicon octicon-tools"></b> Settings</a></li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>

    <?php if ($this->_helper->isAllowed($this->entity, 'special')): ?>
        <div class="modal fade" id="addBranchModal" style="text-align: left">
            <div class="modal-dialog">
                <form role="form" id="addBranch" method="post" action="<?php echo $this->_helper->url('AddBranch', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Create Branch</h4>
                        </div>
                        <div class="modal-body" id="addBranchContents">
                            <?php echo $this->_helper->embed('AddBranch', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Branch</button>
                        </div>
                    </div><!-- /.modal-content -->
                </form>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <script type="text/javascript">
            $(function() {
                $('#addBranch').on('submit', function(e) {
                    e.preventDefault();
                    var data = $(this).serializeArray(), url = $(this).attr('action');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function(data) {
                            $('#addBranchContents').html(data);
                        }
                    });
                });
            });
        </script>
    <?php endif; ?>
</div>

