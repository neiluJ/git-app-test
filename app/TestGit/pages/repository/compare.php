<?php $vh = $this->_helper; ?>
<?php $page_title = "Compare ". (!empty($this->compare) ? $this->compare : '') ." - ". $this->entity->getFullname(); include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "compare"; include __DIR__ . '/_left.php'; ?>
    <div class="col-md-10">

        <form action="<?php echo $this->_helper->url('CompareNEW', array('name' => $this->entity->getFullname())); ?>" method="post">
            <h3 style="margin-top:0"><button type="submit" class="pull-right btn btn-default"><strong>Compare</strong></button> Compare <i class="mega-octicon octicon-git-compare"></i></h3>

            <div class="row compare-head">
                <div class="col-md-5">
                    <h6>Base</h6>
                    <div class="compare-bulb">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-repo"></b></div>
                                <select class="form-control" name="base" readonly="readonly">
                                    <option value="<?php echo $this->entity->getOwner()->getUsername(); ?>" selected="selected"><?php echo $vh->escape($this->entity->getFullname()); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-git-branch" id="baseIcon"></b></div>
                                <input type="text" name="baseRef" style="width: 330px" class="baseRef form-control" value="<?php echo $vh->escape($this->baseRef); ?>" placeholder="Branch, Tag or Commit" />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-md-2" style="text-align: center; color: #ddd; padding-top: 40px;">
                    <span class="mega-octicon octicon-ellipsis" style=""></span>
                </div>
                <div class="col-md-5">
                    <h6>Target</h6>
                    <div class="compare-bulb">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-repo"></b></div>
                                <select class="form-control" name="target">
                                    <?php foreach ($this->targets as $target): ?>
                                        <option value="<?php echo $target->getOwner()->getUsername(); ?>"<?php if($this->target == $target->getOwner()->getUsername()): ?> selected="selected"<?php endif; ?>><?php echo $vh->escape($target->getFullname()); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><b class="octicon octicon-git-branch"></b></div>
                                <input type="text" name="targetRef"  value="<?php echo $vh->escape($this->targetRef); ?>" class="targetRef form-control" placeholder="Branch, Tag or Commit" />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </form>
        <hr />

        <?php if($vh->isAllowed($this->entity, 'write') && $this->target == $this->entity->getOwner()->getUsername() && $this->baseRef != $this->targetRef): ?>
        <div class="alert alert-info" role="alert" style="margin-top: 20px;">
            <button data-toggle="modal" data-target="#mergeModal" class="pull-right btn btn-success btn-lg"><b class="octicon octicon-git-merge"></b> Merge</button>
            <b>Heads up!</b><br />Merge this comparision onto your master branch !
        </div>
        <hr />
        <?php endif; ?>

        <?php if (!empty($this->errorMsg)): ?>
            <div class="alert alert-warning" role="alert" style="margin-top: 20px;">
                <?php echo $vh->escape($this->errorMsg); ?>
            </div>
        <?php endif; ?>

<?php if ($this->diff instanceof \Gitonomy\Git\Diff\Diff): ?>
        <ul class="nav nav-tabs" role="tablist" style="margin-top:20px;">
            <li class="active"><a href="#changes" role="tab" data-toggle="tab">Changes</a></li>
    <?php if (count($this->commits)): ?>
            <li class=""><a href="#commits" role="tab" data-toggle="tab">Commits (<?php echo count($this->commits); ?>)</a></li>
    <?php endif; ?>
    <?php if (count($this->diff->getFiles())): ?>
            <li><a href="#diff" role="tab" data-toggle="tab">Diff</a></li>
    <?php endif; ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="changes">
                <?php if (count($this->diff->getFiles())): ?>
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
        <?php else: ?>
        <div class="alert alert-warning" role="alert" style="margin-top: 20px;">
            <?php
                list($base, $target) = explode('..', $this->compare);
                $inverse = $target .'..'. $base;
            ?>
            No changes to display. <?php if ($inverse != $this->compare): ?>Try the <b><a href="<?php echo $vh->url('CompareNEW', array('name' => $this->entity->getFullname(), 'compare' => $inverse)); ?>">inverse comparision</a></b>.<?php endif; ?>
        </div>
        <?php endif; ?>
            </div>
            <?php if (count($this->commits)): ?>
            <div class="tab-pane" id="commits">
<?php
$finalCommits = array();
foreach ($this->commits as $commit) {
    $day = $commit->getCommitterDate()->format('Ymd');

    if (!isset($finalCommits[$day])) {
        $finalCommits[$day] = array();
    }

    array_push($finalCommits[$day], $commit);
}
?>
                <ul class="commits-history">
                <?php foreach ($finalCommits as $day => $commits): ?>
                    <li class="date">
                        <p><b class="octicon octicon-calendar"></b> <?php $commit = $commits[0]; echo $commit->getCommitterDate()->format('l F d Y'); ?></p>
                        <table>
                            <tbody>
                            <?php foreach ($commits as $commit): ?>
                            <tr>
                                <td style="width:120px;">
                                    <i class="octicon octicon-git-commit"></i> <a href="<?php echo $vh->url('CommitNEW', array('name' => $this->entity->getFullname(), 'hash' => $commit->getHash())); ?>"><?php echo substr($commit->getHash(), 0, 6); ?></a>
                                    <?php $thId = 'commit-'. $this->entity->getId() .'-'. $commit->getHash(); $comments = $this->_helper->embed('CommentsCount', array('id' => $thId)); if ($comments > 0): ?>
                                    <?php echo $comments; ?> <b class="octicon octicon-comment"></b>
                                    <?php endif; ?>
                                </td>
                                <td  style="width:150px;"><?php echo $vh->escape($commit->getAuthorName()); ?></td>
                                <td style="display:block"><span class="commit-txt"><?php echo $vh->escape($commit->getMessage()); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </li>

                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if(count($this->diff->getFiles())): ?>
            <div class="tab-pane" id="diff">
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
            </div>
        <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div><!-- /row -->
</div><!-- /.container -->

<div class="modal" id="mergeModal">
    <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Merge <?php echo $vh->escape($this->targetRef); ?> to <?php echo $vh->escape($this->baseRef); ?></h4>
                </div>
                <div class="modal-body" id="mergeContents">
                    <div class="alert" id="mergeAlert" role="alert"></div>
                    <h5>Merge output:</h5>
                    <pre id="mergeOutput"></pre>
                </div>
                <div class="modal-footer">
                    <form action="<?php echo $vh->url('Merge', array('name' => $this->entity->getFullname(), 'compare' => $this->compare)); ?>" method="post">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="mergeBtn" disabled="disabled">Apply Merge</button>
                    </form>
                </div>
            </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    $(function() {
        $('#mergeModal').on('shown.bs.modal', function (e) {
            var url = "<?php echo $vh->url('Merge', array('name' => $this->entity->getFullname(), 'compare' => $this->compare)); ?>";
            $('#mergeOutput').html("");
            $('#mergeAlert').removeClass('alert-error').removeClass('alert-success').html('');
            $('#mergeBtn').attr("disabled", "disabled");
            $.ajax({
                url: url,
                async: true,
                dataType: "json",
                error: function(xhr, text, err) {
                    $('#mergeOutput').html(err);
                    $('#mergeAlert').addClass('alert-danger').html('An error occured: '+ text);
                    $('#mergeBtn').attr("disabled", "disabled");
                },
                success: function(data) {
                    if (data.mergeSuccess === undefined) {
                        $('#mergeOutput').html("");
                        $('#mergeAlert').addClass('alert-danger').html('Bogus response from server');
                        $('#mergeBtn').attr("disabled", "disabled");
                        return;
                    }
                    if (data.mergeSuccess === false) {
                        $('#mergeOutput').html(data.mergeMsg);
                        $('#mergeAlert').addClass('alert-warning').html('This merge <b>CAN\'T</b> be completed automatically.<br /> Use your repository to perform it and push the changes.');
                        $('#mergeBtn').attr("disabled", "disabled");
                    } else {
                        $('#mergeOutput').html(data.mergeMsg);
                        $('#mergeAlert').addClass('alert-success').html('These changes can be merged automatically !');
                        $('#mergeBtn').attr("disabled", false);
                    }
                }
            });
        })
    });

    <?php
        $tags = array();
        $branches = array();
        foreach ($this->entity->getReferences() as $ref) { if (!$ref->isBranch()) { $tags[] = $ref->getName(); } else { $branches[] = $ref->getName(); } }
    ?>

    var branches = ["<?php echo implode('", "', $branches); ?>"], tags = ["<?php echo implode('", "', $tags); ?>"], refreshIcon = function(dataset, elm) {
        if (dataset.indexOf("branches") !== -1) {
            elm.removeClass("octicon-git-branch")
                .removeClass("octicon-tag")
                .removeClass("octicon-git-commit")
                .addClass("octicon-git-branch");
        } else if (dataset.indexOf("tags") !== -1) {
            elm.removeClass("octicon-git-branch")
                .removeClass("octicon-tag")
                .removeClass("octicon-git-commit")
                .addClass("octicon-tag");
        } else {
            elm.removeClass("octicon-git-branch")
                .removeClass("octicon-tag")
                .removeClass("octicon-git-commit")
                .addClass("octicon-git-commit");
        }
    };

    $(document).ready(function() {
        $('input.baseRef').typeahead([
            {
                name: 'branchesbase',
                local: branches,
                template: [
                    '<p class="repo-name"><i class="octicon octicon-git-branch"></i> {{value}}</p>'
                ].join(''),
                engine: Hogan,
                limit:0,
                header: '<span class="dropdown-header">BRANCHES</span>'
            },
            {
                name: 'tagsbase',
                local: tags,
                template: [
                    '<p class="repo-name"><i class="octicon octicon-tag"></i> {{value}}</p>'
                ].join(''),
                engine: Hogan,
                limit:0,
                header: '<span class="dropdown-header">TAGS</span>'
            },
            {
                name: 'commitsbase',
                remote: {cache: false, url: "<?php echo $this->_helper->url('SearchCommits'); ?>?repo=<?php echo $this->entity->getFullname(); ?>&q=%QUERY", ttl: 5, filter: function(obj) { return obj.searchResults; }},
                template: [
                    '<p class="commit-date">{{date}}</p>',
                    '<p class="repo-name"><i class="octicon octicon-git-commit"></i> {{shortHash}}</p>',
                    '<p class="commit-details"><strong>{{repoName}}</strong> by <strong>{{committer}}</strong></p>',
                    '<p class="commit-txt">{{message}}</p>',
                ].join(''),
                engine: Hogan,
                valueKey: "name",
                limit:3,
                header: '<span class="dropdown-header">COMMITS</span>'
            }
        ]).bind('typeahead:autocompleted', function (obj, datum, dataset) {
            refreshIcon(dataset, $('#baseIcon'));
        }).bind('typeahead:selected', function (obj, datum, dataset) {
            refreshIcon(dataset, $('#baseIcon'));
        });
    });
</script>
<?php include __DIR__ . '/../_footer.php'; ?>