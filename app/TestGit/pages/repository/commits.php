<?php $vh = $this->_helper; ?>
<?php
    if (empty($this->path)) {
        $page_title = $this->entity->getFullname();
    } else {
        $page_title = $this->entity->getName() . "/" . $this->path;
    }

    $page_title .= " - Commits History";

    include __DIR__ . '/../_header.php';
?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "commits"; include __DIR__ . '/_left.php'; ?>
        <div class="col-md-7">
            <div id="main">
               <h3 style="margin-top:0"><span class="mega-octicon octicon-git-commit"></span> Commits History
                   <span style="color: #ccc;"><?php echo date('F, Y', strtotime($this->year ."-". $this->month ."-01")); ?></span></h3>

                <p>There was <strong><?php echo count($this->commits); ?> commits</strong> in <span
                        class="octicon
                octicon-git-branch"></span> <strong><?php echo $vh->escape($this->branch); ?></strong> on
                    <strong><?php echo date('F Y', strtotime($this->year ."-". $this->month ."-01")); ?></strong>.</p>

                <?php if (count($this->commits)): ?>
                    <div class="tab-pane" id="commits">
                        <?php
                        $finalCommits = array();
                        foreach ($this->commits as $commit) {
                            $date = new DateTime($commit->getCommitterDate());
                            $day = $date->format('Ymd');

                            if (!isset($finalCommits[$day])) {
                                $finalCommits[$day] = array();
                            }

                            array_push($finalCommits[$day], $commit);
                        }
                        ?>
                        <ul class="commits-history">
                            <?php foreach ($finalCommits as $day => $commits): ?>
                                <li class="date">
                                    <p><b class="octicon octicon-calendar"></b> <?php $commit = $commits[0]; $date = new DateTime($commit->getCommitterDate()); echo $date->format('l d'); ?></p>
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
                <?php else: ?>
                    <div class="alert alert-info" style="margin-top: 20px;">
                        They are no commits to show.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3">
            <p class="user-stat"><span class="big-counter"><?php echo $this->totalCommits; ?></span> total commits</p>

            <div class="panel-group" id="accordion" style="margin-top: 20px;">
                <?php $year = date('Y'); $month = date('m'); ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $year; ?>">
                                <?php echo $year; ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?php echo $year; ?>" class="panel-collapse collapse<?php if($year == $this->year): ?> in<?php endif; ?>">
                        <div class="panel-body calendar">
                            <ul>
                                <?php for($x = 1; $x <= 12; $x++): ?>
                                <li class="<?php if($x == $this->month && $year == $this->year): ?>active<?php endif; ?><?php if(in_array($x, array(2,6,10))): ?> mid-l<?php elseif(in_array($x, array(3,7,11))): ?> mid-r<?php endif; ?>">
                                    <a href="<?php echo $vh->url('CommitsNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'year' => $year, 'month' => str_pad($x, 2, '0', STR_PAD_LEFT))); ?>"<?php if(!isset($this->monthlyCount[$year][$x])): ?> class="empty"<?php endif; ?>>
                                        <span class="counter">
                                            <?php echo (isset($this->monthlyCount[$year][$x]) ? $this->monthlyCount[$year][$x] : 0); ?>
                                        </span>
                                        <span class="cal-month">
                                            <?php echo date('M', strtotime("2014-". $x ."-01")); ?>
                                        </span>
                                    </a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php foreach ($this->monthlyCount as $y => $data): if($year == $y) { continue; } ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $y; ?>">
                                <?php echo $y; ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?php echo $y; ?>" class="panel-collapse collapse<?php if($y == $this->year): ?> in<?php endif; ?>"">
                        <div class="panel-body calendar">
                            <ul>
                                <?php for($x = 1; $x <= 12; $x++): ?>
                                    <li class="<?php if($x == $this->month && $y == $this->year): ?>active<?php endif; ?><?php if(in_array($x, array(2,6,10))): ?> mid-l<?php elseif(in_array($x, array(3,7,11))): ?> mid-r<?php endif; ?>">
                                        <a href="<?php echo $vh->url('CommitsNEW', array('name' => $this->entity->getFullname(), 'branch' => $this->branch, 'year' => $y, 'month' => str_pad($x, 2, '0', STR_PAD_LEFT))); ?>"<?php if(!isset($data[$x])): ?> class="empty"<?php endif; ?>>
                                    <span class="counter">
                                        <?php echo (isset($data[$x]) ? $data[$x] : 0); ?>
                                    </span>
                                    <span class="cal-month">
                                        <?php echo date('M', strtotime("2014-". $x ."-01")); ?>
                                    </span>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
</div>
<?php include __DIR__ . '/../_footer.php'; ?>
