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
                   <span style="color: #ccc;">January, 2014</span></h3>

                <p>There was <strong><?php echo count($this->commits); ?> commits</strong> in <span
                        class="octicon
                octicon-git-branch"></span> <strong><?php echo $vh->escape($this->branch); ?></strong> on
                    <strong>January 2014</strong> (1 year ago) by <strong>3 contributors</strong>.</p>

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
                <?php else: ?>
                    <p>They are no commits to show.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3">
            <p class="user-stat"><span class="big-counter"><?php echo count
                    ($this->commits); ?></span> total
                commits</p>

            <div class="panel-group" id="accordion" style="margin-top: 20px;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                2014
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body calendar">
                            <ul>
                                <li>
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Jan</span>
                                    </a>
                                </li>
                                <li class="mid-l">
                                    <a href="#">
                                        <span class="counter">202</span>
                                        <span class="cal-month">Feb</span>
                                    </a>
                                </li>
                                <li class="mid-r">
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Mar</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="counter">2000</span>
                                        <span class="cal-month">Apr</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">May</span>
                                    </a>
                                </li>
                                <li class="mid-l">
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Jun</span>
                                    </a>
                                </li>
                                <li class="mid-r">
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Jul</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Aug</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Sep</span>
                                    </a>
                                </li>
                                <li class="mid-l">
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Oct</span>
                                    </a>
                                </li>
                                <li class="mid-r">
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Nov</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="counter">2</span>
                                        <span class="cal-month">Dec</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                2013
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                            pwet
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                2012
                            </a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse">
                        <div class="panel-body">
                            pwet
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php include __DIR__ . '/../_footer.php'; ?>
