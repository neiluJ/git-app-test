<h4><a style="float:right" class="btn btn-default btn-xs" href="<?php echo $this->_helper->url('Diff', array('name' => $this->name, 'compare' => $this->compare, 'path' => $this->path), true); ?>">Raw diff</a> Differences between <strong><a href="<?php echo $this->_helper->url('Compare', array('name' => $this->name, 'compare' => $this->compare, 'path' => $this->path), true); ?>"><?php echo $this->compare; ?></a></strong></h4>

<ul class="diff-files" style="clear:both;">
    <?php $files = 0; $adds = 0; $dels = 0; ?>
    <?php 
    foreach ($this->diff->getFiles() as $file): 
        if (!empty($this->path) && strpos($file->getName(), $this->path, 0) === false) {
            continue;
        }
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

<h3>Changes</h3>
<?php 
    foreach ($this->diff->getFiles() as $file):
        if (!empty($this->path) && strpos($file->getName(), $this->path, 0) === false) {
            continue;
        }
        
        $changes = $file->getChanges();
        if (count($changes) == 0) {
            continue;
        }
?>
<div class="diff-head" style="clear:both;">
    <h4><a name="<?php echo $file->getName(); ?>"></a> <?php echo $file->getName(); ?></h4>
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
<script type="text/javascript">
$(function() {
   if (!$('.commit-collapse').parent().hasClass('collapsed')) {
       $('.commit-collapse').click(); 
   }
});
</script>