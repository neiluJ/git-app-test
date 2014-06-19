<div class="commit-head">
    <?php foreach ($this->currentCommit->getIncludingBranches(true, true) as $branch): ?>
        <i class="icon-sitemap"></i> <a href="<?php echo $this->_helper->url('Repository', array('name' => $this->name, 'branch' => $branch->getName()), true); ?>"><?php echo $branch->getName(); ?></a>,
    <?php endforeach; ?>
</div>
<div class="commit-infos">
    <p class="commit-txt" style="float:right; text-align:right; white-space: normal">
        <?php $parents = $this->currentCommit->getParents(); if (!count($parents)): ?>
            <strong>initial commit</strong>
        <?php elseif (count($parents) == 1): ?>
            <strong>parent</strong> <a href="<?php echo $this->_helper->url('Commit', array('name' => $this->name, 'hash' => $parents[0]->getHash()), true); ?>" ng-click="navigateToCommit($event, '<?php echo $parents[0]->getHash(); ?>')"><?php echo $parents[0]->getHash(); ?></a>
        <?php else: ?>
            <strong>parents</strong> <?php foreach($parents as $parent): ?><a ng-click="navigateToCommit($event, '<?php echo $parent->getHash(); ?>')" href="<?php echo $this->_helper->url('Commit', array('name' => $this->name, 'hash' => $parent->getHash()), true); ?>"><?php echo substr($parent->getHash(), 0, 6); ?></a>, <?php endforeach; ?>
        <?php endif; ?>
    </p>
    <p class="author"><i class="glyphicon glyphicon-user"></i> <span><?php echo $this->_helper->escape($this->currentCommit->getCommitterName()); ?></span> authored on <span class="date"> <?php echo $this->_helper->escape($this->currentCommit->getCommitterDate()->format('l F d Y H:i:s')); ?></span></p>
</div>

<h3>Diff informations</h3>

<ul class="diff-files">
    <?php $files = 0; $adds = 0; $dels = 0; ?>
    <?php 
    foreach ($this->diff->getFiles() as $file): 
        $files++;
        $adds += $file->getAdditions();
        $dels += $file->getDeletions();
?>
    <li>
        <?php if (!$file->isBinary()): ?>
            <span style="float:right" class="stats"><?php echo $file->getAdditions(); ?> additions / <?php echo $file->getDeletions(); ?> deletions</span>
        <?php endif ?>
        <?php if ($file->getAdditions() > 0 || $file->getDeletions() > 0): ?>
            <a href="#<?php echo $file->getName(); ?>"><?php echo $file->getName(); ?></a>
        <?php else: ?>
            <?php echo $file->getName(); ?>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
    <li class="stats">
        <span style="float:right"><strong><?php echo $adds; ?></strong> additions / <strong><?php echo $dels; ?></strong> deletions</span>
        <span class=""><strong><?php echo $files; ?></strong> files changed</span>
    </li>
</ul>

<?php 
    foreach ($this->diff->getFiles() as $file): 
        $changes = $file->getChanges();
        if (count($changes) == 0) {
            continue;
        }
?>
<div class="diff-head" style="clear:both;">
    <a href="<?php echo $this->_helper->url('Blob', array('name' => $this->name, 'branch' => $this->currentCommit->getHash(), 'path' => $file->getName()), true); ?>" ng-click="navigateToBlob($event, '<?php echo $file->getName(); ?>', '<?php echo $this->_helper->escape($this->currentCommit->getHash()); ?>');" class="btn btn-xs btn-default" style="float:right;">View file @<strong><?php echo substr($this->currentCommit->getHash(),0,6); ?></strong></a>
    <h4><a name="<?php echo $this->_helper->escape($file->getName()); ?>"></a> <?php echo $this->_helper->escape($file->getName()); ?></h4>
</div>

<div class="diff-body" style="clear:both;">
    <?php if (!$file->isBinary()): ?>
    <table class="difftable">
        <tbody>
            <?php foreach ($changes as $change): 
                 $idxIn = $change->getRangeOldStart()-1; 
                 $idxOut = $change->getRangeNewStart()-1; 

?>
                <tr class="infos">
                    <td class="ln">...</td>
                    <td class="ln">...</td>
                    <td>@@ -<?php echo $change->getRangeOldStart(); ?>,<?php echo $change->getRangeOldCount(); ?> +<?php echo $change->getRangeNewStart(); ?>,<?php $change->getRangeNewCount(); ?> @@ </td>
                </tr>
                <?php foreach ($change->getLines() as $line): 
                    if ($line[0] == 1) {
                        $idxOut++;
                    } elseif ($line[0] == -1) {
                        $idxIn++;
                    } else {
                        $idxIn++;
                        $idxOut++;
                    }
?>
                    <tr<?php if ($line[0] == -1): ?> class="deletion"<?php elseif ($line[0] == 1): ?> class="addition"<?php endif; ?>>
                        <td class="ln"><?php if ($line[0] == -1 || $line[0] == 0): ?><?php echo $idxIn; ?><?php endif; ?></td>
                        <td class="ln"><?php if ($line[0] == 1 || $line[0] == 0): ?><?php echo $idxOut; ?><?php endif; ?></td>
                        <td class="code"><pre><code><?php echo ($line[0] == -1 ? '-' : ($line[0] == +1 ? '+' : ' ')); ?> <?php echo htmlentities($line[1], ENT_QUOTES, "utf-8"); ?></code></pre></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <i>Binary file</i>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<h3>Comments (<?php $thId = 'commit-'. $this->entity->getId() .'-'. $this->currentCommit->getHash(); echo $this->_helper->embed('CommentsCount', array('id' => $thId)); ?>)</h3>
<?php echo $this->_helper->embed('CommentsThread', array('id' => $thId, 'type' => 'threaded')); ?>

<h4>Post a comment</h4>
<?php echo $this->_helper->embed('CommentPost', array('thread' => $thId)); ?>
