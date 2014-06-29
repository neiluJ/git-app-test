<?php $vh = $this->_helper; ?>
<?php include __DIR__ .'/_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

<div class="container">
    <div class="row" style="margin-top:40px;">
        <?php $repoMenuActive = "code"; include __DIR__ .'/_repository_left.php'; ?>
        <div class="col-md-8">
            MILIEU
        </div>
    </div>
</div>

</body>
<?php include __DIR__ .'/_footer.php'; ?> 
