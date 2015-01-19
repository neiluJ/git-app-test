<?php
$vh = $this->_helper;

$page_title = $this->entity->getName() . "/" . $this->path;

if ($this->branch != $this->entity->getDefault_branch()) {
    $page_title .= " at ". $this->branch;
}

include __DIR__ . '/../_header.php';
?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "code"; include __DIR__ . '/_left.php'; ?>
        <div class="col-md-8">
            <div id="repo-commit">
                <h5 style="margin-top:0;"><i class="octicon octicon-git-commit"></i> Commit <a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->entity->getFullname(), 'hash' => $this->commit->getHash())); ?>"><?php echo $this->commit->getHash(); ?></a></h5>
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
                            <a style="color: inherit" href="<?php echo $this->_helper->url('BlobNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => rtrim($toPath, DIRECTORY_SEPARATOR))); ?>"><?php echo $this->_helper->escape($p); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div id="main">
                <div id="tc" class="mainv">
                    <?php switch($this->type) {
                        case 'display_image':
                            include __DIR__ . '/blob/display-image.php';
                            break;
                        case 'display_binary':
                            include __DIR__ . '/blob/display-binary.php';
                            break;
                        case 'display_markdown':
                            include __DIR__ . '/blob/display-markdown.php';
                            break;
                        case 'display_text':
                            include __DIR__ . '/blob/display-text.php';
                            break;
                    } ?>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <h5 style="margin-top: 0"><b class="octicon octicon-history"></b> History</h5>
            commits here

            <h5 style="margin-top: 40px;"><b class="octicon octicon-organization"></b> Contributors</h5>
            contributors

            <h5 style="margin-top: 40px;"><b class="octicon octicon-tools"></b> Tools</h5>
            <div class="btn-group btn-group-sm">
                <a class="btn btn-default btn-sm" href="<?php echo $this->_helper->url('BlobRaw', array('name' => $this->name, 'path' => $this->path, 'branch' => $this->branch), true); ?>"><b class="octicon octicon-file-text"></b> <?php echo ($this->type == "display_binary" ? "Download" : "RAW"); ?></a>
                <?php if($this->type != 'display_image' && $this->type != 'display_binary'): ?>
                    <a class="btn btn-default btn-sm" href="<?php echo $this->_helper->url('Blame', array('name' => $this->name, 'path' => $this->path, 'branch' => $this->branch), true); ?>"><b class="octicon octicon-megaphone"></b> Blame</a>
                <?php endif; ?>
            </div>

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
<?php include __DIR__ . '/../_footer.php'; ?>
