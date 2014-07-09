<?php $vh = $this->_helper; ?>
<?php
$fn = $this->profile->getFullname();
$page_title = $this->profile->getUsername() . (!empty($fn) ? ' ('. $fn .')' : '');
include __DIR__ .'/_header.php';
?>
  <body>
    <?php echo $vh->embed('Menu', array('active' => 'users')); ?>

      <div class="container">
          <div class="row" style="margin-top:40px;">
            <?php $userMenuActive = "profile"; include __DIR__ .'/_user_left.php'; ?>
            <div class="col-md-8">
                <?php if(!count($this->repositories)): ?>
                    <div class="alert alert-warning">This <?php echo $this->_helper->escape($this->profile->getType()); ?> has no public repositories yet.</div>
                <?php else: ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                      <form role="form" class="pull-right filter">
                          <input type="search" class="form-control input-sm" placeholder="Filter repositories">
                      </form>
                    <h3 class="panel-title">Repositories</h3>
                  </div>
                  <div class="panel-body">
                      <ul class="repositories">
                          <?php foreach ($this->repositories as $repo): ?>
                          <li>
                              <div class="btn-group pull-right">
                                  <?php if ($this->_helper->isAllowed('repository', 'create')): ?>
                                <a href="<?php echo $this->_helper->url('ForkNEW', array('name' => $repo->getFullname())); ?>" class="btn btn-sm btn-default">Fork</a>
                                 <?php endif; ?>
                                 <?php if (($this->profile->isOrganization() && $this->_helper->isAllowed($this->profile, 'repos-admin'))
                                 || ($this->profile->isUser() && $this->_helper->isAllowed($repo, 'owner'))):?>
                                 <a href="<?php echo $this->_helper->url('DeleteNEW', array('name' => $repo->getFullname())); ?>" class="btn btn-sm btn-danger">Delete</a>
                                <?php endif; ?>
                              </div>
                              <?php if($repo->getParent_id() == null): ?><i class="octicon octicon-repo"></i><?php else: ?><i class="octicon octicon-repo-forked"></i><?php endif; ?> <?php if($repo->isPrivate()): ?><i class="octicon octicon-lock"></i><?php endif; ?> <a class="repo-name" href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $repo->getFullname())); ?>"><?php echo $this->_helper->escape($repo->getName()); ?></a> <?php if($repo->getParent_id() != null): ?><span class="fork">forked from <a class="repo-name" href="<?php echo $this->_helper->url('RepositoryNEW', array('name' => $repo->getParent()->getFullname())); ?>"><?php echo $this->_helper->escape($repo->getParent()->getFullname()); ?></a></span><?php endif; ?>
                              <p class="infos">Created on <span><?php $date = new \DateTime($repo->getCreated_at()); echo $date->format($this->dateFormat); ?></span>. <?php if ($repo->getLast_commit_date() != null): ?>Last updated on <span><?php $date = new \DateTime($repo->getLast_commit_date()); echo $date->format($this->dateFormat); ?></span>.<?php endif; ?></p>
                          </li>
                          <?php endforeach; ?>
                      </ul>
                   </div>
                </div>
                <div class="row">
                    <div class="col col-md-12">
                        <canvas id="userContrib" height="200"></canvas>
                    </div>
                </div>
                <?php endif; ?>
            </div>
          <?php include __DIR__ .'/_user_right.php'; ?>
          </div>
      </div>

    <script src="<?php echo str_replace('/index.php', '/', $vh->url()); ?>js/chartjs/Chart.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var ctx = document.getElementById("userContrib").getContext("2d");
            var myLineChart = new Chart(ctx).Line({
                labels: ["January", "February", "March", "April", "May", "June"],
                datasets: [
                    {
                        label: "My Second dataset",
                        fillColor: "rgba(151,187,205,0.2)",
                        strokeColor: "rgba(151,187,205,1)",
                        pointColor: "rgba(151,187,205,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(151,187,205,1)",
                        data: [28, 48, 40, 19, 86, 27]
                    }
                ]
            },{
                ///Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines : false,

                //String - Colour of the grid lines
                scaleGridLineColor : "rgba(0,0,0,.05)",

                //Number - Width of the grid lines
                scaleGridLineWidth : 1,

                //Boolean - Whether the line is curved between points
                bezierCurve : true,

                //Number - Tension of the bezier curve between points
                bezierCurveTension : 0.4,

                //Boolean - Whether to show a dot for each point
                pointDot : true,

                //Number - Radius of each point dot in pixels
                pointDotRadius : 4,

                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth : 0,

                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius : 20,

                //Boolean - Whether to show a stroke for datasets
                datasetStroke : false,

                //Number - Pixel width of dataset stroke
                datasetStrokeWidth : 2,

                //Boolean - Whether to fill the dataset with a colour
                datasetFill : true,
            });
        });

        function resizeCanvas() {
            var px = $('.col-md-8').width(), canvas = document.getElementById('userContrib');
            canvas.width = px;
        };

        resizeCanvas();

    </script>
  </body>
<?php include __DIR__ .'/_footer.php'; ?> 
