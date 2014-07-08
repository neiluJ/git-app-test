<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "code"; include __DIR__ .'/_repository_left.php'; ?>
        <div class="col-md-8">
            <div id="repo-commit">
                <h5 style="margin-top:0;">
                    <?php $thId = 'commit-'. $this->entity->getId() .'-'. $this->commit->getHash(); $comments = $this->_helper->embed('CommentsCount', array('id' => $thId)); if ($comments > 0): ?>
                        <span class="pull-right"><?php echo $comments; ?> <b class="octicon octicon-comment"></b></span>
                    <?php endif; ?>
                    <i class="octicon octicon-git-commit"></i> Commit <a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->entity->getFullname(), 'hash' => $this->commit->getHash())); ?>"><?php echo $this->commit->getHash(); ?></a>
                </h5>
                <p class="commit-infos commit-txt"><a href="#" class="commit-collapse"><i class="glyphicon glyphicon-plus"></i></a><span><?php echo $this->_helper->escape($this->commit->getMessage()); ?></span></p>
                <hr style="margin:10px 0;" />
            </div>
            <ul class="breadcrumb repo-path">
                <li><a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><?php echo $vh->escape($this->entity->getName()); ?></a></li>
                <?php
                    $path = $this->path;
                    if (empty($path)) {
                        $path = array();
                    } else {
                        $path = explode('/', $this->path);
                    }

                    $toPath = "";
                    foreach ($path as $idx => $p):
                ?>
                <li>
                    <?php $toPath .= $p . DIRECTORY_SEPARATOR; if ($idx+1 < count($path)): ?>
                    <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => rtrim($toPath, DIRECTORY_SEPARATOR))); ?>"><?php echo $this->_helper->escape($p); ?></a>
                    <?php else: ?>
                    <a style="color: inherit" href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => rtrim($toPath, DIRECTORY_SEPARATOR))); ?>"><?php echo $this->_helper->escape($p); ?></a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <div id="main">
                <div id="tc" class="mainv">
                    <table class="table table-striped">
                        <tbody>
                        <?php foreach ($this->files as $file): ?>
                        <tr>
                            <?php if (!$file['special']): ?>
                            <td>
                                <?php if($file['directory']): ?>
                                <i class="octicon octicon-file-directory"></i>
                                <?php else: ?>
                                <i class="octicon octicon-file-code"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($file['directory']): ?>
                                <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => $file['realpath'])); ?>"><?php echo $this->_helper->escape($file['path']); ?></a>
                                <?php else: ?>
                                <a href="<?php echo $this->_helper->url('BlobNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => $file['realpath'])); ?>"><?php echo $this->_helper->escape($file['path']); ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="commit-txt"><a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->entity->getFullname(), 'hash' => $file['lastCommit']['hash'])); ?>" style="color:inherit"><?php echo $this->_helper->escape($file['lastCommit']['message']); ?></a> [<a href="#"><?php echo $this->_helper->escape($file['lastCommit']['author']); ?></a>]</td>
                            <?php else: ?>
                            <td>&nbsp; </td>
                            <td>
                                <?php if (!empty($file['realpath'])): ?>
                                <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => $file['realpath'])); ?>"><?php echo $this->_helper->escape($file['path']); ?></a>
                                <?php else: ?>
                                <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch)); ?>"><?php echo $this->_helper->escape($file['path']); ?></a>
                                <?php endif; ?>
                            </td>
                            <td>&nbsp;</td>
                            <?php endif; ?>
                            <td><?php echo $file['lastCommit']['date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if($this->readme != null): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading"><?php $i = pathinfo($this->readme, PATHINFO_FILENAME) . '.'. pathinfo($this->readme, PATHINFO_EXTENSION); echo $i; ?></div>
                            <div class="panel-body" style="padding: 5px 10px;"><?php echo $vh->embed('BlobNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => $this->readme)); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div>
        </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.commit-collapse').bind('click', function(event) {
            event.preventDefault();
            $(this).parent().toggleClass('collapsed');
            if($(this).parent().hasClass('collapsed')) {
                $(this).find('i').removeClass('glyphicon-plus').addClass('glyphicon-minus');
            } else {
                $(this).find('i').removeClass('glyphicon-minus').addClass('glyphicon-plus');
            }
        });
    });
</script>
</body>
<?php include __DIR__ .'/_footer.php'; ?> 
