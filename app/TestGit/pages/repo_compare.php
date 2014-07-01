<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
<?php $repoMenuActive = "compare"; include __DIR__ .'/_repository_left.php'; ?>
    <div class="col-md-10">

            <h3 style="margin-top:0"><a href="#" class="pull-right btn btn-success"><b class="octicon octicon-git-pull-request"></b> Create <strong>Pull Request</strong></a> Compare <i class="mega-octicon octicon-git-compare"></i></h3>

            <div class="row compare-head">
                <div class="col-md-6">
                    <h6>Base</h6>
                    <div class="compare-bulb">
                        select repo / commit / branch
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Target</h6>
                    <div class="compare-bulb">
                        select repo / commit / branch
                    </div>
                </div>
            </div>
            <hr />
        </div>
    </div><!-- /row -->
</div><!-- /.container -->
<?php include __DIR__ .'/_footer.php'; ?>