<?php $vh = $this->_helper; ?>
<?php $page_title = $this->entity->getFullname(); include __DIR__ . '/../_header.php'; ?>
<body>
<?php echo $vh->embed('Menu', array('active' => 'repositories')); ?>

    <div class="container">
        <div class="row" style="margin-top:40px;">
            <?php $repoMenuActive = "none"; include __DIR__ . '/_left.php'; ?>
            <div class="col-md-8">
                <?php if($this->_helper->isAllowed($this->entity, 'write')): ?>
                    <div class="alert alert-info" style="margin-bottom: 20px;">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        This repository is ready to use!
                    </div>

                    <h3 style="margin-top:0;">Create a new repository</h3>

<pre>
touch README.md
git init
git add README.md
git commit -m "first commit"
git remote add origin <span class="git-clone-url"><?php echo $this->cloneSshUrl; ?></span>
git push -u origin master
</pre>

                    <h3>Push an existing repository</h3>

<pre>
git remote add origin <span class="git-clone-url"><?php echo $this->cloneSshUrl; ?></span>
git push -u origin master
</pre>

                <?php else: ?>
                    <div class="alert alert-warning">Nothing to see here...</div>
                <?php endif; ?>
            </div>
            <div class="col-md-2">
                <div class="cloneUrl"><?php echo $this->_helper->embed('CloneUrl', array('name' => $this->name)); ?></div>
            </div>
        </div><!-- /row -->
    </div><!-- /.container -->

<script type="text/javascript">
$(document).ready(function() {
   $('#gitUrl').on("change", function(event) {
       $('.git-clone-url').html($('#gitUrl').html());
   });
});
</script>    
<?php include __DIR__ . '/../_footer.php'; ?>