<?php $vh = $this->_helper; ?>
<?php $page_title = $this->entity->getFullname() ." - Create Tag"; include __DIR__ . '/../../_header.php'; ?>
<body>
    <?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="row" style="margin-top:40px;">
            <?php $repoMenuActive = "branches"; include __DIR__ . '/../_left.php'; ?>
            <div class="col-md-8">

                <h3 style="margin-top:0">Create Tag</h3>

                <?php echo $vh->form($this->addTagForm); ?>
            </div>
        </div><!-- /row -->
    </div><!-- /.container -->

</body>
<?php include __DIR__ .'/../../_footer.php'; ?>
