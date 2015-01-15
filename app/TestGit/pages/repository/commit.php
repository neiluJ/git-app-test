<?php $vh = $this->_helper; ?>
<?php $page_title = $this->commit->getShortMessage() ." - ". substr($this->commit->getHash(), 0, 8) . " - ". $this->entity->getFullname(); include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "commits"; include __DIR__ . '/_left.php'; ?>
        <div class="col-md-10">
            <div id="repo-commit">
                <h5 style="margin-top:0;">
                    <?php $thId = 'commit-'. $this->entity->getId() .'-'. $this->commit->getHash(); $comments = $this->_helper->embed('CommentsCount', array('id' => $thId)); if ($comments > 0): ?>
                        <span class="pull-right"><?php echo $comments; ?> <b class="octicon octicon-comment"></b></span>
                    <?php endif; ?>
                    <i class="octicon octicon-git-commit"></i> Commit <a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->entity->getFullname(), 'hash' => $this->commit->getHash())); ?>"><?php echo $this->commit->getHash(); ?></a>
                </h5>
                <p class="commit-infos commit-txt" style="height: auto"><?php echo $this->_helper->escape($this->commit->getMessage()); ?></p>
                <hr style="margin:10px 0;" />
            </div>
            <div class="commit-head">
                <?php foreach ($this->currentCommit->getIncludingBranches(true, true) as $branch): ?>
                    <i class="octicon octicon-git-branch"></i> <a href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $this->name, 'branch' => $branch->getName()), true); ?>"><?php echo $branch->getName(); ?></a>,
                <?php endforeach; ?>
            </div>
            <div class="commit-infos">
                <p class="commit-txt" style="float:right; text-align:right; white-space: normal">
                    <?php $parents = $this->currentCommit->getParents(); if (!count($parents)): ?>
                        <strong>initial commit</strong>
                    <?php elseif (count($parents) == 1): ?>
                        <strong>parent</strong> <a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->name, 'hash' => $parents[0]->getHash()), true); ?>"><?php echo $parents[0]->getHash(); ?></a>
                    <?php else: ?>
                        <strong>parents</strong> <?php foreach($parents as $parent): ?><a href="<?php echo $this->_helper->url('CommitNEW', array('name' => $this->name, 'hash' => $parent->getHash()), true); ?>"><?php echo substr($parent->getHash(), 0, 6); ?></a>, <?php endforeach; ?>
                    <?php endif; ?>
                </p>
                <p class="author"><i class="glyphicon glyphicon-user"></i> <span><?php echo $this->_helper->escape($this->currentCommit->getCommitterName()); ?></span> authored on <span class="date"> <?php echo $this->_helper->escape($this->currentCommit->getCommitterDate()->format('l F d Y H:i:s')); ?></span></p>
            </div>


            <ul class="nav nav-tabs" role="tablist" style="margin-top:20px;">
                <li class="active"><a href="#infos" role="tab" data-toggle="tab">Informations</a></li>
                <li><a href="#diff" role="tab" data-toggle="tab">Diff</a></li>
                <li><a href="#comments" role="tab" data-toggle="tab">Comments (<?php $thId = 'commit-'. $this->entity->getId() .'-'. $this->currentCommit->getHash(); echo $this->_helper->embed('CommentsCount', array('id' => $thId)); ?>)</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="infos">
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
                </div>
                <div class="tab-pane" id="diff">
                    <?php
                    foreach ($this->diff->getFiles() as $file):
                        $changes = $file->getChanges();
                        if (count($changes) == 0) {
                            continue;
                        }
                        ?>
                        <div class="diff-head" style="clear:both;">
                            <a href="<?php echo $this->_helper->url('BlobNEW', array('name' => $this->name, 'branch' => $this->currentCommit->getHash(), 'path' => $file->getName()), true); ?>" class="btn btn-xs btn-default" style="float:right;">View file @<strong><?php echo substr($this->currentCommit->getHash(),0,6); ?></strong></a>
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
                </div>
                <div class="tab-pane" id="comments">
                    <h3>Comments</h3>
                    <?php echo $this->_helper->embed('CommentsThread', array('id' => $thId, 'type' => 'threaded')); ?>

                    <h4>Post a comment</h4>
                    <?php echo $this->_helper->embed('CommentPost', array('thread' => $thId)); ?>
                </div>
            </div>
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ . '/../_footer.php'; ?>