<?php
$vh = $this->_helper;

$page_title = "Blame ". $this->entity->getName() . "/" . $this->path;

$page_title .= " at ". $this->branch;

include __DIR__ . '/../_header.php';
?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "code"; include __DIR__ . '/_left.php'; ?>
        <div class="col-md-10">
            <h3 style="margin-top: 0; margin-bottom: 20px;">Blame <a href="<?php echo $this->_helper->url('BlobNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'path' => $this->path)); ?>"><?php echo $vh->escape($this->path); ?></a></h3>
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

            <?php $lines = $this->blame->getGroupedLines(); ?>
            <table class="table blame">
                <?php foreach ($lines as $info): ?>
                <tr>
                    <td class="commit">
                        <a style="float:right;" href="<?php echo $vh->url("CommitNEW", array('name' => $this->entity->getFullname(), 'hash' => $info[0]->getHash())); ?>" title="<?php echo $vh->escape($info[0]->getShortMessage()); ?>"><?php echo $info[0]->getShortHash(); ?></a>
                        <?php if (isset($this->blameCommits[$info[0]->getHash()])): ?>
                            <small><strong><?php echo $vh->escape($this->blameCommits[$info[0]->getHash()]->getComputedAuthorName()); ?></strong> on <?php echo $this->blameCommits[$info[0]->getHash()]->getAuthorDateObj()->format('d/m/Y'); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="ln">
                        <?php foreach($info[1] as $num => $line): ?>
                        <?php echo $num . "<br />"; ?>
                        <?php endforeach; ?>
                    </td>
                    <td class="code"><code><?php foreach($info[1] as $num => $line): ?><?php echo ltrim($vh->escape($line->getContent()),"\t") ."\n"; ?><?php endforeach; ?></code></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
</body>
<?php include __DIR__ . '/../_footer.php'; ?>