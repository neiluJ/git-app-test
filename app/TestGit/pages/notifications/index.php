<?php $vh = $this->_helper; include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'notifications')); ?>

<div class="container">
    <div class="row" style="margin-top:20px; text-align: center">
        <h1>Notifications</h1>
    </div>
    <div class="row" style="margin-top:40px;">
        <?php $notifMenuActive = $this->channel; include __DIR__ . '/_left.php'; ?>
        <div class="col-md-9">
            <div class="btn-group pull-right btn-group-sm" style="margin-top: -40px;">
                <a href="#" class="btn btn-default btn-sm disabled"><i class="octicon octicon-eye-unwatch"></i> Mark all as read</a>
                <a href="#" class="btn btn-default btn-sm disabled"><i class="octicon octicon-x"></i> Delete all</a>
            </div>
            <ol class="list-unstyled notifs">
                <li class="unread">
                    <span class="actions">
                        <a href="#"><i class="octicon octicon-eye-unwatch"></i></a>
                        <a href="#"><i class="octicon octicon-x"></i></a>
                    </span>
                    <b class="octicon octicon-mention"></b> <a href="#">Bidule</a> mentioned you in a <a href="#">commit</a> on <a href="#">ecommerce/ecommerce</a>: <span class="date">22 Apr, 13h15</span>
                    <p class="desc">Hey @neiluj tu peux voir ça stp?</p>
                </li>
                <li class="unread">
                    <span class="actions">
                        <a href="#"><i class="octicon octicon-eye-unwatch"></i></a>
                        <a href="#"><i class="octicon octicon-x"></i></a>
                    </span>
                    <b class="octicon octicon-git-pull-request"></b> <a href="#">Bidule</a> created <a href="#">pull request #12</a> on <a href="#">ecommerce/ecommerce</a>: <span class="date">22 Apr, 13h15</span>
                </li>
                <li class="">
                    <span class="actions">
                        <a href="#"><i class="octicon octicon-eye-unwatch"></i></a>
                        <a href="#"><i class="octicon octicon-x"></i></a>
                    </span>
                    <b class="octicon octicon-mention"></b> <a href="#">Bidule</a> mentioned you in a <a href="#">commit</a> on <a href="#">ecommerce/ecommerce</a>: <span class="date">22 Apr, 13h15</span>
                    <p class="desc">Hey @neiluj tu peux voir ça stp?</p>
                </li>
            </ol>
        </div>
</div>
<?php include __DIR__ . '/../_footer.php'; ?>
